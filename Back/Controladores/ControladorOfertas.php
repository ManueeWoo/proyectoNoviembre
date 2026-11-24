<?php
namespace App\Controladores;

use App\DAO\OfertaDAO;
use App\DAO\ServicioDAO;
use App\Helpers\Sesion;
use App\Modelo\Oferta;
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

    public function misOfertas(){
        $u = Sesion::dameLaSesion("user");
        $servicios=((new ServicioDAO())->findServiciosUser($u->getId()));
        $ofertas = ((new OfertaDAO())->findByIdUsuario($u->getId()));
        echo $this->twig->render("contenidos/misOfertas.html.twig", ["servicios"=>$servicios, "ofertas"=>$ofertas]);
    }

    public function crearOferta(){
        $u = Sesion::dameLaSesion("user");
        if($u !== null){
            $servicio = ((new ServicioDAO())->findById($_POST['servicio']));
            $puntos = $_POST['puntos'];
            $tiempo = $_POST['tiempo'];
            $descripcion = $_POST['descripcion'];
            $activo = isset($_POST['activo']) ? 1 : 0;
            $o = new Oferta($servicio, $u,$puntos, $descripcion, $tiempo, $activo);
            if(((new OfertaDAO())->insertarOferta($o))){
                header("Location: /ofertas");
            }
        
        }
    }

    public function borrarOferta(int $id){
        $u = Sesion::dameLaSesion("user");
        if($u !== null){
            if(((new OfertaDAO())->eliminarOfertaId($id))){
                header("Location: /ofertas");
            }
        }
    }

    public function cambiarEstado(int $id){
        $u = Sesion::dameLaSesion("user");
        if($u !== null){
            $o = (new OfertaDAO())->findById($id);
            if($o->getActivo() !== false){
                ((new OfertaDAO())->cambiarEstado($id, false));
            }else{
                ((new OfertaDAO())->cambiarEstado($id, true));
            }
            header("Location: /ofertas");
        }
    }
}