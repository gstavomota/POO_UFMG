<?php 
    include_once("../class.voo.php");
    include_once("../class.aeroporto.php");
    
    $frequencia = 2 ;
    $decolagem = new DateTime();
    $aterrissagem = new DateTime();
    $duracao = 60;
    $sigla = "SIG";

    $aeroporto1 = new Aeroporto ("Sao Paulo", "SP", "GRU");
    $aeroporto2 = new Aeroporto ("Belo Horizonte", "MG", "GIG");

    $voo = new Voo($frequencia, $decolagem, $aterrissagem, $duracao, $sigla, $aeroporto1, $aeroporto2);

    // teste de Gets
    echo $voo->getFrequencia();
    echo "\n";
    echo $voo->getHorarioDecolagem()->format('H:i:s');
    echo "\n";
    echo $voo->getHorarioAterrissagem()->format('H:i:s');
    echo "\n";
    echo $voo->getDuracao();
    echo "\n";
    echo $voo->getSigla();
    echo "\n";
    echo $voo->exibeVoo();