<?php
//clase que responda al js
namespace App\Helpers;
class Respondedor{

public static function jsonResponse(mixed $data, int $statusCode): void {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($statusCode);
        echo json_encode($data);
    }
}