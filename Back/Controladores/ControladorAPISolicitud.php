<?php
namespace App\Controladores;
use PDO;
USE App\DAO\ConexionDB;
class ControladorAPISolicitud{
    private PDO $pdo;
    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }

    public function hacerSolicitud(){
        try {
            $this->pdo->beginTransaction();
        } catch (\Throwable $e) {
            if($this->pdo->inTransaction()){
                $this->pdo->rollBack();
            }
        }
    }
 
}
