<?php
namespace App\Modelo;
use App\Modelo\Categoria;
class Servicio
{
    public function __construct(
        private Categoria $cat,
        private string $titulo,
        private string $descripcion,
        private ?int $id = null,
        private ?int $ofertas = null
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

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $decripcion): void
    {
        $this->descripcion = $decripcion;
    }

     public function getOfertas(): ?int
    {
        return $this->ofertas;
    }

    public function setOfertas(int $ofertas): void
    {
        $this->ofertas = $ofertas;
    }
}