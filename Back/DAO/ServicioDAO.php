<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Servicio;
use PDO;
class ServicioDAO{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }

    //CRUD
    public function insertarServicio(Servicio $s):bool{
        $sentencia = "INSERT INTO servicios (id_cat, titulo, descripcion) VALUES (:id_cat, :titulo, :descripcion)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([":id_cat"=>$s->getCat()->getId(), ":titulo"=>$s->getTitulo(), ":descripcion"=>$s->getDecripcion()]);
        if($esCorrecto){
            $s->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarServicio(Servicio $s, int $id):bool{
        $sentencia = "UPDATE servicios SET id_cat = :id_cat, titulo = :titulo, descripcion = :descripcion WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id_cat"=>$s->getCat()->getId(), ":titulo"=>$s->getTitulo(), ":descripcion"=>$s->getDecripcion(), ":id"=>$id]);
    }

    public function eliminarServicio(Servicio $s):bool{
        $sentencia = "DELETE FROM servicios WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$s->getId()]);
    }

    public function eliminarServicioId(int $id):bool{
        $sentencia = "DELETE FROM servicios WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$id]);
    }

    //QUERY

    public function findAll(): array{
        $resultado = $this->pdo->query("SELECT id_cat, titulo, descripcion, id FROM servicios");
        $servicios = [];
        foreach($resultado->fetchAll(PDO::FETCH_ASSOC) as $fila){
            $servicio = $this->filaToServicio($fila);
            array_push($servicios, $servicio);
        }
        return $servicios;
    }

    public function findByIdCat(int $id): array{
        $resultado = $this->pdo->prepare("SELECT id_cat, titulo, descripcion, id FROM servicios WHERE id_cat=:id_cat");
        $resultado->execute([":id_cat"=>$id]);
        $servicios = [];
        $servicios = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $servicios;
    }

    public function findById(int $id): ?Servicio{
        $resultado = $this->pdo->prepare("SELECT id_cat, titulo, descripcion, id FROM servicios WHERE id = :id");
        $resultado->execute([":id"=>$id]);
        $valoresServicio = $resultado->fetch(PDO::FETCH_ASSOC);
        $servicio = null;
        if($valoresServicio === false){
            $servicio = null;
        }else{
            $servicio = $this->filaToServicio($valoresServicio);
        }
        return $servicio;
    }

   private function filaToServicio(array $fila): Servicio {
         $categoria = (new CategoriaDAO())->findById($fila['id_cat']); 
    return new Servicio($categoria, $fila['titulo'], $fila['descripcion'], $fila['id']);
}
}