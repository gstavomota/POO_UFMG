<?php
require_once "encontrar_voo_strategy.php";
require_once "log.php";

interface EncontrarMelhorVooStrategy
{
    /** Encontra a melhor lista de voos saindo do aeroporto de saida e chegando no aeroporto de chegada na data desejada
     * @param bool $clienteVip
     * @param Data $data
     * @param SiglaAeroporto $aeroportoDeSaida
     * @param SiglaAeroporto $aeroportoDeChegada
     * @param FranquiasDeBagagem $franquiasDeBagagem
     * @param float $tarifaFranquia
     * @param HashMap<CodigoVoo, Voo> $voos
     * @return CodigoVoo[]
     */
    public function encontrar(bool $clienteVip, Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, FranquiasDeBagagem $franquiasDeBagagem, float $tarifaFranquia, HashMap $voos): array;
}

class EncontrarMelhorVooPreferindoSemConexaoEEmPelaMenorTarifaStrategy implements EncontrarMelhorVooStrategy
{
    public function encontrar(bool $clienteVip, Data $data, SiglaAeroporto $aeroportoDeSaida, SiglaAeroporto $aeroportoDeChegada, FranquiasDeBagagem $franquiasDeBagagem, float $tarifaFranquia, HashMap $voos): array
    {

        $voos_sem_conexao = (new EncontrarVoosSemConexaoStrategy())->encontrar($data, $aeroportoDeSaida, $aeroportoDeChegada, $voos->values());
        $melhor_tarifa = INF;

        if (count($voos_sem_conexao) != 0) {
            /**
             * @var Voo $melhor_voo
             */
            $melhor_voo = null;
            foreach ($voos_sem_conexao as [$codigo_voo]) {
                /**
                 * @var Voo $voo
                 */
                $voo = $voos->get($codigo_voo);
                $tarifa = $voo->calculaTarifa($clienteVip, $franquiasDeBagagem, $tarifaFranquia);

                if ($tarifa >= $melhor_tarifa)
                    continue;

                $melhor_voo = $voo;
                $melhor_tarifa = $tarifa;
            }

            return log::getInstance()->logCall([$melhor_voo->getCodigo()]);
        }

        $pares_de_voos = (new EncontrarVoosComUmaConexaoStrategy())->encontrar($data, $aeroportoDeSaida, $aeroportoDeChegada, $voos->values());

        if (count($pares_de_voos) == 0) {
            return [];
        }

        /**
         * @var Voo $melhor_voo_intermediario
         */
        $melhor_voo_intermediario = null;

        /**
         * @var Voo $melhor_voo_final
         */
        $melhor_voo_final = null;

        foreach ($pares_de_voos as $par_de_voos) {
            [$codigo_voo_intermediario, $codigo_voo_final] = $par_de_voos;
            /**
             * @var Voo $voo_intermediario
             */
            $voo_intermediario = $voos->get($codigo_voo_intermediario);
            /**
             * @var Voo $voo_final
             */
            $voo_final = $voos->get($codigo_voo_final);
            $tarifa_intermediario = $voo_intermediario->calculaTarifa($clienteVip, $franquiasDeBagagem, $tarifaFranquia);
            $tarifa_final = $voo_final->calculaTarifa($clienteVip, $franquiasDeBagagem, $tarifaFranquia);
            $tarifa = $tarifa_intermediario + $tarifa_final;

            if ($tarifa >= $melhor_tarifa) {
                continue;
            }

            $melhor_voo_intermediario = $voo_intermediario;
            $melhor_voo_final = $voo_final;
            $melhor_tarifa = $tarifa;
        }

        return log::getInstance()->logCall([$melhor_voo_intermediario->getCodigo(), $melhor_voo_final->getCodigo()]);
    }
}