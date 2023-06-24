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
        return $this->id;
    }

    public function getNomePassageiro(): string
    {
        return $this->nomePassageiro;
    }

    public function getSobrenomePassageiro(): string
    {
        return $this->sobrenomePassageiro;
    }

    public function getSiglaAeroportoDeChegada(): SiglaAeroporto
    {
        return $this->siglaAeroportoDeChegada;
    }

    public function getSiglaAeroportoDeSaida(): SiglaAeroporto
    {
        return $this->siglaAeroportoDeSaida;
    }

    public function getMomentoMaximoDeEmbarque(): DataTempo
    {
        return $this->momentoMaximoDeEmbarque;
    }

    public function getAssento(): CodigoDoAssento
    {
        return $this->assento;
    }
}

?>