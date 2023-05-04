<?php
    include_once("class.aeroporto.php");
    include_once("class.companhia.php");
    include_once("class.aeronave.php"); 

    $aeroporto = new Aeroporto("São Paulo", "SP");

    // Teste de cadastro da sigla
    $aeroporto->validaSigla("GRU"); // Sucesso
    echo "Sigla: " . $aeroporto->getSigla() . "\n"; // Resultado esperado: GRU

    $aeroporto->validaSigla("123"); // Erro
    echo "Sigla: " . $aeroporto->getSigla() . "\n"; // Resultado esperado: GRU

    $aeroporto->validaSigla("CGH"); // Sucesso
    echo "Sigla: " . $aeroporto->getSigla() . "\n"; // Resultado esperado: CGH

    // Teste de adição de companhias
    // Para o construtor de uma companhia, precisa, nessa ordem: nome, código, razão social, CNPJ e sigla
    $companhia = new Companhia("Companhia Aérea", "CAA", "Companhia Aérea S.A.", "01.234.567/0001-89", "CA");
    $aeroporto->addCompanhia($companhia);
    echo "Companhias: " . count($aeroporto->getCompanhias()) . "\n"; // Resultado esperado: 1

    $companhia = new Companhia("Companhia B", "CMB", "Companhia B S.A.", "23.456.789/0001-12", "CB");
    $aeroporto->addCompanhia($companhia);
    echo "Companhias: " . count($aeroporto->getCompanhias()) . "\n"; // Resultado esperado: 2

    // Teste de exceção
    $companhia = "Companhia C";
    $aeroporto->addCompanhia($companhia); // Erro
    echo "Companhias: " . count($aeroporto->getCompanhias()) . "\n"; // Resultado esperado: 2
