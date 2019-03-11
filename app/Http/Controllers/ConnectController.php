<?php

namespace App\Http\Controllers;

use PlaceToPay;

class ConnectController extends Controller
{
    /**
     * Contenedor de la instancia del singleton
     *
     */
    private static $_instance;
    private $requestConnection;

    private function __construct()
    {
        $this->requestConnection = new PlacetoPay([
            'login' => env('IDENTIFICATOR'),
            'tranKey' => env('SECRETKEY'),
            'url' => env('ENDPOINT'),
        ]);
    }

    /**
     * Retorna la conexión a PlayToPay para poder consumir los servicios
     *
     * @return Dnetix\Redirection\PlacetoPay
     */
    public function getConnection()
    {
        return $this->requestConnection;
    }

    /**
     * Retrona la intancia de la conexion evitando que la aplicación tenga innumerable y descontroladas conexiones innecesarias abiertas
     * @return mixed
     */
    public static function singleton()
    {
        /**
         *  Crea la instancia de la clase siempre que no haya sido creada o seteada anteriormente
         */
        if (!self::$_instance instanceof self)
            self::$_instance=new self();

        return self::$_instance;
    }

    /**
     *   Evita la creación del objeto desde el exterior de la clase gracias a ser privado.
     */
    private function __clone() { }
}