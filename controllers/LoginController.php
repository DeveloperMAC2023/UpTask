<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController{

    public static function login(Router $router){         
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

        }
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión'
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
        }
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
        }
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password'
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    public static function confirmar(Router $router){
        $token = s($_GET['token']);
        if (!token) {
            header('Location: /');
        }
        $usuario = Usuario::where('token',$token);
        if (empty($usuario)) {
            Usuario::setAlerta('error','Token no válido');
        } else {
            $usuario->confirmado =1;
            $usuario->token = null;
            unset($usuario->password2);
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta Comprada Correctamente');
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta UpTask',
            'alertas' => $alertas
        ]);
    }

}