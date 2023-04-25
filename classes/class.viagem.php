<?php
require_once("persist.php");
//Gustavo

class Viagem extends persist {
  private DateTime $decolagem;
  private DateTime $aterrissagem;
  private int $duracao;
  private $assentos = array();
  static $local_filename = "viagem.txt";

  static public function getFilename(){
    return get_called_class::$local_filename;  
  }
  
  public function __construct (DateTime $decolagem, DateTime $aterrissagem, int $duracao) {
    $this->decolagem = $decolagem;
    $this->aterrissagem = $aterrissagem;
    $this->duracao = $duracao;
  }

  public function getDecolagem(){
    return $this->decolagem;
  }

  public function getAterrissagem(){
    return $this->aterrissagem;
  }

  public function getDuracao(){
    return $this->duracao;
  }
}