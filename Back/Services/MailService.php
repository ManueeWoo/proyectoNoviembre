<?php

namespace App\Services;

use App\Modelo\Usuario;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public function __construct(
        private PdfService $pdfService,
        private array $smtpConfig,
        private string $baseUrl = 'https://truequeapp.com' //preguntar que pongo yo 
    ) {}

    private function renderTemplate(string $templatePath, array $data): string
    {
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    private function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $this->smtpConfig['host'];
        $mail->Port       = $this->smtpConfig['port'];

        // Autenticación opcional para entorno dev / prod
        $mail->SMTPAuth   = !empty($this->smtpConfig['username']);
        if ($mail->SMTPAuth) {
            $mail->Username = $this->smtpConfig['username'];
            $mail->Password = $this->smtpConfig['password'];
        }

        // Cifrado opcional
        if (!empty($this->smtpConfig['encryption'])) {
            $mail->SMTPSecure = $this->smtpConfig['encryption'];
        } else {
            $mail->SMTPSecure  = false;
            $mail->SMTPAutoTLS = false;
        }

        $mail->CharSet    = 'UTF-8';
        $mail->setFrom($this->smtpConfig['from_email'], $this->smtpConfig['from_name']);
        $mail->isHTML(true);

        return $mail;
    }

    /**
     * Correo 2: Registro realizado, pendiente de aprobación del admin.
     */
    public function sendPendingRegistrationEmail(Usuario $user): bool
    {
        try {
            $body = $this->renderTemplate(__DIR__ . '/../Plantillas/emails/pendienteAceptacion.php', [
                'user'      => $user,
                'actionUrl' => $this->baseUrl . '/estado-cuenta'
            ]);

            $mail = $this->createMailer();
            $mail->addAddress($user->getEmail(), $user->getNombre());
            $mail->Subject = 'Registro completado - En espera de confirmación';

            // Adjuntar imagen local embebida inline
            $mail->addEmbeddedImage(
                __DIR__ . '/../Public/upload/logo.png',
                'banner_cid',
                'welcome-banner.png'
            );

            $mail->Body = $body;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar email de registro pendiente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Correo 3: Confirmación de registro por parte del administrador.
     */
    public function sendRegistrationApprovedEmail(Usuario $user): bool
    {
        try {
            $body = $this->renderTemplate(__DIR__ . '/../Plantillas/emails/aceptacionAprobada.php', [
                'user'      => $user,
                'actionUrl' => $this->baseUrl . '/login'
            ]);

            $mail = $this->createMailer();
            $mail->addAddress($user->getEmail(), $user->getNombre());
            $mail->Subject = '¡Tu cuenta en Trueque O Trato ha sido activada!';

            // Adjuntar imagen local embebida inline
            $mail->addEmbeddedImage(
                __DIR__ . '/../Public/upload/logo.png',
                'badge_cid',
                'verified-badge.png'
            );

            $mail->Body = $body;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar email de confirmación de registro: " . $e->getMessage());
            return false;
        }
    }
}
