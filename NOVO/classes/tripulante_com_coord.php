<?php 
    require_once("identificadores.php");
    require_once("coordenada.php");
    require_once("temporal.php");
    require_once("tripulante.php");

    class TripulanteComCoordenada extends Tripulante {
        private RegistroDeTripulante $id;
        private Coordenada $estadia;
        private Coordenada $aeroporto_de_origem;

        public function __construct(Coordenada $estadia, Coordenada $aeroporto_de_origem ,RegistroDeTripulante $id){
            $this->estadia = $estadia;
            $this->aeroporto_de_origem = $aeroporto_de_origem;
            $this->id = $id;
        }

        public function getID(): RegistroDeTripulante
        {
            return $this->id;
        }

        public function getEstadia(): Coordenada
        {
            return $this->estadia;
        }

        public function getAeroportoOrigem() : Coordenada{
            return $this->aeroporto_de_origem;
        }
    }
?>