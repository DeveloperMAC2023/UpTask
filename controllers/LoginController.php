<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController{

    public static function login(Router $router){         
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if (empty($alertas)){
                $usuario = Usuario::where('email', $usuario->email);
                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                } else {
                    if (password_verify($_POST['password'], $usuario->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        header('Location: /proyectos');
                    } else {
                        Usuario::setAlerta('error','Password incorrecto');
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        echo 'desde logout';
    }

    public static function crear(Router $router){        
        $usuario = new Usuario;
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email','$usuario->email');
                if ($existeUsuario) {
                    Usuario::setAlerta('error','El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear password
                    $usuario->hashPassword();
                    // Eliminar password 2
                    unset($usuario->password2);
                    // Generar Token
                    $usuario->crearToken();
                    // Crear usuario
                    $resultado = $usuario->guardar();
                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }    
            }
        }
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);

    }

    public static function olvide(Router $router){        
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            if (empty($alertas)) {
                $usuario = Usuario::where('email',$usuario->email);
                if ($usuario && $usuario->confirmado === "1") {
                    $usuario->crearToken();
                    unset($usuario->password2);
                    $usuario->guardar();
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    Usuario::setAlerta('exito','Hemos enviado las instrucciones a tu email');
                } else {
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');                    
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){        
        $token = s($_GET['token']);
        $mostrar = true;
        if (!$token) {
            header('Location: /');
        }
        $usuario = Usuario::where('token', $token);
        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();
            if (empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    public static function confirmar(Router $router){
        $token = s($_GET['token']);
        if (!$token) {
            header('Location: /');
        }
        $usuario = Usuario::where('token',$token);
        if (empty($usuario)) {
            Usuario::setAlerta('error','Token no válido');
        } else {
            $usuario->confirmado =1;
            $usuario->token = '';
            unset($usuario->password2);
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta Comprobada Correctamente');
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta UpTask',
            'alertas' => $alertas
        ]);
    }

}