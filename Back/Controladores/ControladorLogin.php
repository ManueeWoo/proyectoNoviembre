<?php
namespace App\Controladores;

use App\DAO\UsuarioDAO;
use App\Helpers\Sesion;
use Twig\Environment;
use App\Modelo\Usuario;
use App\Helpers\Login;
class ControladorLogin{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }

    public function sacarLogin(){
        $errores=[];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $contra = $_POST['contrasenia'];

        $intento = new Usuario('', '', '', '', 0, 0, '', $email, $contra,'');
        $errores = Login::logearse($intento);

        if (empty($errores)) {
            header("Location: /");
            exit;
        }   
    }
    echo $this->twig->render('contenidos/login.html.twig', ['errores' => $errores]); 
    }

    public function cerrarSesion(){
        if(Login::deslogear()){
            header("Location: /");
            exit;
        }
    }

    public function verPerfil(){
    $usuario = Sesion::dameLaSesion("user");

    echo $this->twig->render("contenidos/verPerfil.html.twig", [
        "usuario" => $usuario,
        "editar" => false,
        "errores" => [],
        "valores" => []
    ]);
}

   public function guardarPerfil(){

    $u = Sesion::dameLaSesion("user");

    $repo = new UsuarioDAO();
    $errores = [];

    $valores = [
        "nombre" => trim($_POST['nombre'] ?? ''),
        "ap1" => trim($_POST['ap1'] ?? ''),
        "ap2" => trim($_POST['ap2'] ?? ''),
        "telefono" => trim($_POST['telefono'] ?? '')
    ];

    // Validaciones
    if ($valores["nombre"] === '') {
        $errores[] = 'El nombre es obligatorio.';
    }

    if ($valores["ap1"] === '') {
        $errores[] = 'El primer apellido es obligatorio.';
    }

    if (!preg_match('/^[0-9+ ]{9,15}$/', $valores["telefono"])) {
        $errores[] = 'El teléfono no es válido.';
    }

    if (isset($_FILES['foto']) &&
        $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

        $formatos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        $tipo = mime_content_type($_FILES['foto']['tmp_name']);

        if (!isset($formatos[$tipo])) {

            $errores[] = "Formato de imagen no permitido.";

        } else {

            $nombreFoto = 'usuario_' . $u->getId() . '.' . $formatos[$tipo];

            $destino = __DIR__ . '/../Public/upload/' . $nombreFoto;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {

                $u->setFoto($nombreFoto);

                if (!$repo->actualizarFoto($u->getId(), $nombreFoto)) {
                    $errores[] = "No se pudo actualizar la foto en la base de datos.";
                }

            } else {
                $errores[] = "Error al subir la fotografía.";
            }
        }
    }

    if (!empty($errores)) {

        echo $this->twig->render("contenidos/verPerfil.html.twig", [
            "usuario" => $u,
            "editar" => true,
            "errores" => $errores,
            "valores" => $valores
        ]);

        return;
    }

    if ($repo->actualizarPerfil(
        $u->getId(),
        $valores["nombre"],
        $valores["ap1"],
        $valores["ap2"],
        $valores["telefono"]
    )) {
 $u->setNombre($valores["nombre"]);
    $u->setAp1($valores["ap1"]);
    $u->setAp2($valores["ap2"]);
    $u->setTelefono($valores["telefono"]);
        header("Location: /verperfil");
        exit;

    } else {

        echo $this->twig->render("contenidos/verPerfil.html.twig", [
            "usuario" => $u,
            "editar" => true,
            "errores" => ["No se pudieron actualizar los datos."],
            "valores" => $valores
        ]);
    }
}
}
