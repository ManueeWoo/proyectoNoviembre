<?php
namespace App\Controladores;

use App\DAO\UsuarioDAO;
use App\Helpers\Sesion;
use Twig\Environment;
use App\Modelo\Usuario;
use App\Helpers\Login;
class ControladorLogin{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }

 
}
