<?php
namespace App\Modelo;
//controlar que puntuacion sea entre 1 y 5
class Valoracion
{
    public function __construct(
        private int $puntuacion,
        private string $descripcion,
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

    public function getPuntuacion(): int
    {
        return $this->puntuacion;
    }

    public function setPuntuacion(int $puntuacion): void
    {
        $this->puntuacion = $puntuacion;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }
}