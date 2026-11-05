<?php
namespace App\Router;
use Twig\Environment;
class Router
{
    private Environment $twig;
    private array $rutas = [];
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    //PARA LAS RUTAS AMIGABLES
    public function get(string $ruta, string $claseYmetodo)
    {
        $this->rutas['GET'][] = ['ruta' => $ruta, 'metodoYclase' => $claseYmetodo];
        //a rutas le meto por cada verbo una ruta y una clase con metodo con array de arrays
        // por dentro algo asi 'GET'  => [rutas => '/' metodoYclase => 'Controlador@sacarPlantilla'],
    }

    public function post(string $ruta, string $claseYmetodo)
    {
        $this->rutas['POST'][] = ['ruta' => $ruta, 'metodoYclase' => $claseYmetodo];
    }

    //CONTROLAR EL QUE ME PUEDA VENIR /servicios/5
    public function dispatch(string $method, string $url): void
    {
        $url = parse_url($url, PHP_URL_PATH);
        $trozosUrl = explode('/', trim($url, '/'));
        foreach ($this->rutas[$method] ?? [] as $ruta) {
            $trozosGuardados = explode('/', trim($ruta['ruta'], '/'));
            $total = count($trozosGuardados);
            if ($total === count($trozosUrl)) {
                $parametrosMetodo = [];
                $coincide = true;
                //ver si en los trozos hay uno que empieza y termina {} para comprobar si es un parametro que le paso a la funcion
                for ($i = 0; $i < $total; $i++) {
                    $segmento = $trozosGuardados[$i];
                    if (str_starts_with($segmento, '{') && str_ends_with($segmento, '}')) {
                        $parametrosMetodo[] = $trozosUrl[$i];
                    } elseif ($segmento !== $trozosUrl[$i]) {
                        $coincide = false;
                    }
                }
                //si va bien creo el controlador y el metodo con su parametro 
                if ($coincide) {
                    $lista = explode('@', $ruta['metodoYclase']);
                    $clase = $lista[0];
                    $metodo = $lista[1];
                    $controlador = new $clase($this->twig);
                    $controlador->$metodo(...$parametrosMetodo); //operador ... spread hace que la coleccion que le paso se muestre parametro por parametro
                }
            }

        }
    }
}
