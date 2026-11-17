<?php
namespace App\Helpers;
use App\DAO\CandidatosDAO;
use App\Helpers\Sesion;
use App\Modelo\Usuario;
use App\DAO\UsuarioDAO;
class Login{
    public static function logearse(Usuario $intento): array{
    $resultado = self::validarUsuario($intento);
    if(empty($resultado["errores"])){
        Sesion::aniadirASesion('user', $resultado["usuario"]);
    }
    return $resultado["errores"];
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
    public static function validarUsuario(Usuario $intento): array {
    $errores = [];
    $encontrado = (new UsuarioDAO())->findByEmailContra($intento);

    if ($encontrado === null) {
        $candidato = (new CandidatosDAO())->findByEmail($intento->getEmail());

        if ($candidato !== null && password_verify($intento->getContra(), $candidato->getContra())) {
            $errores["pendiente"] = "Tu cuenta está pendiente de que el administrador la acepte.";
        } else {
            $errores["nombreContra"] = "Los campos no coinciden con ningún usuario.";
        }
    }

    return ["errores" => $errores, "usuario" => $encontrado];
}

    public static function estaLogeado():bool{
        return Sesion::existeSesion('user');
    }

    public static function deslogear():bool{
       return Sesion::cerrarSesion();
    }

/*
    public static function registrarse(Usuario $usuario):bool{
        
    }
*/
}



