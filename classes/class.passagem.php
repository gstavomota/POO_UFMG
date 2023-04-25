<?php
  include_once("class.companhia.php");
  include_once("class.assento.php");
  include_once("class.viagem.php");

  class Passagem {
    private Aeroporto $aeroportoOrigem;
    private Aeroporto $aeroportoDestino;
    private float $valorDaTarifa;
    private string $assento;
    private int $numDeFranquias;
    private int $pesoDaFranquia;
    private float $precoTotal;
    private bool $cancelada;
    private float $valorPassagem;

    public function __construct ( Aeroporto $p_aeroportoOrigem, Aeroporto $p_aeroportoDestino, float $p_valorDaTarifa, string $p_assento, int $p_numDeFranquias, int $p_pesoDaFranquia, float $p_precoTotal, float $p_valorPassagem ) {
      $this->aeroportoDestino->sigla = $p_aeroportoDestino->sigla;
      $this->aeroportoOrigem->sigla = $p_aeroportoOrigem->sigla;
      $this->pesoDaFranquia = $p_pesoDaFranquias;
      $this->numDeFranquias = $p_numDeFranquias;
      $this->valorDaTarifa = $p_valorDaTarifa;
      $this->precoTotal = $p_precoTotal; 
      $this->reservaAssento( $p_assento );
      $this->valorPassagem = $p_valorPassagem;
      $this->cancelada = false;
    }

    public function getAeroportoDestino () {
      return $this->aeroportoDestino;
    }

    public function getAeroportoOrigem () {
      return $this->aeroportoOrigem;
    }

    public function getValorTarifa () {
      return $this->valorDaTarifa;
    }

    public function setValorTarifa ( float $p_valorDaTarifa ) {
      $this->valorDaTarifa = $p_valorDaTarifa;
    }

    public function getAssento () {
      return $this->assento;
    }

    public function getNumDeFranquias () {
      return $this->numDeFranquias;
    }

    public function setNumDeFranquias ( int $p_numDeFranquias ) {
      $this->numDeFranquias = $p_numDeFranquias;
    }

    public function getPesoDaFranquia () {
      return $this->pesoDaFranquia;
    }

    public function setPesoDaFranquia ( int $p_pesoDaFranquia ) {
      $this->pesoDaFranquia = $p_pesoDaFranquia;
    }

    public function getPrecoTotal () {
      return $this->precoTotal;
    }

    public function getCancelamento () {
      return $this->cancelada;
    }

    public function setCancelamento ( bool $p_cancelada ) {
      $this->cancelada = $p_cancelada;
    }

    public function getValorDaPassagem () {
      return $this->valorPassagem;
    }

    public function setValorDaPassagem ( float $p_valorPassagem ) {
      $this->valorPassagem = $p_valorPassagem;
    }
    
    public function calculaPrecoFranquia ( Passagem $p ) {
      $preco = ( $this->p->valorDaTarifa * $this->p->pesoDaFranquia ) * $this->p->numDeFranquias;
      return $preco;
    }

    public function calculaPrecoPassagem ( Passagem $p ) {
      $preco = calculaPrecoFranquia($p) + $this->p->getValorDaPassagem();
      return $preco;
    }

    public function cancelar(){
      $this->cancelada = true;
    }

    public function voosAtivos ( Voo $voo ) {
      $now = new DateTime();
      foreach ( $this->voos as $v ) {
        if ( $v->getSigla() === $voo->getSigla() ) {
          return;
        }
        $dataVoo = $v->getHorarioDecolagem();
        $diff = $now->diff($dataVoo);
        if ( $diff->day < 0 || $diff->day > 30 ) {
          return;
        }
      }
      array_push( $this->voos, $voo );
    }

    public function reservaAssento ( Viagem $v, Assento $assento ) {
      if ( $assento->getCancelamento === true ) {
        throw new Error("Erro ao reservar assento");
      }
      foreach ( $this->assentos as $a ) {
        if ( $a->getNumero() === $assento->getNumero() ) {
          throw new Error("Assento já reservado!");
        }
      }
      array_push( $this->assentos, $assento );
    }

    public function cancelaAssento ( Viagem $v, Assento $assento ) {
      if ( $assento->getCancelamento == true ) {
        throw new Error("Assento já cancelado.");
      }
      for ( $i = 0; $i < count( $this->assentos ); $i++ ) {
        if ( $this->assentos[$i]->getNumero() == $assento->getNumero() ) {
          array_splice( $this->assentos, $i, 1 );
        }
      }
    }
  }
