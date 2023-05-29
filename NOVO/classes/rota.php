<?php

interface CalculoRotaStrategy
{
    function calculaDistancia(): float;
}

class RotaStrategy implements CalculoRotaStrategy
{
    private array $enderecos;
    private TripulanteComCoordenada $tripulanteComCoordenada;

    public function __construct(array $enderecos, TripulanteComCoordenada $tripulante){
        $this->enderecos = $enderecos;
        $this->tripulanteComCoordenada = $tripulante;
    }
    public function calculaDistancia(){

    }
}
?>
