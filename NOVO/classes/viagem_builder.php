<?php
require_once('assento.php');
require_once('calculo_tarifa_strategy.php');
require_once('franquia_de_bagagem.php');
require_once('identificadores.php');
require_once('temporal.php');
require_once('viagem.php');
require_once('tripulacao.php');
require_once('voo.php');
require_once "HashMap.php";

class ViagemBuilder
{
    private float $carga, $tarifa, $tarifa_franquia;
    private int $passageiros, $pontuacaoMilhagem;
    /**
     * @var HashMap<CodigoDoAssento, Assento>
     */
    private HashMap $assentos;
    private GeradorDeRegistroDeViagem $gerador_de_registro;
    private RegistroDeViagem $registro;
    private RegistroDeAeronave $aeronave;
    private Tripulacao $tripulacao;
    private Data $data;
    private CodigoVoo $codigo_do_voo;

    private SiglaAeroporto $aeroporto_de_saida, $aeroporto_de_chegada;

    private DataTempo $hora_de_partida, $hora_de_chegada;

    public function addTarifaFranquia(float $tarifa_franquia): self
    {
        $this->tarifa_franquia = $tarifa_franquia;
        return $this;
    }

    public function adicionarGeradorDeRegistro(GeradorDeRegistroDeViagem $gerador_de_registro): self
    {
        $this->gerador_de_registro = $gerador_de_registro;
        return $this;
    }

    public function gerarRegistro(): self
    {
        $this->registro = $this->gerador_de_registro->gerar();
        return $this;
    }

    public function addData(Data $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function addVoo(Voo $voo): self
    {
        $this->carga = $voo->getCapacidadeCarga();
        $this->passageiros = $voo->getCapacidadeDePassageiros();
        $this->codigo_do_voo = $voo->getCodigo();
        $this->tarifa = $voo->getTarifa();
        $this->aeroporto_de_saida = $voo->getAeroportoSaida();
        $this->aeroporto_de_chegada = $voo->getAeroportoChegada();
        $this->assentos = $voo->construirAssentos();
        $this->pontuacaoMilhagem = $voo->getPontuacaoMilhagem();
        return $this;
    }

    public function addAeronave(Aeronave $aeronave): self
    {
        $carga = $aeronave->getCapacidadeCarga();
        $passageiros = $aeronave->getCapacidadePassageiros();
        if ($carga != $this->carga || $passageiros != $this->passageiros) {
            throw new InvalidArgumentException("Essa aeronave não tem a carga e passageiros necessários");
        }
        $this->aeronave = $aeronave->getRegistro();
        return $this;
    }

    function temAssentosLiberados(): bool
    {
        foreach ($this->assentos as $assento) {
            if ($assento->vazio()) {
                return true;
            }
        }
        return false;
    }

    function assentoEstaLiberado(CodigoDoAssento $assento): bool
    {
        if (!isset($this->assentos["{$assento}"])) {
            throw new InvalidArgumentException('Assento não encontrado');
        }
        return $this->assentos["{$assento}"]->vazio();
    }

    function temCargaDisponivelParaFranquias(FranquiasDeBagagem $franquias): bool
    {
        $carga_usada = 0;
        foreach ($this->assentos as $assento) {
            $carga_usada += $assento->getFranquias()->getCarga();
        }
        if ($carga_usada + $franquias->getCarga() > $this->carga) {
            return false;
        }
        return true;
    }

    function codigoAssentoLiberado(): CodigoDoAssento
    {
        foreach ($this->assentos as $assento) {
            if ($assento->vazio()) {
                return $assento->getCodigo();
            }
        }
        // TODO: Custom exception type
        throw new Exception('Não tem assentos liberados');
    }

    function reservarAssento(bool $cliente_vip, RegistroDePassagem $registro_passagem, FranquiasDeBagagem $franquias, CodigoDoAssento $assento_desejado): float
    {
        if (!$this->temCargaDisponivelParaFranquias($franquias)) {
            throw new Exception('Não tem carga para franquia disponível');
        }
        if (!isset($this->assentos["{$assento_desejado}"])) {
            throw new InvalidArgumentException('Assento não encontrado');
        }
        $assento = $this->assentos["{$assento_desejado}"];
        if ($assento->preenchido()) {
            throw new Exception('O assento está preenchido');
        }
        $assento->reservar($registro_passagem, $franquias);
        return calculo_tarifa_strategy_for($cliente_vip, $this->tarifa, $this->tarifa_franquia)->calcula($franquias);
    }

    function liberarAssento(RegistroDePassagem $registro_passagem, CodigoDoAssento $assento): void
    {
        if (!isset($this->assentos["{$assento}"])) {
            throw new InvalidArgumentException('Assento não encontrado');
        }
        $this->assentos["{$assento}"]->liberar($registro_passagem);
    }

    function addHoraDePartidaEHoraDeChegada(DataTempo $hora_de_partida, DataTempo $hora_de_chegada): self
    {
        $this->hora_de_partida = $hora_de_partida;
        $this->hora_de_chegada = $hora_de_chegada;
        return $this;
    }

    /**
     * @return RegistroDeViagem
     */
    public function getRegistro(): RegistroDeViagem
    {
        return $this->registro;
    }

    /**
     * @return CodigoVoo
     */
    public function getCodigoDoVoo(): CodigoVoo
    {
        return $this->codigo_do_voo;
    }

    public function build(): Viagem
    {
        return new Viagem(
            $this->registro,
            $this->codigo_do_voo,
            $this->aeroporto_de_chegada,
            $this->aeroporto_de_saida,
            $this->hora_de_partida,
            $this->hora_de_chegada,
            $this->aeronave,
            $this->tripulacao,
            $this->tarifa,
            $this->tarifa_franquia,
            $this->assentos,
        );
    }
}