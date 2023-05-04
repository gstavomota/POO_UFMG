<?php
    require_once('global.php');
    include_once('persist.php');
    include_once('class.aeroporto.php');

class aeroportoTst extends Persist {

    // Instanciando um novo objeto da classe aeroporto
    if ( 0 ) {
        $aeroporto1 = new Aeroporto( "São Paulo". "SP", "GRU" );
        $check = validaSigla( $aeroporto1->getSigla() );
        if ( $check ) {
          $aeroporto1->save();
          cadastraAeroporto( $aeroporto1 );  
        } else {
          throw new Error( "Aeroporto não cadastrado por conta de sigla em formato errado!" );
        }
    }

    if ( 0 ) {
        $aeroporto2 = new Aeroporto( "Belo Horizonte", "MG", "CNF" );
        $check = validaSigla( $aeroporto2->getSigla() );
        if ( $check ) {
          $aeroporto2->save();
          cadastraAeroporto( $aeroporto2 );
        } else {
          throw new Error( "Aeroporto não cadastrado por conta de sigla em formato errado!" );
        }
    }

    if ( 0 ) {
        $aeroporto3 = new Aeroporto( "Rio de Janeiro", "RJ", "GLO" );
        $check = validaSigla( $aeroporto3->getSigla() );
        if ( $check ) {
          $aeroporto3->save();
          cadastraAeroporto( $aeroporto3 );
        } else {
          throw new Error( "Aeroporto não cadastrado por conta de sigla em formato errado!" );
        }
        if ( count( $this->aeroportos ) != 3 ) {
          throw new Error( "Erro na função de cadastro!" );
        }

        // -------------------------------------------------------------------------------------------------------
        // Testando métodos Get

        try {
          if( $aeroporto3->getCidade() != 'Rio de Janeiro' )
            throw new Exception ( "Erro na atribuição da cidade onde fica localizado o aeroporto." );
        } catch ( Exception $e ){
          echo $e;
        }
        
        try {
          if ( $aeroporto3->getEstado() != 'RJ' )
            throw new Exception ( "Erro na atribuição do estado onde fica localizado o aeroporto." );
        } catch( Exception $e ){
  		    echo $e;
        }
        
        try {
          if ( $aeroporto3->getSigla() != 'GLO' )
            throw new Exception ( "Erro na atribuição da sigla do aeroporto." );
        } catch ( Exception $e ){
     	  	echo $e;
        }
    }

    // Testando função listar aeroporto
    listarAeroporto();

    if ( 0 ) {
        $aeroporto4 = new Aeroporto( "Governador Aluizio Alves", "RN", "GAA" );
        $check = validaSigla( $aeroporto2->getSigla() );
        if ( $check ) {
          $aeroporto4->save();
          cadastraAeroporto( $aeroporto4 );
        } else {
          throw new Error( "Aeroporto não cadastrado por conta de sigla em formato errado!" );
        }
    }

    // Testando funções alterarAeroporto e excluirAeroporto
    alterarAeroporto( $aeroporto3, $aeroporto4 );
    excluirAeroporto( $aeroporto4 );

    // Conferindo se as chamadas acima funcionaram
    listarAeroporto();

    // Criando companhias
    if ( 0 ) {
        $companhia1 = new Companhia( "Latam", "LAT", "LATAM S.A", "01.234.567/0001-89", "LT" );
        $companhia2 = new Companhia( "Azul", "AZL", "AZUL S.A", "12.345.678/0001-89", "AZ" );
        $companhia3 = new Companhia( "GOL", "GOL", "GOL S.A", "87.654.321/0001-89", "GL" );
        $aeroporto1->addCompanhia( $companhia1 );
        $aeroporto2->addCompanhia( $companhia1 );
        $aeroporto3->addCompanhia( $companhia1 );
        $aeroporto1->addCompanhia( $companhia2 );
        $aeroporto2->addCompanhia( $companhia2 );
        $aeroporto3->addCompanhia( $companhia2 );
        $aeroporto1->addCompanhia( $companhia3 );
        $aeroporto2->addCompanhia( $companhia3 );
        $aeroporto3->addCompanhia( $companhia3 );
      
        try{
          if( sizeof( $aeroporto1->getCompanhias() ) != 3 ) 
            throw new Exception ("Ocorreu algum erro nas funções addCompanhia ou getCompanhias.");
          } catch ( exception $e ) {
          echo $e;
        }

        $aeroporto1->save();
        $aeroporto2->save();
        $aeroporto3->save();
    }

    // -------------------------------------------------------------------------------------------------------

    if (1) {
        $aeroportos = getRecords();
        print_r($aeroportos);
    }
}