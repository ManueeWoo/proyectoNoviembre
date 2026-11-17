<?php
namespace App\Controladores;

use App\DAO\CandidatosDAO;
use App\DAO\UsuarioDAO;
use App\Helpers\Sesion;
use Twig\Environment;
use App\Services\MailService;
use App\Services\PdfService;
use App\Services\ConfigMail;
class ControladorAdmin{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }
    public function sacarPanelUsers(){
        $usuarios = ((new UsuarioDAO())->findAll());
        $candidatos = ((new CandidatosDAO())->findAll());
        echo $this->twig->render("contenidos/adminUsuarios.html.twig", ["usuarios"=>$usuarios , "pendientes"=>$candidatos]);
    }

    public function borrarUsuario(int $id){
        $user = Sesion::dameLaSesion("user");
        if($user->getRol()!=="admin"){
            header("Location: /login");
        }else{
            ((new UsuarioDAO())->eliminarUserId($id));
            header("Location: /adminUsers");
        }
    }

    public function aceptarUser(int $id) {
        $user = Sesion::dameLaSesion("user");
        $destino = "/adminUsers";

        if ($user === null || $user->getRol() !== "admin") {
            $destino = "/login";
        } else {
            $dao = new CandidatosDAO();
            $candidato = $dao->findById($id);  

            if ($candidato !== null && $dao->aprobar($id)) {
                $mail = new MailService(new PdfService(), (new ConfigMail())->getConfig());
                $mail->sendRegistrationApprovedEmail($candidato);
            }
        }
        header("Location: " . $destino);
        exit;
    }

    public function rechazarUser(int $id) {
        $user = Sesion::dameLaSesion("user");
        $destino = "/adminUsers";

        if ($user === null || $user->getRol() !== "admin") {
            $destino = "/login";
        } else {
            $dao = new CandidatosDAO();
            $candidato = $dao->findById($id);  

            if ($candidato !== null && $dao->eliminarUserId($id)) {
                $mail = new MailService(new PdfService(), (new ConfigMail())->getConfig());
                $mail->sendRegistrationRechazedEmail($candidato);//enviar email de rechazo
            }
        }
        header("Location: " . $destino);
        exit;
    }
}