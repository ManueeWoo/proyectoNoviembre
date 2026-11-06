<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\AccesoServicio;
use PDO;

class AccesoServicioDAO{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }

    //CRUD
    public function insertarAcceso(AccesoServicio $a): bool{
        $sentencia = "INSERT INTO accesoServicio (id_user, id_servicio, estado) VALUES (:id_user, :id_servicio, :estado)";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([
            ":id_user" => $a->getUsuario()->getId(),
            ":id_servicio" => $a->getServicio()->getId(),
            ":estado" => $a->getEstado()
        ]);
    }

    public function actualizarAcceso(AccesoServicio $a): bool{
        $sentencia = "UPDATE accesoServicio SET estado = :estado WHERE id_user = :id_user AND id_servicio = :id_servicio";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([
            ":estado" => $a->getEstado(),
            ":id_user" => $a->getUsuario()->getId(),
            ":id_servicio" => $a->getServicio()->getId()
        ]);
    }

    public function eliminarAcceso(AccesoServicio $a): bool{
        return $this->eliminarAccesoId($a->getUsuario()->getId(), $a->getServicio()->getId());
    }

    public function eliminarAccesoId(int $idUser, int $idServicio): bool{
        $sentencia = "DELETE FROM accesoServicio WHERE id_user = :id_user AND id_servicio = :id_servicio";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id_user" => $idUser, ":id_servicio" => $idServicio]);
    }

    //QUERY

    public function findAll(): array{
        $resultado = $this->pdo->query("SELECT id_user, id_servicio, estado FROM accesoServicio");
        $accesos = [];
        foreach($resultado->fetchAll() as $fila){
            $accesos[] = $this->filaToAcceso($fila);
        }
        return $accesos;
    }

    public function findById(int $idUser, int $idServicio): ?AccesoServicio{
        $resultado = $this->pdo->prepare("SELECT id_user, id_servicio, estado FROM accesoServicio WHERE id_user = :id_user AND id_servicio = :id_servicio");
        $resultado->execute([":id_user" => $idUser, ":id_servicio" => $idServicio]);
        $valores = $resultado->fetch(PDO::FETCH_ASSOC);
        if($valores === false){
            return null;
        }
        return $this->filaToAcceso($valores);
    }

    public function findByUser(int $idUser): array{
        $resultado = $this->pdo->prepare("SELECT id_user, id_servicio, estado FROM accesoServicio WHERE id_user = :id_user");
        $resultado->execute([":id_user" => $idUser]);
        $accesos = [];
        foreach($resultado->fetchAll(PDO::FETCH_ASSOC) as $fila){
            $accesos[] = $this->filaToAcceso($fila);
        }
        return $accesos;
    }

    public function filaToAcceso(array $datos): AccesoServicio{
        return new AccesoServicio($datos['id_user'], $datos['id_servicio'], $datos['estado']);
    }
}