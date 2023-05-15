<?php

    class Pontos{
        private int $pontos_ganhos;
        private DateTime $data_de_obtencao;

        public function __construct(int $pontos_ganhos, DateTime $data_de_obtencao)
        {
            $this->pontos_ganhos = $pontos_ganhos;
            $this->data_de_obtencao = $data_de_obtencao;
        }
        public function getPontosGanhos(){
            return $this->pontos_ganhos;
        }
        public function getDataDeObtencao(){
            return $this->data_de_obtencao;
        }
    }

?>