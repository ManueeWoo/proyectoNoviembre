<?php
namespace App\Controladores;

use App\DAO\CandidatosDAO;
use App\DAO\UsuarioDAO;
use App\Helpers\Sesion;
use Twig\Environment;
class ControladorAdmin{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarPanelUsers(){
        $usuarios = ((new UsuarioDAO())->findAll());
        $candidatos = ((new CandidatosDAO())->findAll());
        echo $this->twig->render("contenidos/adminUsuarios.html.twig", ["usuarios"=>$usuarios , "pendientes"=>$candidatos]);
    }

    public function borrarUsuario(int $id){
        $user = Sesion::dameLaSesion("user");
        if($user->getRol()!=="admin"){
            header("Location: /login");
        }else{
            ((new UsuarioDAO())->eliminarUserId($id));
            header("Location: /adminUsers");
        }
    }

    public function aceptarUser(int $id){
        $user = Sesion::dameLaSesion("user");
        if($user->getRol()!=="admin"){
            header("Location: /login");
        }else{
            ((new CandidatosDAO())->aprobar($id));
            header("Location: /adminUsers");
        }
    }

    public function rechazarUser(int $id){
        $user = Sesion::dameLaSesion("user");
        if($user->getRol()!=="admin"){
            header("Location: /login");
        }else{
            ((new CandidatosDAO())->eliminarUserId($id));
            header("Location: /adminUsers");
        }
    }
}