<?php
// maria eduarda
  include_once("class.aeronave.php");
  include_once("class.voo.php");
  include_once("companhiaTst.php");
  include_once("class.passagem.php");
  include_once("class.cliente.php");

  class Companhia {
    private $nome;
    private $codigo;
    private $razao_social;
    private $cnpj;
    private $sigla;
    private $precoDaFranquia;
    private $aeronaves = [];
    private $voos = [];
    private $passagens = [];

    static $local_filename = "companhia.txt";

    public function __construct (string $p_nome, string $p_codigo, string $p_razao_social, string $p_cnpj, string $p_sigla, float $p_precoDaFranquia) {
      $this->nome = $p_nome;
      $this->codigo = $p_codigo;
      $this->razao_social = $p_razao_social;
      $this->cnpj = $p_cnpj;
      $this->aeronaves = array(); 
      $this->voos = array();
      $this->passagens = array();
      $this->precoDaFranquia = $p_precoDaFranquia;
      
      $p_sigla = strtoupper($p_sigla);
      
      try {
        $this->validaSigla($p_sigla);
      } catch (Exception $e){
        echo $e->getMessage();
      }
    }

    public function __destruct(Companhia $companhia){
      /* How does PHP cleanup memory? If a variable falls out of scope and is not used in any other place of the currently executed code anymore, then it is garbage collected automatically. You can force this early by using unset() to end variables scope early. */
      unset($companhia);
    }
    
    public function getNome(){
      return $this->nome;
    }
    
    public function getCodigo(){
      return $this->codigo;
    }
    
    public function getRazaoSocial(){
      return $this->razao_social;
    }
    
    public function getCnpj(){
      return $this->cnpj;
    }
    
    public function getSigla(){
      return $this->sigla;
    }
    
    public function getPrecoDaFranquia(){
      return $this->precoDaFranquia;
    }
    
    public function getAeronaves(){
      return $this->aeronaves;
    }

    public function getVoos(){
      return $this->voos;
    }

    public function setSigla(string $p_sigla){
      $this->sigla = $p_sigla;
    }

    public function cadastraAeronave(Aeronave $p_aeronave){
      array_push($this->aeronaves, $p_aeronave);
    }

    public function cadastraVoos(){
      foreach($this->getAeronaves() as $p_aeronave)
        foreach ($p_aeronave->getVoos() as &$p_voo) 
            array_push($this->voos, $p_voo);
    }
    
    public function validaSigla(string $p_sigla){
      $tamanhoSigla = strlen($p_sigla);
      $somenteLetras = ctype_alpha($p_sigla[0]) &&
                      ctype_alpha($p_sigla[1]);
      if ($tamanhoSigla != 2){
        throw new Exception("$p_sigla contém tamanho diferente de 2 letras.");
      } 
      else {
        if ($somenteLetras){
          $this->setSigla($p_sigla);
          throw new Exception("Sigla $p_sigla salva com sucesso.");
        }
        else {
          throw new Exception("Sigla $p_sigla só pode ser formada por letras sem acento.");
        }
      }
    }

    public function comprarPassagem(Cliente $p_cliente, Voo $p_voo, Assento $p_assento){
      // ...
      // inclui no map de passagens a passagem criada
      // retorna passagem
    }

    public function cancelarPassagem(Cliente $p_cliente, Passagem $p_passagem){
      // retira do map (array) de passagens a passagem passada por parametro
      // cancela assento reservado por essa passagem
      // chama metodo para cancelar passagem do cliente
      // unset nessa passagem
    }

    public function calculaPrecoPassagem(Passagem $p_passagem){
      // verifica se passagem existe
      // soma preço do assento com preço da franquia * quantidade de malas (?)
      // retorna preço total
    }
 }
?>