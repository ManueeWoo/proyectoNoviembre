<?php
namespace App\Controladores;

use App\DAO\OfertaDAO;
use App\DAO\ServicioDAO;
use Twig\Environment;
class ControladorServicios{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarServicios(){
        $servicios= ((new ServicioDAO())->findAll());
        foreach($servicios as $servicio){
            $ofertasServicio = (new OfertaDAO())->findByCountServicio($servicio->getId());
            $servicio->setOfertas($ofertasServicio);
        }
        echo $this->twig->render("contenidos/servicios.html.twig", ["servicios"=>$servicios]);
    }
}