<?php
namespace App\Helpers;
use App\Helpers\Sesion;
use App\Modelo\Usuario;
use App\DAO\UsuarioDAO;
class Login{
    public static function logearse(Usuario $usuario):array{
        $errores = self::validarUsuario($usuario);
        if(empty($errores)){
            Sesion::aniadirASesion('user', $usuario);
        }
        return $errores;
    }

    public static function existeUser(Usuario $usuario):bool{
        $existe = false;
        $repo = new UsuarioDAO();
        if($repo->findById($usuario->getID())){
            $existe = true;
        }
        return $existe;
    }
//comprobar luego para el login
    public static function validarUsuario(Usuario $usuario):array{
        $errores = [];
        $repo = new UsuarioDAO();
         $encontrado = $repo->findByNombreContra($usuario);
        if(empty($encontrado)){
            $errores["nombreContra"] = "error los campos no coinciden con el user"; 
        }
        return $errores;
    }

    public static function estaLogeado():bool{
        return Sesion::existeSesion('user');
    }

    public static function deslogear(Usuario $usuario){
        Sesion::cerrarSesion();
    }

/*
    public static function registrarse(Usuario $usuario):bool{
        
    }
*/
}



