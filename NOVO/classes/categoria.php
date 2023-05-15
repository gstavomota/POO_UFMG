<?php

    require_once("programa_de_milhagem.php");

    class Categoria {
        
        private string $nome;
        private int $pontuacao;

        public function __construct( string $p_nome, int $p_pontuacao ) {
            $this->nome = $p_nome;
            $this->pontuacao = $p_pontuacao;
        }
        public function getNome(){
            return $this->nome;
        }
        public function getPontuacao(){
            return $this->pontuacao;
        }
    }

?>