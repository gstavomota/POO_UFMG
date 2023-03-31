<?php
include_once("PontoOnline.php");
include_once("PontoOffline.php");
class BancoHoras {
    private $mes;
    private $ano;
    private $saltoMesAnterior;
    private $saldoH;

    public function __construct( $p_mes, $p_ano, $p_saldoAnterior ){
        $this->ano = $p_ano;
        $this->saltoMesAnterior = $p_saldoAnterior;
        $this->mes = $p_mes;
    }

    private function calcularBancoHoras(){}

}