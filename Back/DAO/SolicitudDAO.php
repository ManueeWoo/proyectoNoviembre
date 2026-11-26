<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Solicitud;
use App\Modelo\EstadoOferta;
use PDO;
class SolicitudDAO{
private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();//esto lo hago para q cada vez que hablo con la bd no hacer $conexion getpdo sino directo pdo
    }

    //CRUD
    public function insertarSolicitud(Solicitud $s):bool{
        $sentencia = "INSERT INTO solicitud (id_user, id_oferta, estado, fecha, tiempoFinalizado, puntos, motivo, id_val) VALUES (:id_user, :id_oferta, :estado, :fecha, :tiempoFinalizado, :puntos, :motivo, :id_val)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([":id_user"=>$s->getUsuario()->getId(), ":id_oferta"=>$s->getOferta()->getId(), ":estado"=>$s->getEstado()->value,":fecha"=>$s->getFecha(),":tiempoFinalizado"=>$s->getTiempoFinalizado(), ":puntos"=>$s->getPuntos(), ":motivo"=>$s->getMotivo(), ":id_val"=>$s->getValoracion()?->getId()]); 
        if($esCorrecto){
            $s->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarSolicitud(Solicitud $s, int $id):bool{
        $sentencia = "UPDATE solicitud SET id_user= :id_user, id_oferta = :id_oferta, estado = :estado, fecha = :fecha, tiempoFinalizado = :tiempoFinalizado, puntos = :puntos, motivo = :motivo, id_val = :id_val WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id_user"=>$s->getUsuario()->getId(), ":id_oferta"=>$s->getOferta()->getId(), ":estado"=>$s->getEstado()->value, ":fecha"=>$s->getFecha(), ":tiempoFinalizado"=>$s->getTiempoFinalizado(), ":puntos"=>$s->getPuntos(), ":motivo"=>$s->getMotivo(), ":id_val"=>$s->getValoracion()->getId(), ":id"=>$id]);
    }

    public function eliminarSolicitud(Solicitud $s):bool{
        $sentencia = "DELETE FROM solicitud WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$s->getId()]); 
    }

    public function eliminarSolicitudId(int $id):bool{
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

    public function existe(int $idUser, int $idOferta): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM solicitud WHERE id_user = :u AND id_oferta = :o"
        );
        $stmt->execute([":u" => $idUser, ":o" => $idOferta]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function findSolicitudUser(int $idUser, bool $recibidas): array {
        //esto para distinguir si quiero las que yo solicito o las que me solicitan a mi
        $unirPersona = $recibidas ? "u.id = s.id_user" : "u.id = o.id_user";
        $filtro      = $recibidas ? "o.id_user = :yo"  : "s.id_user = :yo";

        $stmt = $this->pdo->prepare(
            "SELECT s.id, s.estado, s.fecha, s.puntos,
                    o.id AS oferta_id, o.descripcion,
                    sv.titulo AS servicio,
                    u.id AS persona_id, u.nombre AS persona_nombre, u.foto AS persona_foto, s.fin_cliente, s.fin_profesional
            FROM solicitud s
            JOIN oferta o    ON o.id  = s.id_oferta
            JOIN servicios sv ON sv.id = o.id_servicio
            JOIN usuario u   ON $unirPersona
            WHERE $filtro
            ORDER BY s.fecha DESC, s.id DESC"
        );
        $stmt->execute([":yo" => $idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findSolicitudUserYProfesional(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT s.id, s.estado, s.id_user, o.id_user AS id_dueno
            FROM solicitud s
            JOIN oferta o ON o.id = s.id_oferta
            WHERE s.id = :id"
        );
        $stmt->execute([":id" => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        $resultado = null;
        if ($fila !== false) {
            $resultado = $fila;
        }
        return $resultado;
    }

    public function cambiarEstado(int $id, string $nuevo): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE solicitud SET estado = :nuevo WHERE id = :id"
        );
        $stmt->execute([":nuevo" => $nuevo, ":id" => $id]);
        return $stmt->rowCount() === 1;
    }


    public function finalizar(int $idSolicitud, int $idUser): bool {
    $hecho = false;

    try {
        $stmt = $this->pdo->prepare(
            "SELECT s.id_user AS id_cliente, s.puntos, s.estado, s.fin_cliente, s.fin_profesional,
                    o.id_user AS id_prof, c.saldo
               FROM solicitud s
               JOIN oferta o  ON o.id = s.id_oferta
               JOIN usuario c ON c.id = s.id_user
              WHERE s.id = :id"
        );
        $stmt->execute([":id" => $idSolicitud]);
        $s = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($s !== false && $s['estado'] === 'aceptado') {
            $soyCliente = (int)$s['id_cliente'] === $idUser;

            // pa saber si yo le dao falta el profesional y al reves
            $completa = $soyCliente ? $s['fin_profesional'] == 1 : $s['fin_cliente'] == 1;

            if (!$completa || $s['saldo'] >= $s['puntos']) {
                $this->pdo->beginTransaction();

                $columna = $soyCliente ? "fin_cliente" : "fin_profesional";
                $stmt2 = $this->pdo->prepare("UPDATE solicitud SET $columna = 1 WHERE id = :id");
                $stmt2->execute([":id" => $idSolicitud]);

                if ($completa) {
                    $stmt3 = $this->pdo->prepare("UPDATE usuario SET saldo = saldo - :p WHERE id = :u");
                    $stmt3->execute([":p" => $s['puntos'], ":u" => $s['id_cliente']]);
                    $stmt4=$this->pdo->prepare("UPDATE usuario SET saldo = saldo + :p WHERE id = :u");
                    $stmt4->execute([":p" => $s['puntos'], ":u" => $s['id_prof']]);
                    $stmt5=$this->pdo->prepare("UPDATE solicitud SET estado = 'completado' WHERE id = :id");
                    $stmt5->execute([":id" => $idSolicitud]);
                }

                $this->pdo->commit();
                $hecho = true;
            }
        }
    } catch (\Throwable $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    return $hecho;
}

    public function filaToSolicitud(array $datos): Solicitud{
    $userDAO = new UsuarioDAO();
    $ofertaDAO = new OfertaDAO();
    $valoracionDAO = new ValoracionDAO();
    $usuario = $userDAO->findById($datos['id_user']);
    $oferta = $ofertaDAO->findById($datos['id_oferta']);
    $valoracion = $valoracionDAO->findById($datos['id_val']) ;
    return new Solicitud($usuario, $oferta, $datos['fecha'], $datos['tiempoFinalizado'], $datos['puntos'], $datos['motivo'],EstadoOferta::from($datos['estado']),$valoracion, $datos['id']);
}
}