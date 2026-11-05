<?php
namespace App\Modelo;
enum EstadoOferta: string
{
    case Solicitado = 'solicitado';
    case Aceptado = 'aceptado';
    case rechazado = 'rechazado';
    case Completado = 'completado';
    case Cancelado = 'cancelado';
    case Ejeccucion = 'ejeccucion';
}
   