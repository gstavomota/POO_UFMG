<?php
    require_once("identificadores.php");

    class CartaoDeEmbarque {
        private RegistroDeCartaoDeEmbarque $id;

        public function __construct(RegistroDeCartaoDe $id){
            $this->id = $id;
        }

        public function calculaHorarioDeEmbarque(){
            
        }
    }
?>