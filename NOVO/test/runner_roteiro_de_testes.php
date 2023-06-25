<?php

require_once "assento.php";
require_once "calculo_tarifa_strategy.php";
require_once "companhia_aerea.php";
require_once "encontrar_voo_strategy.php";
require_once "endereco.php";
require_once "enum_to_array.php";
require_once "franquia_de_bagagem.php";
require_once "HashMap.php";
require_once "identificadores.php";
require_once "passageiroVip.php";
require_once "passagem.php";
require_once "Pessoa.php";
require_once "programaDeMilhagem.php";
require_once "roteiro_de_testes.php";
require_once "suite.php";
require_once "suite_test.php";
require_once "temporal.php";
require_once "tripulacao.php";
require_once "viagem_builder.php";
require_once "voo.php";
require_once "../classes/log.php";

class AccumullatingStdoutLogOutputter implements LogOutputter
{
    private string $contents = "";

    public function output(LogEntry $entry): void
    {
        $this->contents .= $entry . "\n";
    }

    public function flush(): void
    {
        echo $this->contents;
        $this->contents = "";
    }
}

$fileOutputter = new AccumullatingStdoutLogOutputter();
log::getInstance()->setLogOutputter($fileOutputter);
(new TestRunner())
    // Inicio roteiro_de_testes.php
    ->addCase(new RoteiroDeTestesTestCase())
    // Fim roteiro_de_testes.php
    ->setCheckShowPolicy(CheckShowPolicy::FAILURE)
    ->run();

$fileOutputter->flush();