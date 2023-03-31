<?php
include_once("PontoOnline.php");
enum Status{
    case NA;
    case OK;
    case NOK;
};
class PontoOffline extends PontoOnline {
    private Status $statusAprovacao;

    public function __construct(){}

    public function setStatusAprovacao( Status $p_status ){
        $this->statusAprovacao = $p_status;
    }
};