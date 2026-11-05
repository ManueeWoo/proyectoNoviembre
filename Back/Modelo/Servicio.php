<?php
namespace App\Modelo;
use App\Modelo\Categoria;
class Servicio
{
    public function __construct(
        private Categoria $cat,
        private string $titulo,
        private string $decripcion,
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

    public function getCat(): Categoria
    {
        return $this->cat;
    }

    public function setCat(Categoria $cat): void
    {
        $this->cat = $cat;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getDecripcion(): string
    {
        return $this->decripcion;
    }

    public function setDecripcion(string $decripcion): void
    {
        $this->decripcion = $decripcion;
    }
}