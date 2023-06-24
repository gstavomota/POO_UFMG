<?php

require_once "aeroporto.php";
require_once "assento.php";
require_once "calculo_tarifa_strategy.php";
require_once "companhia_aerea.php";
require_once "franquia_de_bagagem.php";
require_once "identificadores.php";
require_once "temporal.php";
require_once "Equatable.php";

class Voo implements Equatable
{
    private CodigoVoo $codigo;
    private SiglaAeroporto $aeroporto_de_saida;
    private SiglaAeroporto $aeroporto_de_chegada;
    private Tempo $hora_de_partida;
    private Duracao $duracao_estimada;
    /**
     * @var DiaDaSemana[]
     */
    private array $dias_da_semana;
    private RegistroDeAeronave $aeronave_padrao;
    private int $capacidade_passageiros;
    private float $capacidade_carga;
    private float $tarifa;
    private int $pontuacaoMilhagem;

    public function __construct(
        CodigoVoo          $codigo,
        SiglaAeroporto     $aeroporto_de_saida,
        SiglaAeroporto     $aeroporto_de_chegada,
        Tempo              $hora_de_partida,
        Duracao            $duracao_estimada,
        array              $dias_da_semana,
        RegistroDeAeronave $aeronave_padrao,
        int                $capacidade_passageiros,
        float              $capacidade_carga,
        float              $tarifa,
        int                $pontuacaoMilhagem
    )
    {
        $this->codigo = $codigo;
        $this->aeroporto_de_saida = $aeroporto_de_saida;
        $this->aeroporto_de_chegada = $aeroporto_de_chegada;
        $this->hora_de_partida = $hora_de_partida;
        $this->duracao_estimada = $duracao_estimada;
        $this->dias_da_semana = Voo::validarDiasDaSemana($dias_da_semana);
        $this->aeronave_padrao = $aeronave_padrao;
        $this->capacidade_passageiros = $capacidade_passageiros;
        $this->capacidade_carga = $capacidade_carga;
        $this->tarifa = $tarifa;
        $this->pontuacaoMilhagem = $pontuacaoMilhagem;
    }

    /**
     * Validações
     */
    private static function validarDiasDaSemana(array $diasDaSemana): array
    {
        if (empty($diasDaSemana)) {
            throw new InvalidArgumentException("Dias da semana não pode ser vazio");
        }
        return $diasDaSemana;
    }

    /** Getters
     *
     */

    /** Retorna o código do Voo
     * @return CodigoVoo
     */
    public function getCodigo(): CodigoVoo
    {
        return $this->codigo;
    }

    /** Retorna a CompanhiaAerea do Voo
     * @return SiglaCompanhiaAerea
     */
    public function getSiglaCompanhiaAerea(): SiglaCompanhiaAerea
    {
        return $this->codigo->getSiglaDaCompanhia();
    }

    /** Retorna o Aeroporto de saida do Voo
     * @return SiglaAeroporto
     */
    public function getAeroportoSaida(): SiglaAeroporto
    {
        return $this->aeroporto_de_saida;
    }

    /** Retorna o Aeroporto de chegada do Voo
     * @return SiglaAeroporto
     */
    public function getAeroportoChegada(): SiglaAeroporto
    {
        return $this->aeroporto_de_chegada;
    }

    /** Retorna a hora de partida do Voo
     * @return Tempo
     */
    public function getHoraDePartida(): Tempo
    {
        return $this->hora_de_partida;
    }

    /** Retorna a duração estimada do Voo
     * @return Duracao
     */
    public function getDuracaoEstimada(): Duracao
    {
        return $this->duracao_estimada;
    }

    /** Retorna os dias da semana do Voo
     * @return DiaDaSemana[]
     */
    public function getDiasDaSemana(): array
    {
        return $this->dias_da_semana;
    }

    /** Retorna a Aeronave padrão do Voo
     * @return RegistroDeAeronave
     */
    public function getAeronavePadrao(): RegistroDeAeronave
    {
        return $this->aeronave_padrao;
    }

    /** Retorna a capacidade de passageiros do Voo
     * @return int
     */
    public function getCapacidadeDePassageiros(): int
    {
        return $this->capacidade_passageiros;
    }

    /** Retorna a capacidade de carga do Voo
     * @return float
     */
    public function getCapacidadeCarga(): float
    {
        return $this->capacidade_carga;
    }

    /** Retorna a tarifa do Voo
     * @return float
     */
    public function getTarifa(): float
    {
        return $this->tarifa;
    }

    /** Retorna a pontuacao de milhagem desse Voo
     * @return int
     */
    public function getPontuacaoMilhagem(): int
    {
        return $this->pontuacaoMilhagem;
    }

    /** Methods
     *
     */

    /** Calcula a tarifa para um cliente com franquias
     * @param bool $cliente_vip
     * @param FranquiasDeBagagem $franquias
     * @param float $tarifa_franquia
     * @return float
     */
    public function calculaTarifa(bool $cliente_vip, FranquiasDeBagagem $franquias, float $tarifa_franquia): float
    {
        return calculo_tarifa_strategy_for($cliente_vip, $this->tarifa, $tarifa_franquia)->calcula($franquias);
    }

    /** Constroi os assentos para a capacidade de passageiros desse Voo
     * @return HashMap<CodigoDoAssento, Assento>
     */
    public function construirAssentos(): HashMap
    {
        $gerador = new GeradorDeCodigoDoAssento($this->capacidade_passageiros, 0.0);
        $assentos = $gerador->gerar_todos();
        $hashmap_assentos = new HashMap();
        foreach ($assentos as $codigo_assento) {
            $hashmap_assentos->put($codigo_assento, new Assento($codigo_assento));
        }
        return $hashmap_assentos;
    }

    public function eq(Equatable $other): bool
    {
        if (!$other instanceof self) {
            throw new EquatableTypeException();
        }
        return
            $this->codigo->eq($other->codigo) &&
            $this->aeroporto_de_saida->eq($other->aeroporto_de_saida) &&
            $this->aeroporto_de_chegada->eq($other->aeroporto_de_chegada) &&
            $this->hora_de_partida->eq($other->hora_de_partida) &&
            $this->duracao_estimada->eq($other->duracao_estimada) &&
            $this->dias_da_semana == $other->dias_da_semana &&
            $this->aeronave_padrao->eq($other->aeronave_padrao) &&
            $this->capacidade_passageiros == $other->capacidade_passageiros &&
            $this->capacidade_carga == $other->capacidade_carga &&
            $this->tarifa == $other->tarifa &&
            $this->pontuacaoMilhagem == $other->pontuacaoMilhagem;
    }
}

?>