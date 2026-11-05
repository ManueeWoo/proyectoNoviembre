<?php
namespace App\Modelo;

use DateTime;
class Usuario
{
    public function __construct(
        private string $nombre,
        private string $ap1,
        private string $ap2,
        private string $alta,
        private string $telefono,
        private int $saldo,
        private string $rol,
        private string $email,
        private string $contrasenia,
        private ?int $id = null
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

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getAp1(): string
    {
        return $this->ap1;
    }

    public function setAp1(string $ap1): void
    {
        $this->ap1 = $ap1;
    }

    public function getAp2(): string
    {
        return $this->ap2;
    }

    public function setAp2(string $ap2): void
    {
        $this->ap2 = $ap2;
    }

    public function getAlta(): string
    {
        return $this->alta;
    }

    public function setAlta(string $alta): void
    {
        $this->alta = $alta;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getSaldo(): int
    {
        return $this->saldo;
    }

    public function setSaldo(int $saldo): void
    {
        $this->saldo = $saldo;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function setRol(string $rol): void
    {
        $this->rol = $rol;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setContrasenia(string $contra): void
    {
        $this->contrasenia = $contra;
    }

    public function getContra(): string
    {
        return $this->contrasenia;
    }

    public function __tostring()
    {
        return $this->nombre . " " . $this->ap1 . " " . $this->ap2;
    }
}