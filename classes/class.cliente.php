<?php
  require_once("class.pessoa.php");
  require_once("class.aeroporto.php");

  class Cliente extends Pessoa{
    
    private $passagens = array();
    private DateTime $dataDaCompra;

    public function __construct(string $nome, string $sobrenome, string $documento){
      parent::__construct($nome, $sobrenome, $documento);
    }

    // Passagem só é adicionada ao array através dessa funcao
    // Ela tbm inicializa o dataDaCompra
    public function compraPassagem(Passagem $passagem){
      $novaPassagem = $passagem;
      array_push($this->passagens, $novaPassagem);
    }

    public function cancelaPassagem($id){
       foreach ($this->passagens as $passagem){
         if ($passagem->getId() == $id){
           $passagem->cancelar();
           return true;
         }
       }
      return false;
    }
  }
