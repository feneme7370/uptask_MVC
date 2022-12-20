<?php 
namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController{
    public static function login(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'el usuario no existe o no esta confirmado');
                }else{
                    //el usuario existe
                    if( password_verify($auth->password, $usuario->password)){
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        header('Location: /dashboard');
                    }else{
                        Usuario::setAlerta('error', 'el password es incorrecto');

                    }
                }
            }
        };

        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'titulo' => 'login',
            'alertas' => $alertas
        ]);
    }
    public static function logout(Router $router){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router){

        $alertas = [];//para que no aparesca undefined
        $usuario = new Usuario;//crea un objeto vacio y lo paso
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);//llena los campos con lo enviado
            $alertas = $usuario->validarCrear();//trae return de alertas errores

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta('error', 'el usuario ya existe');//solo inserto en el array el dato
                    $alertas = Usuario::getAlertas();//muestro el array con los datos
                }else{
                    //hashear el password
                    $usuario->hashPassword();

                    //eliminar password2
                    unset($usuario->password2);

                    //generar token
                    $usuario->generarToken();
                    //enviar mail por mailtrap
                        //se creo una clase para no poner todo el codigo aca, y se llama al objeto Email que contiene datos
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarConfirmacion();

                    //insertar dato
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        };

        $router->render('auth/crear', [
            'titulo' => 'crear cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje (Router $router){
        
        $router->render('auth/mensaje', [
            'titulo' => 'Envio de token'
            
        ]);
    }
    public static function confirmar(Router $router){
        $alertas = [];
        $token = s($_GET['token']) ?? null;
        
        if(!$token){
            header('Location : /');
        }

        $usuario = Usuario::where('token', $token);
        
        if($token === '' || empty($usuario) || $usuario === null){
            Usuario::setAlerta('error', 'token no valido');
        }else{
            $usuario->confirmado = '1';
            $usuario->token = null;
            unset($usuario->password2);
            $usuario->guardar();
            Usuario::setAlerta('exito','cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar nuevo password',
            'alertas' => $alertas,
            'token' => $token
        ]);
    }
    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();    

            if(empty($alertas)){
                //buscar usuario
                $usuario = Usuario::where('email', $usuario->email);
                if($usuario && $usuario->confirmado === '1'){
                    //encontro el usuario
                    $usuario->generarToken();
                    unset($usuario->password2);
                    
                    $usuario->guardar();

                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de exito
                    Usuario::setAlerta('exito', 'revisa tu mail para generar un nuevo password');
                    $alertas = Usuario::getAlertas();
                }else{
                    //no encuentra usuario
                    Usuario::setAlerta('error', 'el usuario no existe o no esta confirmado');
                    $alertas = Usuario::getAlertas();
                    
                }
            }
        };
        $router->render('auth/olvide', [
            'titulo' => 'Recuperar cuenta',
            'alertas' => $alertas
        ]);
    }
    public static function restablecer (Router $router){
        $alertas = [];
        $mostrar = true;

        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if(empty($usuario) || $usuario === null){
            Usuario::setAlerta('error', 'token no valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);//sincroniza lo agregado 'password'
            $alertas = $usuario->validarPassword();
            unset($usuario->password2);
            
            if(empty($alertas)){
                //hashear password
                $usuario->hashPassword();
                //eliminar token
                $usuario->token = null;
                //guardar
                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/restablecer', [
            'titulo' => 'Restablecer password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
}
?>
