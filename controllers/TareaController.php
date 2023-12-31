<?php

namespace Controllers;

use MVC\Router;
use Model\Tarea;
use Model\Proyecto;

class TareaController{
    public static function index(){
        session_start();
        $url = $_GET["id"];
        if(!$url) header("Location: /dashboard");
        $proyecto = Proyecto::where("url",$url);
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"])
            header("Location: /404");
        $tareas = Tarea::belongsTo("proyectoId",$proyecto->id);
        echo json_encode([
            "tareas" => $tareas
        ]);
    }
    public static function crear(){
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            session_start();
            $proyectoId = $_POST["proyectoId"];
            $proyecto = Proyecto::where("url",$proyectoId);
            if(!$proyecto || $proyecto->propietarioId!==$_SESSION["id"]){
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un error al agregar la tarea"
                ];
                echo json_encode($respuesta);
                return;
            }
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                "tipo" => "exito",
                "id" => $resultado["id"],
                "mensaje" => "Tarea agregada correctamente",
                "proyectoId" => $proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }
    public static function actualizar(){
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            session_start();
            $proyecto = Proyecto::where("url",$_POST["proyectoId"]);
            if(!$proyecto || $proyecto->propietarioId!==$_SESSION["id"]){
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un error al agregar la tarea"
                ];
                echo json_encode($respuesta);
                return;
            }
            $tarea = new Tarea($_POST);
            $tarea->proyectoId=$proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                "tipo" => "exito",
                "id" => $resultado["id"],
                "mensaje" => "Actualizado correctamente",
                "proyectoId" => $proyecto->id
            ];
            echo json_encode(["respuesta" => $respuesta]);
        }
    }
    public static function eliminar(){
        if($_SERVER["REQUEST_METHOD"]==="POST"){
            session_start();
            $proyecto = Proyecto::where("url",$_POST["proyectoId"]);
            if(!$proyecto || $proyecto->propietarioId!==$_SESSION["id"]){
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un error al agregar la tarea"
                ];
                echo json_encode($respuesta);
                return;
            }
            $tarea = new Tarea($_POST);
            $tarea->proyectoId=$proyecto->id;
            $resultado = $tarea->eliminar();
            $respuesta = [
                "tipo" => "exito",
                "id" => $resultado["id"],
                "mensaje" => "Eliminado correctamente",
                "proyectoId" => $proyecto->id
            ];
            echo json_encode(["respuesta" => $respuesta]);
        }
    }
}