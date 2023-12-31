<?php


namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = "usuarios";
    protected static $columnasDB = ["id","nombre","email","password","token","confirmado"];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $password_actual;
    public $password_nuevo;
    public $token;
    public $confirmado;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->password2 = $args["password2"] ?? "";
        $this->password_actual = $args["password_actual"] ?? "";
        $this->password_nuevo = $args["password_nuevo"] ?? "";
        $this->token = $args["token"] ?? "";
        $this->confirmado = $args["confirmado"] ?? 0;
    }
    public function validarLogin(){
        if(!$this->email){
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas["error"][] = "El email no es válido";
        }
        if(!$this->password){
            self::$alertas["error"][] = "El Password no puede ir vacío";
        }else if(strlen($this->password)<6){
            self::$alertas["error"][] = "El password debe contener al menos 6 caracteres";
        }
        return self::$alertas;
    }
    //Validación para nuevas cuentas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre del Usuario es Obligatorio";
        }
        if(!$this->email){
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }
        if(!$this->password){
            self::$alertas["error"][] = "El Password no puede ir vacío";
        }else if(strlen($this->password)<6){
            self::$alertas["error"][] = "El password debe contener al menos 6 caracteres";
        }else if($this->password!==$this->password2){
            self::$alertas["error"][] = "Las contraseñas son diferentes";
        }
        return self::$alertas;
    }
    public function validarPassword(){
        if(!$this->password){
            self::$alertas["error"][] = "El Password no puede ir vacío";
        }else if(strlen($this->password)<6){
            self::$alertas["error"][] = "El password debe contener al menos 6 caracteres";
        }
        return self::$alertas;
    }
    public function hashPassword(){
        $this->password = password_hash($this->password,PASSWORD_BCRYPT);
    }
    public function crearToken(){
        $this->token = uniqid();
    }
    public function validarEmail(){
        if(!$this->email){
            self::$alertas["error"][] = "El email es obligatorio";
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas["error"][] = "El email no es válido";
        }
        return self::$alertas;
    }
    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre del Usuario es Obligatorio";
        }
        if(!$this->email){
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }
        return self::$alertas;
    }
    public function nuevo_password(){
        if(!$this->password_actual){
            self::$alertas["error"][] = "La Contraseña Actual es Obligatoria";
        }
        if(!$this->password_nuevo){
            self::$alertas["error"][] = "La Contraseña Nueva es Obligatoria";
        }
        if(strlen($this->password_nuevo)<6){
            self::$alertas["error"][] = "La Contraseña debe contener al menos 6 caracteres";
        }
        return self::$alertas;
    }
    public function comprobar_password():bool{
        return password_verify($this->password_actual,$this->password);
    }
}