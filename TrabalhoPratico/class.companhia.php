// Maria Eduarda
<?php
  include_once("class.aeronave.php");
  include_once("class.voo.php");

  class Companhia {
    private string $nome;
    private string $codigo;
    private string $razao_social;
    private string $cnpj;
    private string $sigla;
    private array $aeronaves = [];
    private array $voos = [];

    public function __construct (string $p_nome, string $p_codigo, string $p_razao_social, string $p_cnpj, string $p_sigla) {
      $this->nome = $p_nome;
      $this->codigo = $p_codigo;
      $this->razao_social = $p_razao_social;
      $this->cnpj = $p_cnpj;
      $p_sigla = strtoupper($p_sigla);
      $this->validaSigla($p_sigla);
      $this->aeronaves = array(); 
      $this->voos = array(); 
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
    
    public function getAeronaves(){
      return $this->aeronaves;
    }

    public function setSigla(string $p_sigla){
      $this->sigla = $p_sigla;
    }

    public function cadastraAeronaves(Aeronave $p_aeronave){
      array_push($this->aeronaves, $p_aeronave);
    }

    public function cadastraVoos(){
      foreach($this->getAeronaves() as $p_aeronave){
        foreach ($p_aeronave->getVoos() as &$p_voo) {
          array_push($this->voos, $p_voo);
        }
      }
    }

    public function validaSigla(string $p_sigla){
      $tamanhoSigla = strlen($p_sigla);
      $somenteLetras = ctype_alpha($p_sigla[0]) &&
                       ctype_alpha($p_sigla[1]);
        
      if ($tamanhoSigla != 2){
        echo "Somente permitido uma sigla formada por 2 letras. Tamanho inadequado.";
      } 
      else {
        if ($somenteLetras){
          $this->setSigla($p_sigla);
          echo "Sigla salva com sucesso.";
        }
        else {
          echo "Somente permitido uma sigla formada por 2 letras. Digitar apenas letras sem acento.";
        }
      }
  }
?>