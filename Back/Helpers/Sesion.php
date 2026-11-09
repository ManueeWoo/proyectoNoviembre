<?php
namespace App\Helpers;
class Sesion{
    public static function existeSesion($clave):bool{
        self::abrirSesion();
        return isset($_SESSION[$clave]);
    }

    public static function abrirSesion(){
        if(session_status() === PHP_SESSION_NONE){
            //para que no salga el notice en la web 
            session_start();
        }
    }

    public static function cerrarSesion(): bool{
        self::abrirSesion();
        $_SESSION = array();
        setcookie(session_name(), '' , time() -4000);
        return session_destroy();
    }

    public static function aniadirASesion($clave, $valor){
        self::abrirSesion();
        $_SESSION[$clave] = $valor;
    }

    public static function borrarClaveSesion($clave){
        self::abrirSesion();
        unset($_SESSION[$clave]);
    }

    public static function dameLaSesion($clave){
        self::abrirSesion();
        return $_SESSION[$clave];
    }
}
