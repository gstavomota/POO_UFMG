<?php 
    require_once("identificadores.php");
    require_once("coordenada.php");
    require_once("temporal.php");
    require_once("tripulante.php");

    class TripulanteComCoordenada extends Tripulante {
        private RegistroDeTripulante $id;
        private Coordenada $coordenada;

        public function __construct(Coordenada $coordenada, RegistroDeTripulante $id){
            $this->coordenada = $coordenada;
            $this->id = $id;
        }

        public function getID(): RegistroDeTripulante
        {
            return $this->id;
        }

        public function getCoordenada(): Coordenada
        {
            return $this->coordenada;
        }
    }
?>