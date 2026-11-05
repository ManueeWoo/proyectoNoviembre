<?php
namespace App\Modelo;
use App\Modelo\Servicio;
use App\Modelo\Usuario;
use DateTime;

class Oferta
{
    public function __construct(
        private Servicio $servicio,
        private Usuario $usuario,
        private int $puntos,
        private string $descripcion,
        private string $tiempoEstimado,
        private ?int $id
    ) {
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getServicio(): Servicio
    {
        return $this->servicio;
    }

    public function setServicio(Servicio $servicio): void
    {
        $this->servicio = $servicio;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function getPuntos(): int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): void
    {
        $this->puntos = $puntos;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getTiempoEstimado(): string
    {
        return $this->tiempoEstimado;
    }

    public function setTiempoEstimado(string $tiempoEstimado): void
    {
        $this->tiempoEstimado = $tiempoEstimado;
    }
}