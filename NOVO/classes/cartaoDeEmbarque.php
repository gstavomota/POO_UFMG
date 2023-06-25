<?php
require_once("identificadores.php");
require_once "log.php";

class CartaoDeEmbarque
{
    private RegistroDeCartaoDeEmbarque $id;
    private string $nomePassageiro;
    private string $sobrenomePassageiro;
    private SiglaAeroporto $siglaAeroportoDeSaida;
    private SiglaAeroporto $siglaAeroportoDeChegada;
    private DataTempo $momentoMaximoDeEmbarque;
    private CodigoDoAssento $assento;


    public function __construct(
        RegistroDeCartaoDeEmbarque $id,
        string                     $nomePassageiro,
        string                     $sobrenomePassageiro,
        SiglaAeroporto             $siglaAeroportoDeSaida,
        SiglaAeroporto             $siglaAeroportoDeChegada,
        DataTempo                  $momentoMaximoDeEmbarque,
        CodigoDoAssento            $assento
    )
    {
        $this->id = $id;
        $this->nomePassageiro = $nomePassageiro;
        $this->sobrenomePassageiro = $sobrenomePassageiro;
        $this->siglaAeroportoDeSaida = $siglaAeroportoDeSaida;
        $this->siglaAeroportoDeChegada = $siglaAeroportoDeChegada;
        $this->momentoMaximoDeEmbarque = $momentoMaximoDeEmbarque;
        $this->assento = $assento;
    }

    public function getId(): RegistroDeCartaoDeEmbarque
    {
        return log::getInstance()->logRead($this->id);
    }

    public function getNomePassageiro(): string
    {
        return log::getInstance()->logRead($this->nomePassageiro);
    }

    public function getSobrenomePassageiro(): string
    {
        return log::getInstance()->logRead($this->sobrenomePassageiro);
    }

    public function getSiglaAeroportoDeChegada(): SiglaAeroporto
    {
        return log::getInstance()->logRead($this->siglaAeroportoDeChegada);
    }

    public function getSiglaAeroportoDeSaida(): SiglaAeroporto
    {
        return log::getInstance()->logRead($this->siglaAeroportoDeSaida);
    }

    public function getMomentoMaximoDeEmbarque(): DataTempo
    {
        return log::getInstance()->logRead($this->momentoMaximoDeEmbarque);
    }

    public function getAssento(): CodigoDoAssento
    {
        return log::getInstance()->logRead($this->assento);
    }
}

?>