<?php
namespace App\Controladores;

use Twig\Environment;
class Controlador{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarLayout(){
        echo $this->twig->render("layout.html.twig");
    }
    public function sacarInicio(){
        echo $this->twig->render("contenidos/inicio.html.twig");
    }

    public function sacarQuienesSomos() {
        echo $this->twig->render("contenidos/quienessomos.html.twig");
    }

}