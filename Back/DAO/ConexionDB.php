<?php
namespace App\DAO;
use PDO;
use PDOException;
use RuntimeException;
class ConexionDB{
    private string $host;
    private string $user;
    private string $pass;
    private string $db;
    private PDO $pdo; 
    private static ?ConexionDB $con=null;

    public function __construct(){
        $this->host=getenv('DB_HOST');
        $this->user=getenv('DB_USER');
        $this->pass=getenv('DB_PASSWORD');
        $this->db=getenv('DB_NAME');
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->db;charset=utf8mb4"; 
            $this->pdo= new PDO($dsn, $this->user, $this->pass);
        } catch (PDOException $e) {
            throw new RuntimeException("Error de conexion: " . $e->getMessage());
        }
    }

    public static function getConnection(): PDO{
        if(self::$con === null){
            $con = new ConexionDB();
        }
        return $con->pdo;
        
    }

}
