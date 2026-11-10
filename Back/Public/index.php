<?php

require_once "../vendor/autoload.php";

use App\Router\Router;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Controladores\Controlador;
use App\Controladores\ControladorLogin;
use  App\Helpers\Login;
use  App\Helpers\Sesion;

$loader = new FilesystemLoader(__DIR__ . '/../Plantillas');
$twig = new Environment($loader);
$usuarioRol = "invitado";
if (Login::estaLogeado()) {
    $u= Sesion::dameLaSesion("user");
    $usuarioRol = $u->getRol();
}
$twig->addGlobal('rol', $usuarioRol);


$router= new Router($twig);
$router->get("/inicio/{id}",Controlador::class."@pintarId"); //tengo que poner asi la clase porque si no conoce a ningun Controladores hay que ponerla con el nombre entero
$router->get("/",Controlador::class."@sacarInicio");
$router->get("/quienessomos",Controlador::class."@sacarQuienes");
$router->get("/login",ControladorLogin::class."@sacarLogin");
$router->post("/login", ControladorLogin::class."@sacarLogin");
$router->get("/cerrarsesion",ControladorLogin::class."@cerrarSesion");
$router->get("/verperfil",ControladorLogin::class."@verPerfil");
$router->post("/verperfil", ControladorLogin::class."@guardarFoto");
$router->dispatch($_SERVER['REQUEST_METHOD'],$_SERVER['REQUEST_URI']);


