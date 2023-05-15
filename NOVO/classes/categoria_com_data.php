<?php
    require_once("categoria.php");

    class CategoriaComData{
        private Categoria $categoria;
        private DateTime $data_de_entrada; 

        public function __construct(Categoria $categoria, DateTime $data_de_entrada)
        {
            $this->categoria = $categoria;
            $this->data_de_entrada = $data_de_entrada;
        }
        public function getCategoria(){
            return $this->categoria;
        }
        public function getDataDeEntrada(){
            return $this->data_de_entrada;
        }
    }

?>