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
                // Fecha y puntos los pone el servidor
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
 
}
