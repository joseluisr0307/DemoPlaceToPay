<?php

namespace App\Http\Controllers;

use PlaceToPay;

class ConnectController extends Controller
{

    private $requestConnection;

    public function __construct()
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

}