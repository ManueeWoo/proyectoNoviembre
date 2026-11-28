<?php
namespace App\Controladores;

use App\DAO\CandidatosDAO;
use App\DAO\UsuarioDAO;
use App\Helpers\Sesion;
use Twig\Environment;
use App\Services\MailService;
use App\Services\PdfService;
use App\Services\ConfigMail;
use App\Modelo\Usuario;
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

    public function pintarEdicion(Usuario $editado, array $valores, array $errores): void {
        echo $this->twig->render("contenidos/editarUsuario.html.twig", [
            "editado" => $editado,
            "valores" => $valores,
            "errores" => $errores,
        ]);
    }

    public function formEditarUsuario(int $id) {
     $user = Sesion::dameLaSesion("user");
        if($user->getRol()!=="admin"){
            header("Location: /login");
        }else{
    $errores=[];
    $editado = (new UsuarioDAO())->findById($id);
    if ($editado === null) {
        $errores["no"] = "no se puede editar";
    }

    $valores = [
        "nombre"   => $editado->getNombre(),
        "ap1"      => $editado->getAp1(),
        "ap2"      => $editado->getAp2(),
        "telefono" => $editado->getTelefono(),
        "email"    => $editado->getEmail(),
        "rol"      => $editado->getRol(),
        "saldo"    => $editado->getSaldo(),
    ];
    $this->pintarEdicion($editado, $valores, $errores);
    }
}

public function guardarEdicionUsuario(int $id) {
     $user = Sesion::dameLaSesion("user");
        if($user->getRol()!=="admin"){
            header("Location: /login");
        }else{

    $dao = new UsuarioDAO();

    $editado = $dao->findById($id);
    if ($editado === null) {
        $errores["no"] = "no se puede editar";
    }

    $valores = [
        "nombre"   => trim($_POST['nombre'] ?? ''),
        "ap1"      => trim($_POST['ap1'] ?? ''),
        "ap2"      => trim($_POST['ap2'] ?? ''),
        "telefono" => trim($_POST['telefono'] ?? ''),
        "email"    => trim($_POST['email'] ?? ''),
        "rol"      => $_POST['rol'] ?? '',
        "saldo"    => trim($_POST['saldo'] ?? ''),
    ];

    if ($id === $user->getId()) {
        $valores["rol"] = $editado->getRol();
    }
    if ($valores["nombre"] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }
    if ($valores["ap1"] === '') {
        $errores['ap1'] = 'El primer apellido es obligatorio.';
    }
    if (!preg_match('/^[0-9+ ]{9,15}$/', $valores["telefono"])) {
        $errores['telefono'] = 'El teléfono no es válido.';
    }
    if (!in_array($valores["rol"], ['usuario', 'admin'], true)) {
        $errores['rol'] = 'El rol no es válido.';
    }

    if ((int)$valores["saldo"]  < 0) {
        $errores['saldo'] = 'El saldo debe ser un número entero, 0 o mayor.';
    }

    if (!filter_var($valores["email"], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El email no es válido.';
    } else {
        $otro = $dao->findByEmail($valores["email"]);
        if (($otro !== null && $otro->getId() !== $id)
            || (new CandidatosDAO())->findByEmail($valores["email"]) !== null) {
            $errores['email'] = 'Ya existe otro registro con ese email.';
        }
    }

    if (empty($errores)) {
        $guardado = $dao->actualizarUserDesdeAdmin(
            $id, $valores["nombre"], $valores["ap1"], $valores["ap2"],
            $valores["telefono"], $valores["email"], $valores["rol"], (int)$valores["saldo"]
        );
        if ($guardado) {
            header("Location: /adminUsers");
            exit;
        }
        $errores['general'] = 'No se pudo guardar el usuario.';
    }

    $this->pintarEdicion($editado, $valores, $errores);
}
}

}