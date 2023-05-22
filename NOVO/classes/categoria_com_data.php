<?php
require_once("categoria.php");

class CategoriaComData
{
    private Categoria $categoria;
    private DataTempo $data_de_entrada;

    public function __construct(Categoria $categoria, DataTempo $data_de_entrada)
    {
        $this->categoria = $categoria;
        $this->data_de_entrada = $data_de_entrada;
    }

    public function getCategoria(): Categoria
    {
        return $this->categoria;
    }

    public function getDataDeEntrada(): DataTempo
    {
        return $this->data_de_entrada;
    }
}

?>