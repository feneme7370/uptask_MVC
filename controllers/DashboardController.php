<?php 
namespace Controllers;

use MVC\Router;
use Model\Proyecto;
use Model\Usuario;

class DashboardController{
    public static function index(Router $router){
        session_start();
        isAuth();

        $proyectos = Proyecto::belongTo('propietarioId', $_SESSION['id']);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }
    public static function crear_proyecto(Router $router){
        session_start();
        isAuth();
        
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);

            //validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //generar url unica
                $hash = md5( uniqid() );
                $proyecto->url = $hash;

                //almacenar creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                
                //guardar proyecto
                $proyecto->guardar();

                //redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyectos', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();

        $token = s($_GET['id']);
        if(!$token){ header('Location: /dashboard');};
        //comprobar propietario del proyecto
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']){ 
            header('Location: /dashboard');};

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto        
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPerfil();

            if(empty($alertas)){
                //verificar que el email no existe
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    //mostrar error
                    Usuario::setAlerta('error', 'el email ya existe');
                    $alertas = Usuario::getAlertas();
                }else{
                    //guardar el usuario
                    $usuario->guardar();
    
                    Usuario::setAlerta('exito', 'guardado correctamente');
                    $alertas = $usuario->getAlertas();
    
                    //asignar nuevo nombre a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        };

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();
        
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            //sincronizar con datos enviados
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();
            //debuguear($usuario);

            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();

                if($resultado){
                    //asignar nuevo password
                    $usuario->password = $usuario->password_nuevo;

                    //eliminar password que no corresponden del objeto
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    unset($usuario->password2);

                    //hashear nuevo password
                    $usuario->hashPassword();

                    //actualizar password
                    $resultado = $usuario->guardar();

                    if($resultado){
                        //alerta de error
                        Usuario::setAlerta('exito', 'password guardado correctamente');
                        $alertas = $usuario->getAlertas();    
                    }
                }else{
                    //alerta de error
                    Usuario::setAlerta('error', 'el password es incorrecto');
                    $alertas = $usuario->getAlertas();
                }

            }
        };
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'cambiar password',
            'alertas' => $alertas
            
        ]);
    }
}
?>