<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Oferta;
use PDO;

class OfertaDAO{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }

    //CRUD
    public function insertarOferta(Oferta $o): bool{
        $sentencia = "INSERT INTO oferta (id_servicio, id_user, puntos, descripcion, tiempo_estimado, activo) VALUES (:id_servicio, :id_user, :puntos, :descripcion, :tiempo_estimado, :activo)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([
            ":id_servicio" => $o->getServicio()->getId(),
            ":id_user" => $o->getUsuario()->getId(),
            ":puntos" => $o->getPuntos(),
            ":descripcion" => $o->getDescripcion(),
            ":tiempo_estimado" => $o->getTiempoEstimado(),
            ":activo" => $o->getActivo()
        ]);
        if($esCorrecto){
            $o->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarOferta(Oferta $o, int $id): bool{
        $sentencia = "UPDATE oferta SET id_servicio = :id_servicio, id_user = :id_user, puntos = :puntos, descripcion = :descripcion, tiempo_estimado = :tiempo_estimado, activo = :activo WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([
            ":id_servicio" => $o->getServicio()->getId(),
            ":id_user" => $o->getUsuario()->getId(),
            ":puntos" => $o->getPuntos(),
            ":descripcion" => $o->getDescripcion(),
            ":tiempo_estimado" => $o->getTiempoEstimado(),
            ":activo" => $o->getActivo(),
            ":id" => $id
        ]);
    }

    public function eliminarOferta(Oferta $o): bool{
        $sentencia = "DELETE FROM oferta WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id" => $o->getId()]);
    }

    public function eliminarOfertaId(int $id): bool{
        $sentencia = "DELETE FROM oferta WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id" => $id]);
    }

    //QUERY

    public function findAll(): array{
        $resultado = $this->pdo->query("SELECT id_servicio, id_user, puntos, descripcion, tiempo_estimado, activo, id FROM oferta");
        $ofertas = [];
        foreach($resultado->fetchAll() as $fila){
            $ofertas[] = $this->filaToOferta($fila);
        }
        return $ofertas;
    }

    public function findById(int $id): ?Oferta{
        $resultado = $this->pdo->prepare("SELECT id_servicio, id_user, puntos, descripcion, tiempo_estimado, activo, id FROM oferta WHERE id = :id");
        $resultado->execute([":id" => $id]);
        $valores = $resultado->fetch(PDO::FETCH_ASSOC);
        if($valores === false){
            return null;
        }
        return $this->filaToOferta($valores);
    }

    public function filaToOferta(array $datos): Oferta{
        return new Oferta($datos['id_servicio'], $datos['id_user'], $datos['puntos'], $datos['descripcion'], $datos['tiempo_estimado'], $datos['activo'], $datos['id']);
    }
}