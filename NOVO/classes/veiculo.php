<?php 
    require_once("identificadores.php");
    require_once("coordenada.php");
    require_once("temporal.php");
    require_once("calcula_distancia_aproximada_strategy.php");

    class Veiculo {
        private RegistroDeVeiculo $id;
        private Coordenada $coordenada;
        private array $tripulantesComCoordenada; 

        public function __construct(RegistroDeVeiculo $id, Coordenada $coordenadaAeroporto, 
        array $tripulantesComCoordenada){
            $this->id = $id;
            $this->coordenadaAeroporto = $coordenadaAeroporto;
            $this->tripulantesComCoordenada = $tripulantesComCoordenada;
        }

        public function calculaHoraDePartida(CalculaDistanciaStrategy ){

        }
    }
?>