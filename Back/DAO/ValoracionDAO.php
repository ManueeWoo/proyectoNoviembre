<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Valoracion;
use PDO;

class ValoracionDAO{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }

    //CRUD
    public function insertarValoracion(Valoracion $v): bool{
        $sentencia = "INSERT INTO valoracion (puntuacion, descripcion) VALUES (:puntuacion, :descripcion)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([
            ":puntuacion" => $v->getPuntuacion(),
            ":descripcion" => $v->getDescripcion()
        ]);
        if($esCorrecto){
            $v->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarValoracion(Valoracion $v, int $id): bool{
        $sentencia = "UPDATE valoracion SET puntuacion = :puntuacion, descripcion = :descripcion WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([
            ":puntuacion" => $v->getPuntuacion(),
            ":descripcion" => $v->getDescripcion(),
            ":id" => $id
        ]);
    }

    public function eliminarValoracion(Valoracion $v): bool{
        $sentencia = "DELETE FROM valoracion WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id" => $v->getId()]);
    }

    public function eliminarValoracionId(int $id): bool{
        $sentencia = "DELETE FROM valoracion WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id" => $id]);
    }

    //QUERY

    public function findAll(): array{
        $resultado = $this->pdo->query("SELECT puntuacion, descripcion, id FROM valoracion");
        $valoraciones = [];
        foreach($resultado->fetchAll() as $fila){
            $valoraciones[] = $this->filaToValoracion($fila);
        }
        return $valoraciones;
    }

    public function findById(int $id): ?Valoracion{
        $resultado = $this->pdo->prepare("SELECT puntuacion, descripcion, id FROM valoracion WHERE id = :id");
        $resultado->execute([":id" => $id]);
        $valores = $resultado->fetch(PDO::FETCH_ASSOC);
        if($valores === false){
            return null;
        }
        return $this->filaToValoracion($valores);
    }

    public function filaToValoracion(array $datos): Valoracion{
        return new Valoracion($datos['puntuacion'], $datos['descripcion'], $datos['id']);
    }
}