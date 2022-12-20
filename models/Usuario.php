<?php 
namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? '0';
    }

    public function validarLogin() : array{
        if(!$this->email){
            self::$alertas['error'][] = 'el campo email se encuentra vacio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'email no valido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'el campo password no puede estar vacio';
        }
        return self::$alertas;
    }

    public function validarCrear() : array{
        if(!$this->nombre){
            self::$alertas['error'][] = 'el campo nombre se encuentra vacio';
        };
        if(!$this->email){
            self::$alertas['error'][] = 'el campo email se encuentra vacio';
        };
        if(!$this->password){
            self::$alertas['error'][] = 'el campo password se encuentra vacio';
        };
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'el campo password debe tener al menos 6 caracteres';
        };
        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'los password no coiciden';
        };

        return self::$alertas;
    }

    //valida un email
    public function validarEmail() : array{
        if(!$this->email){
            self::$alertas['error'][] = 'el campo email no puede estar vacio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'email no valido';
        }
        return self::$alertas;
    }
    //valida perfil
    public function validarPerfil() : array{
        if(!$this->nombre){
            self::$alertas['error'][] = 'el campo nombre no puede estar vacio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'el campo email no puede estar vacio';
        }
        return self::$alertas;
    }
    //valida un password
    public function validarPassword() : array{
        if(!$this->password){
            self::$alertas['error'][] = 'el campo password no puede estar vacio';
        }
        if(strlen($this->password) < 6 ){
            self::$alertas['error'][] = 'debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }
    
    public function nuevo_password() : array{
        if(!$this->password_actual){
            self::$alertas['error'][] = 'el password actual no puede estar vacio';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'el password nuevo no puede estar vacio';
        }
        if(strlen($this->password_nuevo) < 6 ){
            self::$alertas['error'][] = 'debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function comprobar_password() : bool{
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() : void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function generarToken() : void{
        $this->token = md5(uniqid());
    }
}
?>