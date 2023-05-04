//Gustavo
<?php

class Viagem {
  private DateTime $decolagem;
  private DateTime $aterrissagem;
  private int $duracao;

  public function __construct (DateTime $decolagem, DateTime $aterrissagem, int       $duracao) {
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