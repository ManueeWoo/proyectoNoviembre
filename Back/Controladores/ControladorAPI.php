<?php
namespace App\Controladores;
use App\DAO\AccesoServicioDAO;
use App\DAO\CategoriaDAO;
use App\DAO\ServicioDAO;
use App\DAO\UsuarioDAO;
use App\Helpers\Respondedor;
use App\Helpers\Sesion;
use App\Modelo\AccesoServicio;
use App\Modelo\Usuario;
use DateTime;
class ControladorAPI{

    public function __construct(){

    }

    public function darCategorias() {
        $repo = new CategoriaDAO();
        $categorias = $repo->findAll();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($categorias);
    }

    public function darServicios(int $id) {
        $repo = new ServicioDAO();
        $servicios = $repo->findByIdCat($id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($servicios);
    }

    public function registrar(){
          $statusCode = 200;
        $datos = json_decode(file_get_contents("php://input"), true);
        $errores = [];
        $usuario = Sesion::dameLaSesion("user");

        if ($usuario !== null) {
            $errores['existe'] = 'Ya estas registrado';
        } else {
            $nombre   = trim($datos['nombre'] ?? '');
            $ape1     = trim($datos['ape1'] ?? '');
            $ape2     = trim($datos['ape2'] ?? '');
            $email    = trim($datos['email'] ?? '');
            $telefono = trim($datos['telefono'] ?? '');
            $passwd1  = trim($datos['passwd1'] ?? '');
            $passwd2  = trim($datos['passwd2'] ?? '');

            if ($nombre === '') {
                $errores['nombre'] = 'El nombre es obligatorio.';
            }
            if ($ape1 === '') {
                $errores['ape1'] = 'El primer apellido es obligatorio.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El email no es válido.';
            }
            if (!preg_match('/^[0-9+ ]{9,15}$/', $telefono)) {
                $errores['telefono'] = 'El teléfono no es válido.';
            }

            if (($passwd1 !== $passwd2) || ($passwd1 === '')) {
                $errores['passwd1'] = 'Las contraseñas no coinciden o están vacías';
            }


            if (empty($errores)) {
                if (((new UsuarioDAO())->findByEmail($email)) !== null) {
                    $errores['email'] = 'Ya existe un usuario con ese email.';
                }
            }

            if (empty($errores)) {
                $usuario = new Usuario(
                    $nombre,
                    $ape1,
                    $ape2,
                    (new DateTime())->format('Y-m-d'),
                    $telefono,
                    150,
                    "usuario",
                    $email,
                    $passwd1,         
                );

                $usuario = ((new UsuarioDAO())->insertarUser($usuario));
                if ($usuario == null) {
                    $errores['general'] = 'No se pudo crear el usuario.';
                }else {
                    foreach ($datos['servicios'] ?? [] as $idServicio) {
                        $s = (new ServicioDAO())->findById((int)$idServicio);
                        if ($s !== null) {
                            (new AccesoServicioDAO())->insertarAcceso(new AccesoServicio($usuario, $s));
                        }
                    }
                }
                
            }
        }
        if (!empty($errores)) {
            $statusCode = 400; 
        }
        Respondedor::jsonResponse(['ok' => empty($errores), 'errores' => $errores], $statusCode);
    }

}