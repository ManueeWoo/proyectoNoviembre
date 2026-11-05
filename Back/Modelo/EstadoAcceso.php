<?php
namespace App\Modelo;
enum EstadoAcceso: string
{
    case Pendiente = 'pendiente';
    case Aprobado = 'aprobado';
    case Rechazado = 'rechazado';
}
   