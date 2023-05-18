<?php

namespace Controllers;

use MVC\Router;

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
        }
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask'
        ]);

    }

    public static function olvide(){        
        echo 'desde olvide';
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
        }
    }

    public static function reestablecer(){        
        echo 'desde reestablecer';
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
        }
    }

    public static function mensaje(){
        echo 'desde mensaje';
    }

    public static function confirmar(){
        echo 'desde confirmar';
    }

}