<?php
namespace App\Modelo;
use App\Modelo\EstadoAcceso;
use App\Modelo\Usuario;
use App\Modelo\Servicio;
class AccesoServicio
{
    public function __construct(
        private Usuario $usuario,
        private Servicio $servicio,
        private ?EstadoAcceso $estado = EstadoAcceso::Pendiente
    ) {
    }
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function getServicio(): Servicio
    {
        return $this->servicio;
    }

    public function setServicio(Servicio $servicio): void
    {
        $this->servicio = $servicio;
    }

    public function getEstado(): EstadoAcceso
    {
        return $this->estado;
    }

    public function setEstado(EstadoAcceso $estado): void
    {
        $this->estado = $estado;
    }
}