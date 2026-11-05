<?php
namespace App\Modelo;
use App\Modelo\Oferta;
use App\Modelo\EstadoOferta;
use App\Modelo\Valoracion;
use DateTime;

class Solicitud
{
    public function __construct(
        private Usuario $usuario,
        private Oferta $oferta,
        private EstadoOferta $estado = EstadoOferta::Solicitado,
        private string $fecha,
        private int $tiempoFinalizado,
        private int $puntos,
        private string $motivo,
        private Valoracion $valoracion,
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

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function getOferta(): Oferta
    {
        return $this->oferta;
    }

    public function setOferta(Oferta $oferta): void
    {
        $this->oferta = $oferta;
    }

    public function getEstado(): EstadoOferta
    {
        return $this->estado;
    }

    public function setEstado(EstadoOferta $estado): void
    {
        $this->estado = $estado;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getTiempoFinalizado(): int
    {
        return $this->tiempoFinalizado;
    }

    public function setTiempoFinalizado(int $tiempoFinalizado): void
    {
        $this->tiempoFinalizado = $tiempoFinalizado;
    }

    public function getPuntos(): int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): void
    {
        $this->puntos = $puntos;
    }

    public function getMotivo(): string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): void
    {
        $this->motivo = $motivo;
    }

    public function getValoracion(): Valoracion
    {
        return $this->valoracion;
    }

    public function setValoracion(Valoracion $valoracion): void
    {
        $this->valoracion = $valoracion;
    }

}