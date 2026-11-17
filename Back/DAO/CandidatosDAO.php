<?php
namespace App\DAO;

use App\Modelo\Usuario;
use PDO;

class CandidatosDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = ConexionDB::getConnection();
    }

    public function registrar(Usuario $c, array $idsServicios): bool {
        $res = false;
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO candidatos (nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, foto, pdf)
                 VALUES (:nombre, :ap1, :ap2, :alta, :saldo, :tel, :rol, :email, :contrasenia, :foto, :pdf)"
            );
            $stmt->execute([
                ":nombre"      => $c->getNombre(),
                ":ap1"         => $c->getAp1(),
                ":ap2"         => $c->getAp2(),
                ":alta"        => $c->getAlta(),
                ":saldo"       => $c->getSaldo(),
                ":tel"         => $c->getTelefono(),
                ":rol"         => $c->getRol(),
                ":email"       => $c->getEmail(),
                ":contrasenia" => password_hash($c->getContra(), PASSWORD_DEFAULT),
                ":foto"        => $c->getFoto(),
                ":pdf"         => $c->getPdf(),
            ]);
            $Id = (int)$this->pdo->lastInsertId();

            $stmtS = $this->pdo->prepare(
                "INSERT INTO serviciosCandidatos (id_candidato, id_servicio) VALUES (:c, :s)"
            );
            foreach ($idsServicios as $idServicio) {
                $stmtS->execute([":c" => $Id, ":s" => (int)$idServicio]);
            }

            $this->pdo->commit();
            $res = true;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
        return $res;
    }

    public function aprobar(int $id): bool {
        $aprobado = false;
        try {
            $this->pdo->beginTransaction();

            $res = $this->pdo->prepare(
                "INSERT INTO usuario (nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, foto, pdf)
                 SELECT nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, foto, pdf
                 FROM candidatos WHERE id = :id"
            );
            $res->execute([":id" => $id]);
            $idUsuario = (int)$this->pdo->lastInsertId();

            $res = $this->pdo->prepare(
                "SELECT id_servicio FROM serviciosCandidatos WHERE id_candidato = :c"
            );
            $res->execute([":c" => $id]);
            $idsServicios = $res->fetchAll(PDO::FETCH_COLUMN);

            $res = $this->pdo->prepare(
                "INSERT INTO accesoServicio (id_user, id_servicio) VALUES (:u, :s)"
            );
            foreach ($idsServicios as $idServicio) {
                $res->execute([":u" => $idUsuario, ":s" => (int)$idServicio]);
            }

            $res = $this->pdo->prepare("DELETE FROM serviciosCandidatos WHERE id_candidato = :c");
            $res->execute([":c" => $id]);

            $res = $this->pdo->prepare("DELETE FROM candidatos WHERE id = :id");
            $res->execute([":id" => $id]);

            $this->pdo->commit();
            $aprobado = true;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
              error_log("CandidatosDAO::aprobar: " . $e->getMessage());
        }
        return $aprobado;
    }

    // CRUD

    public function actualizarUser(Usuario $u, int $id): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE candidatos SET nombre = :nombre, ap1 = :ap1, ap2 = :ap2, alta = :alta, saldo = :saldo,
                    tel = :tel, rol = :rol, email = :email, foto = :foto WHERE id = :id"
        );
        return $stmt->execute([
            ":nombre" => $u->getNombre(), ":ap1" => $u->getAp1(), ":ap2" => $u->getAp2(),
            ":alta" => $u->getAlta(), ":saldo" => $u->getSaldo(), ":tel" => $u->getTelefono(),
            ":rol" => $u->getRol(), ":email" => $u->getEmail(), ":foto" => $u->getFoto(),
            ":id" => $id
        ]);
    }

    public function eliminarUser(Usuario $u): bool {
        return $this->eliminarUserId($u->getId());
    }

    public function eliminarUserId(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM serviciosCandidatos WHERE id_candidato = :id");
        $stmt->execute([":id" => $id]);

        $stmt = $this->pdo->prepare("DELETE FROM candidatos WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function actualizarFoto(int $id, ?string $foto): bool {
        $stmt = $this->pdo->prepare("UPDATE candidatos SET foto = :foto WHERE id = :id");
        return $stmt->execute([":foto" => $foto, ":id" => $id]);
    }

    public function actualizarPdf(int $id, ?string $pdf): bool{
        $sentencia = "UPDATE candidatos SET pdf = :pdf WHERE id = :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":pdf" => $pdf, ":id" => $id]);
    }

    //QUERY 

    public function findAll(): array {
        $resultado = $this->pdo->query(
            "SELECT id, nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, foto, pdf
             FROM candidatos ORDER BY alta DESC"
        );
        $usuarios = [];
        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $usuarios[] = $this->filaToUser($fila);
        }
        return $usuarios;
    }

    public function findById(int $id): ?Usuario {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, foto, pdf
             FROM candidatos WHERE id = :id"
        );
        $stmt->execute([":id" => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        $usuario = null;
        if ($fila !== false) {
            $usuario = $this->filaToUser($fila);
        }
        return $usuario;
    }

    public function findByEmail(string $email): ?Usuario {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, foto, pdf
             FROM candidatos WHERE email = :email"
        );
        $stmt->execute([":email" => $email]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        $usuario = null;
        if ($fila !== false) {
            $usuario = $this->filaToUser($fila);
        }
        return $usuario;
    }

    public function filaToUser(array $datos): Usuario {
        return new Usuario(
            nombre: $datos['nombre'],
            ap1: $datos['ap1'],
            ap2: $datos['ap2'],
            alta: $datos['alta'],
            telefono: $datos['tel'],
            saldo: $datos['saldo'],
            rol: $datos['rol'],
            email: $datos['email'],
            contrasenia: $datos['contrasenia'],
            pdf: $datos['pdf'] ?? null,
            foto: $datos['foto'] ?? null,
            id: (int)$datos['id']
        );
    }
}