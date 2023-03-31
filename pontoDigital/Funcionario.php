<?php

include_once("PontoOnline.php");
class Funcionario {
    private $nome;
    private $cpf;
    private $pontos = array();

    public function __construct($nome, $cpf){
        $this->nome = $nome;
        $this->cpf = $cpf;
    }

    public function addPonto( PontoOnline $ponto ){
        array_push($this->pontos, $ponto );
    }

    public function exibeFuncionario(){
        print_r($this->nome);
        print_r($this->cpf);
    }
}


