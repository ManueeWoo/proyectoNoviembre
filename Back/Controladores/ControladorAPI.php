<?php
namespace App\Controladores;
use App\DAO\CategoriaDAO;
class ControladorAPI{

    public function __construct(){

    }

    public function darCategorias(){
        $repo = new CategoriaDAO();
        $categorias = $repo->findAll();
        $json = json_encode($categorias);
    }


}