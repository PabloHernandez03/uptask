<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController{
    public static function login(Router $router){
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if(empty($alertas)){
                $usuario = Usuario::where("email",$usuario->email);
                if(!$usuario){
                    Usuario::setAlerta("error","El usuario no existe");
                }else if($usuario->confirmado==="0"){
                    Usuario::setAlerta("error","El usuario no esta confirmado");
                    $alertas = Usuario::getAlertas();
                }else{
                    //El usuario existe
                    if(password_verify($_POST["password"],$usuario->password)){
                        session_start();
                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;
                        header("Location: /dashboard");
                    }else{
                        Usuario::setAlerta("error","Contraseña incorrecta");
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/login",[
            "titulo"=>"Iniciar Sesión",
            "alertas"=>$alertas
        ]);
    }
    public static function logout(){
        session_start();
        $_SESSION=[];
        header("Location: /");
    }
    public static function crear(Router $router){
        $usuario = new Usuario;
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            if(empty($alertas)){
                $existeUsuario = Usuario::where("email",$usuario->email);
                if($existeUsuario){
                    Usuario::setAlerta("error","El usuario ya esta registrado");
                    $alertas = Usuario::getAlertas();
                }else{
                    //Hashear contraseña
                    $usuario->hashPassword();
                    //Eliminar password2
                    unset($usuario->password2);
                    //Token
                    $usuario->crearToken();
                    //Guardar
                    $resultado = $usuario->guardar();
                    
                    if($resultado){
                        //Enviar email
                        $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                        $email->enviarConfirmacion();
                        header("Location: /mensaje");
                    }
                }
            }
        }
        $router->render("auth/crear",[
            "titulo"=>"Crea tu cuenta",
            "usuario"=>$usuario,
            "alertas"=>$alertas
        ]);
    }
    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            $usuario=new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where("email",$usuario->email);
                if(!$usuario){
                    Usuario::setAlerta("error","El usuario no existe");
                    $alertas = Usuario::getAlertas();
                }else if($usuario->confirmado==="0"){
                    Usuario::setAlerta("error","El usuario no esta confirmado");
                    $alertas = Usuario::getAlertas();
                }else{
                    //Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    //Actualizar el usuario
                    $usuario->guardar();
                    //Enviar el email
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();
                    //Imprimir la alerta
                    Usuario::setAlerta("exito","Hemos enviado las instrucciones a tu email");
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/olvide",[
            "titulo"=>"Olvide mi contraseña",
            "alertas"=>$alertas
        ]);
    }
    public static function reestablecer(Router $router){
        $token = s($_GET["token"]);
        $mostrar = true;
        if(!$token){
            header("Location: /");
        }
        $usuario = Usuario::where("token",$token);
        if(empty($usuario)){
            Usuario::setAlerta("error","Token no válido");
            $mostrar=false;
        }
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            //Añadir el nuevo password
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();
            if(empty($alertas)){
                $usuario->hashPassword();
                unset($usuario->password2);
                $usuario->token=null;
                $resultado = $usuario->guardar();
                if($resultado){
                    header("Location: /");
                }
            }
        }
        
        $alertas = Usuario::getAlertas();
        $router->render("auth/reestablecer",[
            "titulo"=>"Reestablecer Password",
            "alertas" => $alertas,
            "mostrar"=>$mostrar
        ]);
    }
    public static function mensaje(Router $router){
        $router->render("auth/mensaje",[
            "titulo"=>"Cuentra Creada Exitosamente"
        ]);
    }
    public static function confirmar(Router $router){
        $token = s($_GET["token"]);
        if(!$token){
            header("Location: /");
        }

        $usuario = Usuario::where("token",$token);

        if(empty($usuario)){
            Usuario::setAlerta("error","Token no válido");
        }else{
            //Confirmar la cuenta
            $usuario->confirmado=1;
            unset($usuario->password2);
            $usuario->token=null;
            //Actualizar en la base de daatos
            $usuario->guardar();
            Usuario::setAlerta("exito","Cuenta comprobada correctamente");
        }
        $alertas = Usuario::getAlertas();

        $router->render("auth/confirmar",[
            "titulo"=>"Confirma tu Cuenta",
            "alertas"=>$alertas
        ]);
    }
}