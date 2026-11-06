<?php

require_once "../vendor/autoload.php";

use App\Router\Router;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Controladores\Controlador;
use App\Controladores\ControladorLogin;

$loader = new FilesystemLoader(__DIR__ . '/../Plantillas');
$twig = new Environment($loader);
$router= new Router($twig);
$router->get("/inicio/{id}",Controlador::class."@pintarId"); //tengo que poner asi la clase porque si no conoce a ningun Controladores hay que ponerla con el nombre entero
$router->get("/",Controlador::class."@sacarLayout");
$router->get("/quienessomos",Controlador::class."@sacarQuienes");
$router->get("/login",ControladorLogin::class."@sacarLogin");
$router->post("/login", ControladorLogin::class."@sacarLogin");
$router->dispatch($_SERVER['REQUEST_METHOD'],$_SERVER['REQUEST_URI']);


