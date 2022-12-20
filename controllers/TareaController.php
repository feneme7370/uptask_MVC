<?php 
namespace Controllers;

use Model\Proyecto;
use MVC\Router;
use Model\Tarea;

class TareaController{
    public static function index(){
        session_start();
        //tomar url del proyecto
        $proyectoId = $_GET['id'];
        //redireccionar
        if(!$proyectoId) {header ('Location: /dashboard');};

        //traer datos del proyecto
        $proyecto = Proyecto::where('url', $proyectoId);

        //validar
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {header ('Location: /dashboard');};
        
        //tareas de este proyecto, belongTo trae array con todos los registros
        $tareas = Tarea::belongTo('proyectoId', $proyecto->id);
        
        //mostrar json para api
        echo json_encode(['tareas' => $tareas]);
    }
    public static function crear(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            //proyectoId es la url, la uso para traer los datos de todo el proyecto
            $proyectoId = $_POST['proyectoId'];
            $proyecto = Proyecto::where('url', $proyectoId);
            
            //verificio que exista la url del proyecto y sea de la persona correcta
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Error al agregar tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            
            //si esta bien, instaciar y crear tarea con el post de fetch y el id de arriba
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();

            $respuesta = [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Tarea creado correctamente',
                'proyectoId' => $proyecto->id
            ];
            echo json_encode($respuesta);
            
        };
    }
    public static function actualizar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            //validar que el proyecto existe
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            //verificio que exista la url del proyecto y sea de la persona correcta
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Error al actualizar tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            //creo objeto y reemplazo el que tiene url por el id
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            
            $resultado = $tarea->guardar();
            
            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje' => 'Tarea actualizada correctamente'
                ];
                echo json_encode(['respuesta' => $respuesta]);
            }
        };
    }
    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            //validar que el proyecto existe
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            //verificio que exista la url del proyecto y sea de la persona correcta
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Error al eliminar tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            //creo objeto y reemplazo el que tiene url por el id
            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();
            
                $resultado = [
                    'resultado' => $resultado,
                    'mensaje' => 'Tarea eliminada correctamente',
                    'tipo' => 'exito'
                ];
                echo json_encode($resultado);
        };
    }

}
?>