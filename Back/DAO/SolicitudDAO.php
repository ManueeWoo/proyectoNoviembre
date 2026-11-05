<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Solicitud;
use PDO;
class SolicitudDAO{
private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();//esto lo hago para q cada vez que hablo con la bd no hacer $conexion getpdo sino directo pdo
    }

    //CRUD
    public function insertarUser(Solicitud $s):bool{
        $sentencia = "INSERT INTO solicitud (id_usuario, id_oferta, estado, fecha, tiempoFinalizado, puntos, motivo, id_val) VALUES (:id_usuario, :id_oferta, :estado, :fecha, :tiempoFinalizado, :puntos, :motivo, :id_val)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([":id_usuario"=>$s->getUsuario()->getId(), "id_oferta"=>$s->getOferta(), ":estado"=>$s->getEstado(), ":fecha"=>$s->getFecha(), ":tiempoFinalizado"=>$s->getTiempoFinalizado(), ":puntos"=>$s->getPuntos(), ":motivo"=>$s->getMotivo(), ":id_val"=>$s->getValoracion()]); 
        if($esCorrecto){
            $s->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarUser(Solicitud $s, int $id):bool{
        $sentencia = "UPDATE solicitud SET id_usuario= :id_usuario, id_oferta = :id_oferta, estado = :estado, fecha = :fecha, tiempoFinalizado = :tiempoFinalizado, puntos = :puntos, motivo = :motivo, id_val = :id_val WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id_usuario"=>$s->getUsuario()->getId(), "id_oferta"=>$s->getOferta(), ":estado"=>$s->getEstado(), ":fecha"=>$s->getFecha(), ":tiempoFinalizado"=>$s->getTiempoFinalizado(), ":puntos"=>$s->getPuntos(), ":motivo"=>$s->getMotivo(), ":id_val"=>$s->getValoracion(), ":id"=>$id]);
    }

    public function eliminarUser(Solicitud $s):bool{
        $sentencia = "DELETE FROM solicitud WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$s->getId()]); 
    }

    public function eliminarUserId(int $id):bool{
        $sentencia = "DELETE FROM solicitud WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$id]); 
    }

    //QUERY

     public function findAll(): array{
        $resultado= $this->pdo->query("SELECT id_user, id_oferta, estado, fecha, tiempoFinalizado, puntos, motivo , id_val , id FROM solicitud");
        $solicitudes = [];
        foreach($resultado->fetchAll() as $fila){
            $solicitud = $this->filaToSolicitud($fila); 
            array_push($solicitudes,$solicitud);
        }
        return $solicitudes;
    }

    public function findById(int $id): ?Solicitud{
        $resultado= $this->pdo->prepare("SELECT id_user, id_oferta, estado, fecha, tiempoFinalizado, puntos, motivo , id_val , id FROM solicitud WHERE id=:id");
        $valoresSolicitud = null;
        $resultado->execute([":id"=>$id]);
        $valoresSolicitud = $resultado->fetch(PDO::FETCH_ASSOC);
        $user = null;
        if($valoresSolicitud === false){
            $user = null;
        }else{
            $user =$this->filaToSolicitud($valoresSolicitud);
        }
        return $user;
    }

    public function filaToSolicitud(array $datos): Solicitud{
        return new Solicitud($datos['id_user'], $datos['id_oferta'], $datos['estado'], $datos['fecha'], $datos['tiempoFinalizado'], $datos['puntos'], $datos['motivo'], $datos['id_val'], $datos['id']);
    }
}