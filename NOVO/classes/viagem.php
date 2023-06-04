<?php
require_once('identificadores.php');
require_once('tripulacao.php');

class Viagem
{
    private RegistroDeViagem $registro;
    private CodigoVoo $codigo_do_voo;
    private SiglaAeroporto $aeroporto_de_saida;
    private SiglaAeroporto $aeroporto_de_chegada;
    private DataTempo $hora_de_partida;
    private DataTempo $hora_de_chegada;
    private RegistroDeAeronave $aeronave;
    private Tripulacao $tripulacao;
    private float $tarifa;
    private float $tarifa_franquia;
    /**
     * @var HashMap<CodigoDoAssento, Assento>
     */
    private HashMap $assentos;

    public function __construct(
        RegistroDeViagem   $registro,
        CodigoVoo          $codigo_do_voo,
        SiglaAeroporto     $aeroporto_de_saida,
        SiglaAeroporto     $aeroporto_de_chegada,
        DataTempo          $hora_de_partida,
        DataTempo          $hora_de_chegada,
        RegistroDeAeronave $aeronave,
        Tripulacao         $tripulacao,
        float              $tarifa,
        float              $tarifa_franquia,
        HashMap            $assentos
    )
    {
        $this->registro = $registro;
        $this->codigo_do_voo = $codigo_do_voo;
        $this->aeroporto_de_saida = $aeroporto_de_saida;
        $this->aeroporto_de_chegada = $aeroporto_de_chegada;
        $this->hora_de_partida = $hora_de_partida;
        $this->hora_de_chegada = $hora_de_chegada;
        $this->aeronave = $aeronave;
        $this->tripulacao = $tripulacao;
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
        $this->assentos = $assentos;
    }

    public function getRegistro(): RegistroDeViagem
    {
        return $this->registro;
    }

    public function getCodigoDoVoo(): CodigoVoo
    {
        return $this->codigo_do_voo;
    }

    public function getAeroportoDeSaida(): SiglaAeroporto
    {
        return $this->aeroporto_de_saida;
    }

    public function getAeroportoDeChegada(): SiglaAeroporto
    {
        return $this->aeroporto_de_chegada;
    }

    public function getHoraDePartida(): DataTempo
    {
        return $this->hora_de_partida;
    }

    public function getHoraDeChegada(): DataTempo
    {
        return $this->hora_de_chegada;
    }

    public function getAeronave(): RegistroDeAeronave
    {
        return $this->aeronave;
    }

    public function getTripulacao(): Tripulacao
    {
        return $this->tripulacao;
    }

    public function getTarifa(): float
    {
        return $this->tarifa;
    }

    public function getTarifaFranquia(): float
    {
        return $this->tarifa_franquia;
    }

    /**
     * @return HashMap<CodigoDoAssento, Assento>
     */
    public function getAssentos(): HashMap
    {
        return $this->assentos;
    }
}

?>