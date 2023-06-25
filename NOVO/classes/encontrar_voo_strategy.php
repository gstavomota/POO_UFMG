<?php
require_once "temporal.php";
require_once "identificadores.php";
require_once "voo.php";
require_once "log.php";

interface EncontrarVoosStrategy
{
    /** Encontra uma lista de voos saindo do aeroporto de saida e chegando no aeroporto de chegada na data desejada
     * @param Data $data
     * @param SiglaAeroporto $aeroportoDeSaida
     * @param SiglaAeroporto $aeroportoDeChegada
     * @param Voo[] $voos
     * @return CodigoVoo[][]
     */
    public function encontrar(Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, array $voos): array;
}

class EncontrarVoosSemConexaoStrategy implements EncontrarVoosStrategy
{
    private function vooDesejado(Voo $voo, Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada): bool
    {
        if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana()))
            return false;
        if (!$aeroportoDeSaida->eq($voo->getAeroportoSaida()))
            return false;
        if (!$aeroportoDeChegada->eq($voo->getAeroportoChegada()))
            return false;
        return true;
    }


    public function encontrar(Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, array $voos): array
    {

        /**
         * @var CodigoVoo[][] $resultado
         */
        $resultado = [];

        $vooDesejado = function (Voo $voo) use ($data, $aeroportoDeSaida, $aeroportoDeChegada) {
            return $this->vooDesejado($voo, $data, $aeroportoDeSaida, $aeroportoDeChegada);
        };

        /**
         * @var Voo $voo
         */
        foreach (array_filter($voos, $vooDesejado) as $voo) {
            $resultado[] = [$voo->getCodigo()];
        }

        return log::getInstance()->logCall($resultado);
    }
}

class EncontrarVoosComUmaConexaoStrategy implements EncontrarVoosStrategy
{
    private Duracao $duracaoDeConexao;
    public function __construct(?Duracao $duracaoDeConexao = null)
    {
        if (is_null($duracaoDeConexao)) {
            $duracaoDeConexao = Duracao::meiaHora();
        }
        $this->duracaoDeConexao = $duracaoDeConexao;
    }

    private function vooIntermediarioDesejado(Voo $voo, Data $data, SiglaAeroporto $aeroportoDeSaida): bool
    {
        if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
            return false;
        }
        if (!$aeroportoDeSaida->eq($voo->getAeroportoSaida())) {
            return false;
        }
        return true;
    }
    private function vooFinalDesejado(Voo $voo, Voo $vooIntermediario, Data $data, SiglaAeroporto $aeroportoDeChegada): bool
    {
        if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
            return false;
        }
        $aeroportoIntermediario = $vooIntermediario->getAeroportoChegada();
        if (!$voo->getAeroportoSaida()->eq($aeroportoIntermediario)) {
            return false;
        }
        if (!$voo->getAeroportoChegada()->eq($aeroportoDeChegada)) {
            return false;
        }
        $horaDeChegada = $vooIntermediario->getHoraDePartida()->add($vooIntermediario->getDuracaoEstimada());
        $tempoDaConexao = $horaDeChegada->add($this->duracaoDeConexao);
        if ($voo->getHoraDePartida()->st($tempoDaConexao)) {
            return false;
        }
        return true;
    }

    public function encontrar(Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, array $voos): array
    {

        /**
         * @var CodigoVoo[][] $resultado
         */
        $resultado = [];

        $vooIntermediarioDesejado = function (Voo $voo) use ($data, $aeroportoDeSaida) {
            return $this->vooIntermediarioDesejado($voo, $data, $aeroportoDeSaida);
        };

        $vooFinalDesejado = function (Voo $voo, Voo $vooIntermediario) use ($data, $aeroportoDeChegada) {
            return $this->vooFinalDesejado($voo, $vooIntermediario, $data, $aeroportoDeChegada);
        };

        /**
         * @var Voo $vooIntermediario
         */
        foreach (array_filter($voos, $vooIntermediarioDesejado) as $vooIntermediario) {
            $vooFinalDesejadoClosure = function (Voo $vooFinal) use ($vooIntermediario, $vooFinalDesejado) {
                return $vooFinalDesejado($vooFinal, $vooIntermediario);
            };
            /**
             * @var Voo $voo_final
             */
            foreach (array_filter($voos, $vooFinalDesejadoClosure) as $voo_final) {
                $resultado[] = [$vooIntermediario->getCodigo(), $voo_final->getCodigo()];
            }
        }

        return log::getInstance()->logCall($resultado);
    }
}