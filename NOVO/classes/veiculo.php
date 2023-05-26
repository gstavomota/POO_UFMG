<?php 
    require_once("identificadores.php");
    require_once("coordenada.php");
    require_once("temporal.php");

    class Veiculo {
        private RegistroDeVeiculo $id;
        private Coordenada $coordenada;
        private DataTempo $hora_saida;
        private Endereco $endereco;

        public function __construct(RegistroDeVeiculo $id, Coordenada $coordenada, DataTempo $hora_saida, Endereco $endereco){
            $this->id = $id;
            $this->coordenada = $coordenada;
            $this->hora_saida = $hora_saida;
            $this->endereco = $endereco;
        }
    }
?>