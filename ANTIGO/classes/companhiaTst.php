<?php
  require_once("global.php")
  include_once("persist.php");   
  
  class companhiaTst extends persist {
    // Instancia um novo objeto Companhia e testa os métodos relacionados ao atributo array de Aeronaves
    if ( 0 ) {
      $companhia1 = new Companhia('CAA DE SAO PAULO - 1', 'Codigo 123',  'Companhia Aérea S.A.', '01.234.567/0001-89', 'CA', 72.90); 

      $aeronave1 = new Aeronave('DIAMOND', ' Airbus A320', 525, 4500000, 'PR-GUO');
      $aeronave2 = new Aeronave('EMBRAER', 'Aero A771', 739, 600000, 'PW-GUA');

      $companhia1->cadastraAeronave($aeronave1);
      $companhia1->cadastraAeronave($aeronave2);
    
      try {
        if (sizeof($companhia1->getAeronaves()) != 2)
          throw new Exception ("Algum erro envolvendo as funções cadastraAeronave ou getAeronaves.");
      } catch(Exception $e){
        echo $e echo "\n";
      }
    
      $companhia1->save();
    }

    // Instancia um novo objeto Companhia e testa os métodos relacionados ao atributo array de Voos
    if ( 0 ) {
      $companhia2 = new Companhia('CAB DE CONFINS - 2', '456 cOdig0', 'Companhia Aérea ÁÃBÇ .,;', '25.467.508/9980-12', 'CB', 57.89);
      
      $aeronave1 = new Aeronave('DIAMOND', ' Airbus A320', 525, 4500000, 'PR-GUO'); // funciona 
      $aeronave2 = new Aeronave('EMBRAER', 'Aero A771', 739, 600000, 'PW-GUA'); // funciona
 
      $formato = 'd/m/Y';
      $stringDataDecolagem = '16/04/2023';      
      $stringDataChegada = '18/04/2023';
      $diaDecolagem = DateTime::createFromFormat($formato, $stringDataDecolagem);
      $diaChegada = DateTime::createFromFormat($formato, $stringDataChegada);
      $vooAero1 = new Voo(1, $diaDecolagem, $diaChegada, 4, "BH-MS"); // funciona
      $vooAero2 = new Voo(3, $diaDecolagem, $diaChegada, 9, "BH-EUA"); // funciona

      $aeronave1->addVoos($vooAero1); 
      $aeronave2->addVoos($vooAero2); 
      
      $companhia2->cadastraAeronave($aeronave1); // funciona
      $companhia2->cadastraAeronave($aeronave2); // funciona
      $companhia2->cadastraVoos();
      
      if(sizeof($companhia2->getVoos()) != 2)){
          throw new Exception ("Algum erro envolvendo as funções cadastraVoos ou getVoos.");
      } catch(Exception $e){
        echo $e echo "\n";
      }

      $companhia2->save();
    }

    // Testa os métodos get e verifica se a sigla é null, pois não foi validada
    if ( 0 ) {
      $companhia3 = new Companhia('ABCS21 - 3', 'Codigo_Companhia', 'C. A. Minas Gerais', '25.467.508/9980-12', 'VVC', 82.35);
      
      try {
        if($companhia3->getSigla() == 'VVC')
          throw new Exception ("Sigla não é válida, conforme passado por parâmetro pelo construtor. Valor não deveria ter sido atribuido ao atributo.");
      } catch (Exception $e){
        echo $e;
      }
      
      try {
        if ($companhia3->getNome() != 'ABCS21 - 3')
          throw new Exception ("Erro na atribuição do nome.");
      } catch(Exception $e){
		echo $e;
      }
      
      try {
        if ($companhia3->getCodigo() != 'Codigo_Companhia')
          throw new Exception ("Erro na atribuição do código.");
      } catch (Exception $e){
   	  	echo $e;
      }
      
      try {
        if ($companhia3->getRazaoSocial() != 'C. A. Minas Gerais')
          throw new Exception ("Erro na atribuição da razão social.");
      } catch (Exception $e){
   	  	echo $e;
      }
      
      try {
        if ($companhia3->getCnpj() != '25.467.508/9980-12')
          throw new Exception ("Erro na atribuição do CNPJ.");
      } catch (Exception $e){
   	  	echo $e;
      }
      
      try {
        if($companhia3->getPrecoDaFranquia() != 82.35)
          throw new Exception ("Erro na atribuição do preço da franquia.");
      } catch (Exception $e){
        echo $e;
      }

      $companhia3->save();
    }

    // Testa os métodos get e verifica se a sigla é null, pois não foi validada
    if ( 0 ) {
      $companhia4 = new Companhia('@DEFFD _ 4', 'Codigo_Objeto', 'C. A. Rio de Janeiro', '11.222.333/4445-55', 'V2', 54.21);

      try {
        if ($companhia4->getSigla() == 'V2')
          throw new Exception ("Sigla não é válida, conforme passado por parâmetro pelo construtor. Valor não deveria ter sido atribuido ao atributo.");
      } catch (Exception $e){
   	  	echo $e;
      }
      
      try {
        if ($companhia4->getNome() != '@DEFFD _ 4')
          throw new Exception ("Erro na atribuição do nome.");
      } catch (Exception $e){
   	  	echo $e;
      }  
      
      try {
        if ($companhia4->getCodigo() != 'Codigo_Objeto')
          throw new Exception ("Erro na atribuição do código.");
      } catch (Exception $e){
   	  	echo $e;
      }  
      
      try {
        if ($companhia4->getRazaoSocial() != 'C. A. Rio de Janeiro')
          throw new Exception ("Erro na atribuição da razão social.");
      } catch (Exception $e){
   	  	echo $e;
      }  
      
      try {
        if ($companhia4->getCnpj() != '11.222.333/4445-55')
          throw new Exception ("Erro na atribuição do CNPJ.");
      } catch (Exception $e){
   	  	echo $e;
      }
      
      try {
        if($companhia4->getPrecoDaFranquia() != 54.21)
          throw new Exception ("Erro na atribuição do preço da franquia.");
      } catch (Exception $e){
        echo $e;
      }
          
      $companhia4->save();
    }

    if ( 1 ) {
        $companhias = companhia::getRecords();
        print_r($companhias);
    }
  }