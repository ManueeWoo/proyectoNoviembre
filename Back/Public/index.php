<?php

require_once "../vendor/autoload.php";

use App\Controladores\ControladorAPIUsuario;
use App\Controladores\ControladorServicios;
use App\Router\Router;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Controladores\Controlador;
use App\Controladores\ControladorLogin;
use App\Controladores\ControladorOfertas;
use App\Controladores\ControladorAdmin;
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
$router->get("/",Controlador::class."@sacarInicio");
$router->get("/quienessomos",Controlador::class."@sacarQuienes");
$router->get("/login",ControladorLogin::class."@sacarLogin");
$router->post("/login", ControladorLogin::class."@sacarLogin");
$router->get("/cerrarsesion",ControladorLogin::class."@cerrarSesion");
$router->get("/verperfil",ControladorLogin::class."@verPerfil");
$router->post("/verperfil", ControladorLogin::class."@guardarFoto");
$router->get("/api/categorias", ControladorAPIUsuario::class."@darCategorias");
$router->get("/api/servicios/{id}",ControladorAPIUsuario::class."@darServicios");
$router->post("/api/registrar",ControladorAPIUsuario::class."@registrar");
$router->get("/servicios",ControladorServicios::class."@sacarServicios");
$router->get("/api/servicios",ControladorAPIUsuario::class."@darAllServicios");
$router->get("/servicio/{id}",ControladorOfertas::class."@sacarOfertasByServicio");
$router->get("/oferta/{id}",ControladorOfertas::class."@verOferta");
$router->get("/adminUsers",ControladorAdmin::class."@sacarPanelUsers");
$router->post("/adminUsers/borrar/{id}",ControladorAdmin::class."@borrarUsuario");
$router->post("/adminUsers/aceptarPendiente/{id}",ControladorAdmin::class."@aceptarUser");
$router->post("/adminUsers/rechazarPendiente/{id}",ControladorAdmin::class."@rechazarUser");
$router->get("/misofertas",ControladorOfertas::class."@misOfertas");
$router->dispatch($_SERVER['REQUEST_METHOD'],$_SERVER['REQUEST_URI']);


