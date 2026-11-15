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
use App\Services\ConfigMail;
use App\Services\MailService;
use App\Services\PdfService;
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
        $errores = [];
        $usuario = Sesion::dameLaSesion("user");

        if ($usuario !== null) {
            $errores['existe'] = 'Ya estas registrado';
        } else {
            $nombre   = trim($_POST['nombre'] ?? '');
            $ape1     = trim($_POST['ape1'] ?? '');
            $ape2     = trim($_POST['ape2'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $passwd1  = trim($_POST['passwd1'] ?? '');
            $passwd2  = trim($_POST['passwd2'] ?? '');
            $servicios = $_POST['servicios'] ?? [];

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

            $pdfSubido = isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE;
                if ($pdfSubido) {
                    if ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
                        $errores['pdf'] = 'Error al subir el PDF.';
                    } elseif (mime_content_type($_FILES['pdf']['tmp_name']) !== 'application/pdf') {
                        $errores['pdf'] = 'El archivo debe ser un PDF.';
                    } elseif ($_FILES['pdf']['size'] > 2 * 1024 * 1024) {
                        $errores['pdf'] = 'El PDF no puede superar 2 MB.';
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
                    foreach ($servicios as $idServicio) {
                        $s = (new ServicioDAO())->findById((int)$idServicio);
                        if ($s !== null) {
                            (new AccesoServicioDAO())->insertarAcceso(new AccesoServicio($usuario, $s));
                        }
                    }
                    if ($pdfSubido) {
                    $nombrePdf = 'pdf_' . $usuario->getId() . '.pdf';
                    $destino = __DIR__ . '/../Public/upload/pdf/' . $nombrePdf;
                    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $destino)) {
                        (new UsuarioDAO())->actualizarPdf($usuario->getId(), $nombrePdf);
                    } else {
                        $errores['pdf'] = 'No se pudo guardar el PDF.';
                    }
                }
                }
                
            }
        }
        if (!empty($errores)) {
            $statusCode = 400; 
        }else{
            $pdfService = new PdfService();
            $configMail = new ConfigMail();
            $mailService = new MailService($pdfService,$configMail->getConfig());
            $mailService->sendPendingRegistrationEmail($usuario);
        }
        Respondedor::jsonResponse(['ok' => empty($errores), 'errores' => $errores], $statusCode);
    }
}