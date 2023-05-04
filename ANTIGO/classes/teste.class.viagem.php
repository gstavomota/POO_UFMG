<?php
    require_once('class.viagem.php');

    // Instanciando um novo objeto da classe viagem
    if ( 1 ) {
        $dataDec = new DateTime();
        $dataDec->setDate( 2023, 03, 27);
        $dataDec->settime( 8, 5, 0);

        $dataAte = new DateTime();
        $dataAte->setDate( 2023, 03, 28);
        $dataAte->setime( 5, 4, 0);

        $duracao = 120;
      
        $viagem1 = new Viagem ($dataDec, $DataAte, $duracao);
        $viagem1->save();
}
/*
// Carregando registros já persistidos da classe funcionario
    if ( 1 ) {
        $funcionarios = funcionario::getRecords();
        print_r($funcionarios);
    }

    // Criando um ponto offline
    if ( 0 ) {
        $data = new DateTime();
        $data->setDate( 2023, 03, 27 );
        $data->setTime( 8, 5, 0);
        $pOff = new pontoOffline( $data );
        $pOff->setTipo( TipoPonto::INICIO );        
        $pOff->save();
        //print_r( $pOff );
    }

    // Carregando registros já persistidos da classe ponto offline
    if ( 0 ) {
        $pontos = pontoOffline::getRecords();
        print_r($pontos);
    }

    // Criando um ponto online
    if ( 0 ) {
        $dataAgora = new DateTime("now");   
        $pOn = new pontoOnline( $dataAgora );
        $pOn->save();
        //print_r($pOn);
    }

    // Carregando registros já persistidos da classe ponto online
    if ( 0 ) {
        $pontos = pontoOnline::getRecords();
        print_r($pontos);
    }

    // Procurando pelo funcionário Bill Gates e adicionando pontos
    if ( 0 ) {
        $funcionarios = funcionario::getRecordsByField( 'nome', 'Bill Gates' );
        //print_r($funcionarios);
        $funcBill = $funcionarios[0];
        $funcBill->addPonto($pOff);        
        $funcBill->addPonto($pOn);
        $funcBill->save();
    }

*/
