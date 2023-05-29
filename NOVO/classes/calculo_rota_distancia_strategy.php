<?php

use TripulanteComCoordenada;

require_once 'coordenada.php';
require_once 'tripulante_com_coord.php';

interface CalculoRotaDistanciaStrategy{
    function calcula(TripulanteComCoordenada $estadia, TripulanteComCoordenada $aeroporto_de_origem) : float;
}

class CalculoDistancia implements CalculoRotaDistanciaStrategy {
    private float $distancia;

    public function __construct(float $distancia){
        $this->distancia = $distancia;
    }

    public function calcula(TripulanteComCoordenada $estadia, TripulanteComCoordenada $aeroporto_de_origem) : float {
        return $this->distancia = 110.57*sqrt(pow($aeroporto_de_origem->getAeroportoOrigem()->x - $estadia->getEstadia()->x, 2) + 
                                       pow($aeroporto_de_origem->getAeroportoOrigem()->y - $estadia->getEstadia()->y, 2));
    }

}