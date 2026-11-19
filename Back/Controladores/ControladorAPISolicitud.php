<?php
namespace App\Controladores;
use App\DAO\OfertaDAO;
use App\DAO\UsuarioDAO;
use App\DAO\SolicitudDAO;
use App\Modelo\Solicitud;
use App\Helpers\Sesion;
use App\Helpers\Respondedor;
use DateTime;
use PDO;
USE App\DAO\ConexionDB;
class ControladorAPISolicitud{
    private PDO $pdo;
    public function __construct(){
        $this->pdo = ConexionDB::getConnection();
    }


    public function hacerSolicitud() {
        $statusCode = 200;
        $errores = [];
        $user = Sesion::dameLaSesion("user");

        if ($user === null) {
            $statusCode = 401;
            $errores['login'] = 'Inicia sesión para solicitar un servicio.';
        } else {
            $solicitante = (new UsuarioDAO())->findById($user->getId());
            $oferta = (new OfertaDAO())->findById((int)($_POST['id_oferta'] ?? 0));
            $dao = new SolicitudDAO();

            if ($oferta === null || !$oferta->getActivo()) {
                $errores['oferta'] = 'La oferta no está disponible.';
            } elseif ($oferta->getUsuario()->getId() === $solicitante->getId()) {
                $errores['propia'] = 'No puedes solicitar tu propia oferta.';
            } elseif ($solicitante->getSaldo() < $oferta->getPuntos()) {
                $errores['saldo'] = 'No tienes puntos suficientes.';
            } elseif ($dao->existe($solicitante->getId(), $oferta->getId())) {
                $errores['repetida'] = 'Ya has solicitado esta oferta.';
            }

            if (empty($errores)) {
                $solicitud = new Solicitud(
                    $solicitante,
                    $oferta,
                    (new DateTime())->format('Y-m-d'),
                    $oferta->getPuntos()
                );
                if (!$dao->insertarSolicitud($solicitud)) {
                    $errores['general'] = 'No se pudo guardar la solicitud.';
                }
            }
        }

        if (!empty($errores) && $statusCode === 200) {
            $statusCode = 400;
        }
        Respondedor::jsonResponse(['ok' => empty($errores), 'errores' => $errores], $statusCode);
    }

    public function misSolicitudes() {
        $statusCode = 200;
        $datos = ['recibidas' => [], 'enviadas' => []];
        $sesion = Sesion::dameLaSesion("user");

        if ($sesion === null) {
            $statusCode = 401;
        } else {
            $dao = new SolicitudDAO();
            $datos['recibidas'] = $this->formatear($dao->findSolicitudUser($sesion->getId(), true));
            $datos['enviadas']  = $this->formatear($dao->findSolicitudUser($sesion->getId(), false));
        }
        Respondedor::jsonResponse($datos, $statusCode);
    }

// tng q foramtear para q no salgan campos como el email, contraseña solo loq quiero
private function formatear(array $filas): array {
    $resultado = [];
    foreach ($filas as $f) {
        $resultado[] = [
            'id'      => (int)$f['id'],
            'estado'  => $f['estado'],
            'fecha'   => $f['fecha'],
            'puntos'  => (int)$f['puntos'],
            'oferta'  => [
                'id'          => (int)$f['oferta_id'],
                'titulo'      => $f['servicio'],
                'descripcion' => $f['descripcion'],
            ],
            'persona' => [
                'id'     => (int)$f['persona_id'],
                'nombre' => $f['persona_nombre'],
                'foto'   => $f['persona_foto'],
            ],
        ];
    }
    return $resultado;
}

public function cambiarEstadoSolicitud() {
    $statusCode = 200;
    $errores = [];
    $sesion = Sesion::dameLaSesion("user");

    try {
        if ($sesion === null) {
            $statusCode = 401;
            $errores['login'] = 'Inicia sesión para continuar.';
        } else {
            $dao = new SolicitudDAO();
            $idEstadoUserProfesional = $dao->findSolicitudUserYProfesional((int)($_POST['id'] ?? 0));
            $accion = $_POST['accion'] ?? '';
            $nuevos = ['aceptar' => 'aceptado', 'rechazar' => 'rechazado', 'cancelar' => 'cancelado'];
            $nuevo = $nuevos[$accion] ?? null;

            if ($idEstadoUserProfesional === null || $nuevo === null) {
                $errores['solicitud'] = 'La solicitud no existe.';
            } elseif ($idEstadoUserProfesional['estado'] !== 'solicitado') {
                $errores['estado'] = 'Esta solicitud ya no se puede modificar.';
            } else {
                $profesional = (int)$idEstadoUserProfesional['id_dueno'] === $sesion->getId();
                $cliente = (int)$idEstadoUserProfesional['id_user'] === $sesion->getId();
                // comprobar para q el dueño pueda aceptar o rechazar y el cliente solo cancelar
                $permitido = ($accion === 'cancelar' && $cliente)|| (($accion === 'aceptar' || $accion === 'rechazar') && $profesional);
                if (!$permitido) {
                    $errores['permiso'] = 'No puedes realizar esta acción.';
                } elseif (!$dao->cambiarEstado((int)$idEstadoUserProfesional['id'], $nuevo)) {
                    $errores['general'] = 'No se pudo actualizar la solicitud.';
                }
            }
        }
    } catch (\Throwable $e) {
        $errores['general'] = 'No se pudo completar la acción.';
    }

    if (!empty($errores) && $statusCode === 200) {
        $statusCode = 400;
    }
    Respondedor::jsonResponse(['ok' => empty($errores), 'errores' => $errores], $statusCode);
}
 
}
