<?php
require_once "identificadores.php";
require_once "coordenada.php";

class TripulanteComCoordenada implements ICoordenada
{
    private RegistroDeTripulante $tripulante;
    private ICoordenada $coordenada;

    public function __construct(RegistroDeTripulante $tripulante, ICoordenada $coordenada)
    {
        $this->tripulante = $tripulante;
        $this->coordenada = $coordenada;
    }

    public function getTripulante(): RegistroDeTripulante
    {
        return $this->tripulante;
    }

    public function getCoordenada(): ICoordenada
    {
        return $this->coordenada;
    }

    public function getX(): float {
        return $this->coordenada->getX();
    }
    public function getY(): float {
        return $this->coordenada->getY();
    }
    public function eq(Equatable $outro): bool {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->coordenada->eq($outro->coordenada) && $this->tripulante->eq($outro->tripulante);
    }
}

?>