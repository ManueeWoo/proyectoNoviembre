<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Categoria;
use PDO;

class CategoriaDAO{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }

    //CRUD
    public function insertarCategoria(Categoria $c): bool{
        $sentencia = "INSERT INTO categoria (titulo, descripcion) VALUES (:titulo, :descripcion)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([
            ":titulo" => $c->getTitulo(),
            ":descripcion" => $c->getDescripcion()
        ]);
        if($esCorrecto){
            $c->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarCategoria(Categoria $c, int $id): bool{
        $sentencia = "UPDATE categoria SET titulo = :titulo, descripcion = :descripcion WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([
            ":titulo" => $c->getTitulo(),
            ":descripcion" => $c->getDescripcion(),
            ":id" => $id
        ]);
    }

    public function eliminarCategoria(Categoria $c): bool{
        $sentencia = "DELETE FROM categoria WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id" => $c->getId()]);
    }

    public function eliminarCategoriaId(int $id): bool{
        $sentencia = "DELETE FROM categoria WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id" => $id]);
    }

    //QUERY

    public function findAll(): array{
        $resultado = $this->pdo->query("SELECT titulo, descripcion, id FROM categoria");
        $categorias = [];
        foreach($resultado->fetchAll() as $fila){
            $categorias[] = $this->filaToCategoria($fila);
        }
        return $categorias;
    }

    public function findById(int $id): ?Categoria{
        $resultado = $this->pdo->prepare("SELECT titulo, descripcion, id FROM categoria WHERE id = :id");
        $resultado->execute([":id" => $id]);
        $valores = $resultado->fetch(PDO::FETCH_ASSOC);
        if($valores === false){
            return null;
        }
        return $this->filaToCategoria($valores);
    }

    public function filaToCategoria(array $datos): Categoria{
        return new Categoria($datos['titulo'], $datos['descripcion'], $datos['id']);
    }
}