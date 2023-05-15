<?php

use MyApp\Passageiro;

    require_once("companha_aerea.php");
    require_once("passageiro.php");

    class ProgramaDeMilhagem{
        private array $categorias;
        private string $nome_do_programa;

        public function __construct(array $categoria, string $nome_do_programa)
        {
            $this->categorias = $categoria;
            $this->nome_do_programa = $nome_do_programa;
        }
        public function getCategorias(){
            return $this->categorias;
        }
        public function getNomeDoPrograma(){
            return $this->nome_do_programa;
        }
        public function update(PassageiroVip $passageiro){
            $categoriaDoPassageiro = $passageiro->getCategoriaDoPrograma();
            $categoria = $this->categorias[0];
            $pontuacao = $passageiro->getPontosValidos();
            foreach($this->categorias as $categoriaAlvo){
                if($categoriaAlvo->getPontuacao() <= $pontuacao){
                    $categoria = $categoriaAlvo;
                }
            }
            if(count($categoriaDoPassageiro) > 1 and $categoriaDoPassageiro[count($categoriaDoPassageiro)-1] == $categoria){
                return;
            }
            $passageiro->alterarCategoria($categoria);
        }
    }

?>