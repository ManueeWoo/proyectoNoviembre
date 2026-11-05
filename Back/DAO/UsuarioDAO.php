<?php
namespace App\DAO;
use App\DAO\ConexionDB;
use App\Modelo\Usuario;
use PDO;
class UsuarioDAO{
private PDO $pdo;

    public function __construct(){
        $this->pdo = ConexionDB::getConnection();//esto lo hago para q cada vez que hablo con la bd no hacer $conexion getpdo sino directo pdo
    }

    //CRUD
    public function insertarUser(Usuario $u):bool{
        $contraHash = password_hash($u->getContra(), PASSWORD_DEFAULT);
        $sentencia = "INSERT INTO usuario (nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia) VALUES (:nombre, :ap1, :ap2, :alta, :saldo, :tel, :rol, :email, :contrasenia)";
        $pStatment = $this->pdo->prepare($sentencia);
        $esCorrecto = $pStatment->execute([":nombre"=>$u->getNombre(), ":ap1"=>$u->getAp1(), ":ap2"=>$u->getAp2(), ":alta"=>$u->getAlta(), ":saldo"=>$u->getSaldo(), ":tel"=>$u->getTelefono(), ":rol"=>$u->getRol(), ":email"=>$u->getEmail(), ":contrasenia"=>$contraHash]); 
        if($esCorrecto){
            $u->setId($this->pdo->lastInsertId());
        }
        return $esCorrecto;
    }

    public function actualizarUser(Usuario $u, int $id):bool{
        $contraHash = password_hash($u->getContra(), PASSWORD_DEFAULT);
        $sentencia = "UPDATE usuario SET nombre= :nombre, ap1 = :ap1, ap2 = :ap2, alta = :alta, saldo = :saldo, tel = :tel, rol = :rol, email = :email, contrasenia = :contrasenia WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":nombre"=>$u->getNombre(), ":ap1"=>$u->getAp1(), ":ap2"=>$u->getAp2(), ":alta"=>$u->getAlta(), ":saldo"=>$u->getSaldo(), ":tel"=>$u->getTelefono(), ":rol"=>$u->getRol(), ":email"=>$u->getEmail(),":contrasenia"=>$contraHash, ":id"=>$id]);
    }

    public function eliminarUser(Usuario $u):bool{
        $sentencia = "DELETE FROM usuario WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$u->getId()]); 
    }

    public function eliminarUserId(int $id):bool{
        $sentencia = "DELETE FROM usuario WHERE id= :id";
        $pStatment = $this->pdo->prepare($sentencia);
        return $pStatment->execute([":id"=>$id]); 
    }

    //QUERY

     public function findAll(): array{
        $resultado= $this->pdo->query("SELECT nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, id FROM usuario");
        $usuarios = [];
        foreach($resultado->fetchAll() as $fila){
            $usuario = $this->filaToUser($fila); 
            array_push($usuarios,$usuario);
        }
        return $usuarios;
    }

    public function findById(int $id): ?Usuario{
        $resultado= $this->pdo->prepare("SELECT nombre, ap1, ap2, alta, saldo, tel, rol, email, contrasenia, id FROM usuario WHERE id=:id");
        $valoresUser = null;
        $resultado->execute([":id"=>$id]);
        $valoresUser = $resultado->fetch(PDO::FETCH_ASSOC);
        $user = null;
        if($valoresUser === false){
            $user = null;
        }else{
            $user =$this->filaToUser($valoresUser);
        }
        return $user;
    }

    public function filaToUser(array $datos): Usuario{
        return new Usuario($datos['nombre'], $datos['ap1'], $datos['ap2'], $datos['alta'], $datos['saldo'], $datos['tel'], $datos['rol'], $datos['email'], $datos['contrasenia'], $datos['id']); //asocio la fila al array de Prod
    }
}