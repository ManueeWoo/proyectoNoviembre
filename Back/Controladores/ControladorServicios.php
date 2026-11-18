<?php
namespace App\Controladores;

use App\DAO\OfertaDAO;
use App\DAO\ServicioDAO;
use App\DAO\CategoriaDAO;
use Twig\Environment;
class ControladorServicios{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarServicios() {
    echo $this->twig->render("contenidos/servicios.html.twig");
}
}