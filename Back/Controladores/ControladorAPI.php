<?php
namespace App\Controladores;
use App\DAO\CategoriaDAO;
use App\DAO\ServicioDAO;
class ControladorAPI{

    public function __construct(){

    }

    public function darCategorias() {
        $repo = new CategoriaDAO();
        $categorias = $repo->findAll();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($categorias);
    }

    public function darServicios(int $id) {
        $repo = new ServicioDAO();
        $servicios = $repo->findByIdCat($id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($servicios);
    }

}