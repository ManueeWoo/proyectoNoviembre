<?php
namespace App\Services;
class ConfigMail{
    public function __construct(
        private string $host = "mailhog",
        private int $puerto = 1025,
        private string $user = "",
        private string $contrasenia= "",
        private string $encriptado= "",
        private string $destino= "no-constestar@truequeotrato.com",
        private string $origen="Plataforma Trueque O trato"
    ){
    }

    public function getConfig(): array{
        return  [
            'host'       => $this->host, 
            'port'       => $this->puerto,
            'username'   => $this->user,
            'password'   => $this->contrasenia,
            'encryption' => $this->encriptado,
            'from_email' => $this->destino,
            'from_name'  => $this->origen
        ];
    }
}