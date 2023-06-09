<?php
require_once "temporal.php";
require_once "identificadores.php";
require_once "voo.php";
interface EncontrarVoosStrategy {
    /** Encontra uma lista de voos saindo do aeroporto de saida e chegando no aeroporto de chegada na data desejada
     * @param Data $data
     * @param SiglaAeroporto $aeroportoDeSaida
     * @param SiglaAeroporto $aeroportoDeChegada
     * @param Voo[] $voos
     * @return CodigoVoo[][]
     */
    public function encontrar(Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, array $voos): array;
}

class EncontrarVoosSemConexaoStrategy implements EncontrarVoosStrategy {

    public function encontrar(Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, array $voos): array
    {

        /**
         * @var CodigoVoo[][] $voos
         */
        $voos = [];

        $voo_desejado = function (Voo $voo) use ($data, $aeroportoDeSaida, $aeroportoDeChegada) {
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana()))
                return false;
            if (!$aeroportoDeSaida->eq($voo->getAeroportoSaida()))
                return false;
            if (!$aeroportoDeChegada->eq($voo->getAeroportoChegada()))
                return false;
            return true;
        };

        /**
         * @var Voo $voo
         */
        foreach (array_filter($voos, $voo_desejado) as $voo) {
            $voos[] = [$voo->getCodigo()];
        }

        return $voos;
    }
}

class EncontrarVoosComConexaoStrategy implements EncontrarVoosStrategy {

    public function encontrar(Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, array $voos): array
    {

        /**
         * @var CodigoVoo[][] $voos
         */
        $voos = [];

        $voo_intermediario_desejado = function (Voo $voo) use ($data, $aeroportoDeSaida) {
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
                return false;
            }
            if (!$aeroportoDeSaida->eq($voo->getAeroportoSaida())) {
                return false;
            }
            return true;
        };

        $voo_final_desejado = function (Voo $voo, SiglaAeroporto $aeroporto_intermediario, Tempo $hora_de_chegada) use ($data, $aeroportoDeChegada) {
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
                return false;
            }
            if (!$voo->getAeroportoSaida()->eq($aeroporto_intermediario)) {
                return false;
            }
            if (!$voo->getAeroportoChegada()->eq($aeroportoDeChegada)) {
                return false;
            }
            $tempo_da_conexao = $hora_de_chegada->add(Duracao::meiaHora());
            if ($voo->getHoraDePartida()->st($tempo_da_conexao)) {
                return false;
            }
            return true;
        };

        /**
         * @var Voo $voo_intermediario
         */
        foreach (array_filter($voos, $voo_intermediario_desejado) as $voo_intermediario) {
            $hora_de_chegada = $voo_intermediario->getHoraDePartida()->add($voo_intermediario->getDuracaoEstimada());
            $voo_final_desejado_com_aeroporto_intermediario_e_hora_de_chegada = function (Voo $voo_final) use ($voo_intermediario, $voo_final_desejado, $hora_de_chegada) {
                return $voo_final_desejado($voo_final, $voo_intermediario->getAeroportoChegada(), $hora_de_chegada);
            };
            /**
             * @var Voo $voo_final
             */
            foreach (array_filter($voos, $voo_final_desejado_com_aeroporto_intermediario_e_hora_de_chegada) as $voo_final) {
                $voos[] = [$voo_intermediario->getCodigo(), $voo_final->getCodigo()];
            }
        }

        return $voos;
    }
}