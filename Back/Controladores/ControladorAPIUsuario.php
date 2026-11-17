<?php
namespace App\Controladores;

use App\DAO\CandidatosDAO;
use App\DAO\CategoriaDAO;
use App\DAO\ServicioDAO;
use App\DAO\UsuarioDAO;
use App\Helpers\Respondedor;
use App\Helpers\Sesion;
use App\Modelo\Usuario;
use App\Services\ConfigMail;
use App\Services\MailService;
use App\Services\PdfService;
use DateTime;

class ControladorAPIUsuario {

    public function darCategorias() {
        $categorias = (new CategoriaDAO())->findAll();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($categorias);
    }

    public function darServicios(int $id) {
        $servicios = (new ServicioDAO())->findByIdCat($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($servicios);
    }

    public function registrar() {
        $statusCode = 200;
        $errores = [];
        $candidato = null;

        try {
            if (Sesion::dameLaSesion("user") !== null) {
                $errores['existe'] = 'Ya estas registrado';
            } else {
                $nombre    = trim($_POST['nombre'] ?? '');
                $ape1      = trim($_POST['ape1'] ?? '');
                $ape2      = trim($_POST['ape2'] ?? '');
                $email     = trim($_POST['email'] ?? '');
                $telefono  = trim($_POST['telefono'] ?? '');
                $passwd1   = trim($_POST['passwd1'] ?? '');
                $passwd2   = trim($_POST['passwd2'] ?? '');
                $servicios = (array)($_POST['servicios'] ?? []);

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
                if ($passwd1 !== $passwd2 || $passwd1 === '') {
                    $errores['passwd1'] = 'Las contraseñas no coinciden o están vacías';
                }
                if (empty($errores)) {
                    if ((new UsuarioDAO())->findByEmail($email) !== null
                        || (new CandidatosDAO())->findByEmail($email) !== null) {
                        $errores['email'] = 'Ya existe un registro con ese email.';
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
                    $idsServicios = [];
                    foreach ($servicios as $idServicio) {
                        $idS = (int)$idServicio;
                        if ((new ServicioDAO())->findById($idS) !== null) {
                            $idsServicios[$idS] = $idS;
                        }
                    }
                    $nombrePdf = "pdf_". $email . ".pdf";

                    $candidato = new Usuario(
                        $nombre, $ape1, $ape2,
                        (new DateTime())->format('Y-m-d'),
                        $telefono, 150, "usuario", $email,
                        $passwd1,
                        $nombrePdf
                    );

                    $dao = new CandidatosDAO();
                    $idCandidato = $dao->registrar($candidato, array_values($idsServicios));

                    if ($idCandidato === false) {
                        $errores['general'] = 'No se pudo completar el registro.';
                    } elseif ($pdfSubido) {
                        $carpeta = __DIR__ . '/../Public/upload/pdf/';
                        if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $carpeta . $nombrePdf)) {
                            $dao->eliminarUserId($idCandidato); 
                            $errores['pdf'] = 'No se pudo guardar el PDF.';
                        }
                    }
                }
            }
            if (empty($errores) && $candidato !== null) {
                    $mailService = new MailService(new PdfService(), (new ConfigMail())->getConfig());
                    $mailService->sendPendingRegistrationEmail($candidato);
            }
        } catch (\Throwable $e) {
            $errores['general'] = 'No se pudo completar el registro. Inténtalo de nuevo.';
        }

        if (!empty($errores)) {
            $statusCode = 400;
        }
        Respondedor::jsonResponse(['ok' => empty($errores), 'errores' => $errores], $statusCode);
    }
}