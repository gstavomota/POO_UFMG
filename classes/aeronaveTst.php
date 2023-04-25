<?php
  require_once('global.php');
  include_once("persist.php");   
  include_once("class.voo.php");
  include_once("class.viagem.php");
  include_once("class.aeronave.php");
  
  class aeronaveTst extends persist {
    // -------------------------------------------------------------------------------------------------------
    // Instancia um novo objeto Aeronave e testa os métodos relacionados ao atributo array de voos
    if ( 0 ) {
        $aeronave1 = new Aeronave('DIAMOND', ' Airbus A320', 525, 4500000, 'PR-GUO'); 

        // Parâmetros do constutor de Voo: (int $frequencia, DateTime $decolagem, DateTime $aterrissagem, int $duracao, string $sigla)
        $voo1 = new Voo(1, "2019-02-28 11:06:53.684238", "2019-02-28 12:36:53.684238", 90, 'AZ1234');
        $voo2 = new Voo(3, "2019-02-21 11:06:53.684238", "2019-02-28 12:36:53.684238", 90, 'GO4321');

        $aeronave1->addVoo($voo1);
        $aeronave1->addVoo($voo2);
      
        try {
          if (sizeof($aeronave1->getVoos()) != 2)
            throw new Exception ("Ocorreu algum erro nas funções addVoo ou getVoos.");
        } catch(Exception $e){
          echo $e;
        }
      
        $aeronave1->save();
    }

    // -------------------------------------------------------------------------------------------------------
    // Instancia um novo objeto Aeronave e testa os métodos relacionados ao atributo array de viagens
    if ( 0 ) {
      $aeronave2 = new Aeronave('EMBRAER', 'Aero A771', 739, 600000, 'PW-GUA'); 
      // Parâmetros do construtor de Viagem: DateTime $decolagem, DateTime $aterrissagem, int $duracao
      $viagem1 = new Viagem("2019-02-28 11:06:53.684238", "2019-02-28 12:36:53.684238", 90);
      $viagem2 = new Viagem("2019-02-21 11:06:53.684238", "2019-02-28 12:36:53.684238", 90);

      $aeronave1->addViagem($viagem1);
      $aeronave1->addViagem($viagem2);

      try {
        if (sizeof($aeronave1->getViagens()) != 2)
          throw new Exception ("Ocorreu algum erro nas funções addViagem ou getViagens.");
      } catch(Exception $e){
        echo $e;
      }
    
      $aeronave1->save();

    }

    // -------------------------------------------------------------------------------------------------------
    // Testa os métodos get e verifica se a sigla é null, pois não foi validada
    if (0) {
      $aeronave3 = new Aeronave('EMBRAER', 'Aero A771', 739, 600000, 'PW-GyA');
      
      try {
        if( $aeronave3->getFabricante() != 'EMBRAER')
          throw new Exception ("Erro na atribuição do fabricante da aeronave.");
      } catch (Exception $e){
        echo $e;
      }
      
      try {
        if ($aeronave3->getModelo() != 'Aero A771')
          throw new Exception ("Erro na atribuição do modelo da aeronave.");
      } catch(Exception $e){
		echo $e;
      }
      
      try {
        if ($aeronave3->getCapacidade() != 739)
          throw new Exception ("Erro na atribuição da capacidade da aeronave.");
      } catch (Exception $e){
   	  	echo $e;
      }
      
      try {
        if ($aeronave3->getCapacidade_kg() != 600000)
          throw new Exception ("Erro na atribuição da capacidade em kilos da aeronave.");
      } catch (Exception $e){
   	  	echo $e;
      }

      try {
        if ($aeronave3->getRegistro() == 'PW-GyA')
          throw new Exception ("Erro na atribuição do registro. Ele está fora do padrão aceito, e, portanto, não deveria ter sido validado.");
      } catch (Exception $e){
   	  	echo $e;
      }

      $aeronave3->save();
    }

    // -------------------------------------------------------------------------------------------------------
    if (1) {
        $aeronaves = aeronave::getRecords();
        print_r($aeronaves);
    }
  }