<?php

namespace Controllers;

use Model\Proyecto;
use MVC\Router;

class DashboardController{
    public static function index(Router $router){
        session_start();
        isAuth();

        $id = $_SESSION["id"];
        $proyectos = Proyecto::belongsTo("propietarioid",$id);
        
        $router->render("dashboard/index",[
            "titulo"=>"Proyectos",
            "proyectos"=>$proyectos
        ]);
    }
    public static function crear_proyecto(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            $proyecto = new Proyecto($_POST);
            //Validación
            $alertas = $proyecto->validarProyecto();
            if(empty($alertas)){
                //Generar una url única
                $hash = md5(uniqid());
                $proyecto->url = $hash;
                //Almacenar el creador del proyecto
                $proyecto->propietarioId=$_SESSION["id"];
                $resultado = $proyecto->guardar();
                if($resultado){
                    header("Location: /proyecto?id=".$proyecto->url);
                }
            }
        }
        $router->render("dashboard/crear-proyecto",[
            "titulo"=>"Crear Proyecto",
            "alertas"=>$alertas
        ]);
    }
    public static function proyecto(Router $router){
        session_start();
        isAuth();
        
        $token = $_GET["id"];
        if(!$token)
            header("Location: /dashboard");
        //Revisar que la persona que visita el proyecto, es quien lo creo
        $proyecto = Proyecto::where("url",$token);
        if($proyecto->propietarioId !== $_SESSION["id"]){
            header("Location: /dashboard");
        }

        $alertas = [];
        if($_SERVER["REQUEST_METHOD"]==="POST"){
           
        }
        $router->render("dashboard/proyecto",[
            "titulo"=>$proyecto->proyecto,
            "alertas"=>$alertas
        ]);
    }
    public static function perfil(Router $router){
        session_start();
        isAuth();
        $router->render("dashboard/perfil",[
            "titulo"=>"Perfil"
        ]);
    }
}