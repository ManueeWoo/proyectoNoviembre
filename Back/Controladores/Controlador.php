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

    public function sacarQuienes(){
        echo $this->twig->render("contenidos/quienessomos.html.twig");
    }

    public function sacarLogin(){
        echo $this->twig->render("contenidos/login.html.twig");
    }
    public function pintarId(int $id){
        echo $id;
    }
}