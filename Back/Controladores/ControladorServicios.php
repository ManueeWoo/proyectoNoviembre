<?php
namespace App\Controladores;

use App\DAO\ServicioDAO;
use Twig\Environment;
class ControladorServicios{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarServicios(){
        $servicios= ((new ServicioDAO())->findAll());
        echo $this->twig->render("contenidos/servicios.html.twig", ["servicios"=>$servicios]);
    }

}