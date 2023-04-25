<?php
  include_once("class.aeroporto.php");
  include_once("class.voo.php");

  class VooComConexao {
    private $precoTotalDaConexao;
    private $aeroportoDeDecolagem;
    private $aeroportoDeConexao;
    private $aeroportoDeDestino;
    private $voosDeDecolagem = [];
    private $voosDeConexao = [];
    private $primeiroVoo;
    private $segundoVoo;

    public function __construct(Aeroporto $p_aeroportoDeDecolagem, Aeroporto $p_aeroportoDeDestino){
      $this->aeroportoDeDecolagem = $p_aeroportoDeDecolagem;
      $this->aeroportoDeDestino = $p_aeroportoDeDestino;
      $this->voosDePartida = array();
      $this->voosDeConexao = array();
      
      $this->voosDeDecolagem = array();
      $this->voosDeDecolagem = $this->aeroportoDeDecolagem->getVoos();
    }
    
    public function getVoosDeDecolagem(){
      return $this->voosDeDecolagem;
    }

    public function getVoosDeConexao(){
      return $this->voosDeConexao;
    }

    public function getAeroportoDeDestino(){
      return $this->aeroportoDeDestino;
    }

    public function addVoosDeConexao(Voo $p_voo){
      array_push($this->voosDeConexao, $p_voo);
    }

    public function qttdVoosDeConexao(){
      return sizeof($this->getVoosDeConexao());
    }

    public function setAeroportoDeConexao(Aeroporto $p_aeroportoDeConexao){
      $this->aeroportoDeConexao = $p_aeroportoDeConexao;
    }

    public function getAeroportoDeConexao(){
      return $this->aeroportoDeConexao;
    }
  
    public function constroiConexao(){
      $possiveisAeroportosDeConexao = array(); 
      
      foreach($this->getVoosDeDecolagem() as $vooDeDecolagem)
        array_push($possiveisAeroportosDeConexao, $vooDeDecolagem->getAeroportoChegada()); 

      foreach($possiveisAeroportosDeConexao as $possivelAeroportoDeConexao){
        $possiveisVoosDeConexao = $possivelAeroportoDeConexao->getVoos();
        
        foreach($possiveisVoosDeConexao as $possivelVooDeConexao){
          if($possivelVooDeConexao->getAeroportoChegada() == $this->getAeroportoDeDestino())
            $this->addVoosDeConexao($possivelVooDeConexao);
          else 
            continue;
        }
      }

      if ($this->qttdVoosDeConexao() == 0)
        throw New Exception ("Não existe um voo de conexão entre as cidades dos aeroportos citados.");
      else {
        $vooDeConexao = $this->getVoosDeConexao()[0];
        $this->setSegundoVoo($vooDeConexao);
        $this->setAeroportoDeConexao($vooDeConexao->getAeroportoSaida());

        $key = array_search($this->getAeroportoDeConexao(), $this->getVoosDeDecolagem()->getAeroportoChegada());
        $p_primeiroVoo = $this->getVoosDeDecolagem[$key];
        $this->setPrimeiroVoo($p_primeiroVoo);        
      }
    }
  
  }
?>