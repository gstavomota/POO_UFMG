<?php
  require_once('global.php');
  include_once("persist.php");   
  include_once("class.voo.php");
  include_once("class.viagem.php");
  include_once("class.companhia.php");
  include_once("class.aeroporto.php");
  
  class vooTst extends persist {
    // Instanciando um novo objeto da classe Voo e testando os métodos get
    if ( 0 ) {
        //$aeroporto1 = new Aeroporto("Belo Horizonte", "Minas Gerais", "GRU");
        //$aeroporto2 = new Aeroporto("São Paulo", "São Paulo", "SPA");
        $voo1 = new Voo(1, "2019-02-28 11:06:53.684238", "2019-02-28 12:36:53.684238", 90, "AZ1234"); 

        try {
          if ($voo1->getSigla()) != "AZ1234")
            throw new Exception ("Algum erro envolvendo getSigla");
        } catch(Exception $e){
          echo $e;
        }
        if ( count( $this->voo ) != 1 ) {
          throw new Error( "Erro na função de cadastro!" );
        }
        try {
          if ($voo1->getFrequencia()) != 1)
            throw new Exception ("Algum erro envolvendo getFrequencia");
        } catch(Exception $e){
          echo $e;
        }
        try {
          if ($voo1->getHorarioDecolagem()) != "2019-02-28 11:06:53.684238")
            throw new Exception ("Algum erro envolvendo getHorarioDecolagem");
        } catch(Exception $e){
          echo $e;
        }
        try {
          if ($voo1->getHorarioAterrissagem()) != "2019-02-28 12:36:53.684238")
            throw new Exception ("Algum erro envolvendo getHorarioAterrissagem");
        } catch(Exception $e){
          echo $e;
        }
        try {
          if ($voo1->getDuracao()) != 90)
            throw new Exception ("Algum erro envolvendo getDuracao");
        } catch(Exception $e){
          echo $e;
        }
    }

    // -------------------------------------------------------------------------------------------------------
    // testando exibir um voo
    if ( 0 ) {
      $voo2 = new Voo(2, "2019-02-02 11:06:53.684238", "2019-02-02 12:36:53.684238", 180, "AZ2345"); 
      try {
          if ($voo1->getFrequencia() != 2 && $voo1->getHorarioDecolagem() != "2019-02-02 11:06:53.684238")
            throw new Exception ("Algum erro envolvendo exibeVoo");
        } catch(Exception $e){
          echo $e;
        }

    // -------------------------------------------------------------------------------------------------------
    // Testa os métodos Valida Duração
      if ( 0 ) {
      $voo3 = new Voo(3, "2019-02-03 11:06:53.684238", "2019-02-03 12:36:53.684238", 100, "AB2345"); 
      try {
          if ($voo1->validaDuracao() != 100)
            throw new Exception ("Algum erro envolvendo validaDuracao");
        } catch(Exception $e){
          echo $e;
        }
      try {
          if ($voo1->validaDuracao() == 0)
            throw new Exception ("Algum erro envolvendo validaDuracao, não pode ser 0");
        } catch(Exception $e){
          echo $e;
        }  

    // -------------------------------------------------------------------------------------------------------
    // Testa os métodos Valida Codigo
      if ( 0 ) {
      $voo4 = new Voo(4, "2019-02-03 11:06:53.684238", "2019-02-03 12:36:53.684238", 100, "AB2345"); 
      try {
          if ($voo1->validaCodigo() != "AB2345")
            throw new Exception ("Algum erro envolvendo validaCodigo");
        } catch(Exception $e){
          echo $e;
        }
      try {
          if ($voo1->validaCodigo() == 0)
            throw new Exception ("Algum erro envolvendo validaCodigo, não pode ser 0");
        } catch(Exception $e){
          echo $e;
        }  

      $voo1->save();
      $voo2->save();
      $voo3->save();
      $voo4->save();
    }

    // -------------------------------------------------------------------------------------------------------
    if (1) {
        $voos = voos::getRecords();
        print_r($voos);
    }
  }