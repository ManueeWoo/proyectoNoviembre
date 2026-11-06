<?php
namespace App\Controladores;

use Twig\Environment;
use App\Modelo\Usuario;
use App\DAO\UsuarioDAO;
use App\Helpers\Login;
class ControladorLogin{
    private Environment $twig;
    public function __construct(Environment $twig){
        $this->twig= $twig;
    }

    public function sacarLogin(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $contra = $_POST['contrasenia'];

            $intento = new Usuario('', '', '', '', 0, 0, '', $email, $contra);
            $dao = new UsuarioDAO();
            $usuario = $dao->findByEmailContra($intento);

            //comprobar lo del array de errores y el addGlobal
            if ($usuario !== null) {
                Login::logearse($usuario);
                $this->twig->addGlobal("usuario","usuario");
                header("Location: /");
                exit;
            }
        }
        echo $this->twig->render('contenidos/login.html.twig', ['error' => 'Email o contraseña incorrectos']);
    }
}
