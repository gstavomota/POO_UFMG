<?php

require_once "aeroporto.php";
require_once "assento.php";
require_once "calculo_tarifa_strategy.php";
require_once "companhia_aerea.php";
require_once "franquia_de_bagagem.php";
require_once "identificadores.php";
require_once "temporal.php";

class Voo
{
    private CodigoVoo $codigo;
    private CompanhiaAerea $companhia_aerea;
    private SiglaAeroporto $aeroporto_de_saida;
    private SiglaAeroporto $aeroporto_de_chegada;
    private Data $hora_de_partida;
    private Duracao $duracao_estimada;
    private DiaDaSemana $dias_da_semana;
    private RegistroDeAeronave $aeronave_padrao;
    private int $capacidade_passageiros;
    private float $capacidade_carga;
    private float $tarifa;

    public function __construct(
        CodigoVoo          $codigo,
        CompanhiaAerea     $companhia_aerea,
        SiglaAeroporto     $aeroporto_de_saida,
        SiglaAeroporto     $aeroporto_de_chegada,
        Data               $hora_de_partida,
        Duracao            $duracao_estimada,
        DiaDaSemana        $dias_da_semana,
        RegistroDeAeronave $aeronave_padrao,
        int                $capacidade_passageiros,
        float              $capacidade_carga,
        float              $tarifa
    )
    {
        $this->codigo = $codigo;
        $this->companhia_aerea = $companhia_aerea;
        $this->aeroporto_de_saida = $aeroporto_de_saida;
        $this->aeroporto_de_chegada = $aeroporto_de_chegada;
        $this->hora_de_partida = $hora_de_partida;
        $this->duracao_estimada = $duracao_estimada;
        $this->dias_da_semana = $dias_da_semana;
        $this->aeronave_padrao = $aeronave_padrao;
        $this->capacidade_passageiros = $capacidade_passageiros;
        $this->capacidade_carga = $capacidade_carga;
        $this->tarifa = $tarifa;
    }

    public function calculaTarifa(bool $cliente_vip, FranquiasDeBagagem $franquias, float $tarifa_franquia)
    {
        return calculo_tarifa_strategy_for($cliente_vip, $this->tarifa, $tarifa_franquia)->calcula($franquias);
    }

    public function construirAssentos(): array
    {
        $gerador = new GeradorDeCodigoDoAssento($this->capacidade_passageiros, 0.0);
        $assentos = $gerador->gerar_todos();
        $dict_assentos = array();
        foreach ($assentos as $codigo_assento) {
            $dict_assentos[$codigo_assento] = new Assento($codigo_assento);
        }
        return $dict_assentos;
    }

    public function getCodigo(): CodigoVoo
    {
        return $this->codigo;
    }

    public function getCompanhiaAerea(): CompanhiaAerea
    {
        return $this->companhia_aerea;
    }

    public function getAeroportoSaida(): SiglaAeroporto
    {
        return $this->aeroporto_de_saida;
    }

    public function getAeroportoChegada(): SiglaAeroporto
    {
        return $this->aeroporto_de_chegada;
    }

    public function getHoraDePartida(): Data
    {
        return $this->hora_de_partida;
    }

    public function getDuracaoEstimada(): Duracao
    {
        return $this->duracao_estimada;
    }

    public function getDiasDaSemana(): DiaDaSemana
    {
        return $this->dias_da_semana;
    }

    public function getAeronavePadrao(): RegistroDeAeronave
    {
        return $this->aeronave_padrao;
    }

    public function getCapacidadeDePassageiros(): int
    {
        return $this->capacidade_passageiros;
    }

    public function getCapacidadeCarga(): float
    {
        return $this->capacidade_carga;
    }

    public function getTarifa(): float
    {
        return $this->tarifa;
    }
}

?>