<?php
//Raphael
  class Pessoa{
    private string $nome;
    private string $sobrenome;
    private string $documento;

    public function __construct(string $nome, string $sobrenome, string $documento){
      this->$nome = $nome;
      this->$sobrenome = $sobrenome;
      this->$documento = $documento;
    }
    public function getNome(){
      return this->$nome;
    }
    public function getSobrenome(){
      return this->$sobrenome;
    }
    public function getDocumento(){
      return this->$documento;
    }
    public function validaDocumento(string $documento): void{
      $tamanho = strlen($documento);
      $caracteres = substr($documento, 0, 2); //retorna uma parte da string.
      $eh_letra = ctype_alpha($caracteres); //retorna true se for tudo letra.
      
      if(is_null($documento)){
        print_r("O documento não pode ser nulo.");
        return;
      }
      if($tamanho != 8){
        throw new Exception("Documento de tamanho inválido");
      }
      if($tamanho == 8 && !($eh_letra)){
        print_r("É registro geral, documento válido.");
      }else if($tamanho == 8 && $eh_letra){
        print_r("É passaporte, documento válido.");
      }
      else {
        throw new Exception( "Formato Inválido!" );
    }
  }
?>