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
        echo $this->twig->render("contenidos/verPerfil.html.twig", ["usuario" => $usuario]);
    }

    public function guardarFoto(){
        $u = Sesion::dameLaSesion("user");


    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

            $formatos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

            // Comprobar el tipo REAL del archivo no solo el nombre
            $tipo = mime_content_type($_FILES['foto']['tmp_name']);
            if (!isset($formatos[$tipo])) {
                $errores["formato"] = "Formato no permitido";
            }

            //nombre pa guardarlo en upload y en la bd
            $nombreFoto = 'usuario_' . $u->getId() . '.' . $formatos[$tipo];
            $destino = __DIR__ . '/../Public/upload/' . $nombreFoto;

            //muevo la foto a la carpeta de upload
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                $repo = new UsuarioDAO();
                $u->setFoto($nombreFoto);  
            } 
        }
        if($repo->actualizarFoto($u->getId(), $nombreFoto)){
            header("Location: /verperfil");
            exit;
        }
    }
}
