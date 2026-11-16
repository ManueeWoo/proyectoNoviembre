<?php
namespace App\Controladores;

use App\DAO\OfertaDAO;
use App\DAO\ServicioDAO;
use Twig\Environment;
class ControladorOfertas{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarOfertasByServicio(int $id){
        $servicio= ((new ServicioDAO())->findById($id));
        $ofertas = ((new OfertaDAO())->findByIdServicio($servicio->getId()));
        echo $this->twig->render("contenidos/oferta.html.twig", ["servicio"=>$servicio, "ofertas" => $ofertas]);
    }

    public function verOferta(int $id){
        $oferta = ((new OfertaDAO())->findById($id));
         echo $this->twig->render("contenidos/verOferta.html.twig", ["oferta" => $oferta]);
    }
}