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
require_once "onibus.php";
require_once "coordenada.php";
require_once "tripulante_com_coordenada.php";
require_once "log.php";

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

    private DataTempo $hora_de_partida_estimada;

    private DataTempo $hora_de_partida, $hora_de_chegada;
    private Onibus $onibus;

    function __construct() {
        $this->tripulacao = new Tripulacao();
    }

    public function addTarifaFranquia(float $tarifa_franquia): self
    {
        $pre = clone $this;
        $this->tarifa_franquia = $tarifa_franquia;
        return log::getInstance()->logWrite($pre, $this);
    }

    public function adicionarGeradorDeRegistro(GeradorDeRegistroDeViagem $gerador_de_registro): self
    {
        $pre = clone $this;
        $this->gerador_de_registro = $gerador_de_registro;
        return log::getInstance()->logWrite($pre, $this);
    }

    public function gerarRegistro(): self
    {
        $pre = clone $this;
        $this->registro = $this->gerador_de_registro->gerar();
        return log::getInstance()->logWrite($pre, $this);
    }

    public function addData(Data $data): self
    {
        $pre = clone $this;
        $this->data = $data;
        return log::getInstance()->logWrite($pre, $this);
    }

    public function addVoo(Voo $voo): self
    {
        $pre = clone $this;
        $this->carga = $voo->getCapacidadeCarga();
        $this->passageiros = $voo->getCapacidadeDePassageiros();
        $this->codigo_do_voo = $voo->getCodigo();
        $this->tarifa = $voo->getTarifa();
        $this->aeroporto_de_saida = $voo->getAeroportoSaida();
        $this->aeroporto_de_chegada = $voo->getAeroportoChegada();
        $this->assentos = $voo->construirAssentos();
        $this->pontuacaoMilhagem = $voo->getPontuacaoMilhagem();
        $this->hora_de_partida_estimada = $voo->getHoraDePartida()->comData($this->data);
        $aeroportoDeSaida = Aeroporto::getRecordsBySigla($voo->getAeroportoSaida())[0];
        $this->onibus = new Onibus($aeroportoDeSaida->getCoordenada(), $voo->getHoraDePartida()->sub(new Duracao(0, 60*40)));
        return log::getInstance()->logWrite($pre, $this);
    }

    public function addAeronave(Aeronave $aeronave): self
    {
        $pre = clone $this;
        $carga = $aeronave->getCapacidadeCarga();
        $passageiros = $aeronave->getCapacidadePassageiros();
        if ($carga != $this->carga || $passageiros != $this->passageiros) {
            throw new InvalidArgumentException("Essa aeronave não tem a carga e passageiros necessários");
        }
        $this->aeronave = $aeronave->getRegistro();
        return log::getInstance()->logWrite($pre, $this);
    }

    function temAssentosLiberados(): bool
    {
        foreach ($this->assentos->values() as $assento) {
            if ($assento->vazio()) {
                return log::getInstance()->logCall(true);
            }
        }
        return log::getInstance()->logCall(false);
    }

    function assentoEstaLiberado(CodigoDoAssento $assento): bool
    {
        if (!$this->assentos->containsKey($assento)) {
            log::getInstance()->logThrow(new InvalidArgumentException('Assento não encontrado'));
        }
        return log::getInstance()->logCall($this->assentos->get($assento)->vazio());
    }

    function temCargaDisponivelParaFranquias(FranquiasDeBagagem $franquias): bool
    {
        $carga_usada = 0;
        foreach ($this->assentos->values() as $assento) {
            /**
             * @var ?FranquiasDeBagagem $franquiasAssento
             */
            $franquiasAssento = $assento->getFranquias();
            if (is_null($franquiasAssento)) {
                continue;
            }
            $carga_usada += $franquiasAssento->carga();
        }
        if ($carga_usada + $franquias->carga() > $this->carga) {
            return log::getInstance()->logCall(false);
        }
        return log::getInstance()->logCall(true);
    }

    function codigoAssentoLiberado(): CodigoDoAssento
    {
        foreach ($this->assentos->values() as $assento) {
            if ($assento->vazio()) {
                return log::getInstance()->logCall($assento->getCodigo());
            }
        }
        // TODO: Custom exception type
        log::getInstance()->logThrow(new Exception('Não tem assentos liberados'));
    }

    function reservarAssento(bool $cliente_vip, RegistroDePassagem $registro_passagem, FranquiasDeBagagem $franquias, CodigoDoAssento $assento_desejado): float
    {
        if (!$this->temCargaDisponivelParaFranquias($franquias)) {
            // TODO: Custom exception type
            log::getInstance()->logThrow(new Exception('Não tem carga para franquia disponível'));
        }
        if (!$this->assentos->containsKey($assento_desejado)) {
            log::getInstance()->logThrow(new InvalidArgumentException('Assento não encontrado'));
        }
        $assento = $this->assentos->get($assento_desejado);
        if ($assento->preenchido()) {
            log::getInstance()->logThrow(new PreenchimentoDeAssentoException('O assento está preenchido'));
        }
        $assento->reservar($registro_passagem, $franquias);
        return log::getInstance()->logCall(calculo_tarifa_strategy_for($cliente_vip, $this->tarifa, $this->tarifa_franquia)->calcula($franquias));
    }

    function liberarAssento(RegistroDePassagem $registro_passagem, CodigoDoAssento $codigoAssento): void
    {
        if (!$this->assentos->containsKey($codigoAssento)) {
            log::getInstance()->logThrow(new InvalidArgumentException('Assento não encontrado'));
        }
        $assento = $this->assentos->get($codigoAssento);
        if (!$assento->getPassagem()->eq($registro_passagem)) {
            log::getInstance()->logThrow(new Exception("Passagem errada"));
        }
        $assento->liberar();
    }

    function addTripulante(Tripulante $tripulante, ICoordenada $coordenada): self {
        $pre = clone $this;
        $registro = $tripulante->getRegistro();
        $this->onibus->adicionarTripulante(new TripulanteComCoordenada($registro, $coordenada));
        match ($tripulante->getCargo()) {
            Cargo::COMISSARIO => $this->tripulacao->addComissario($registro),
            Cargo::PILOTO => $this->tripulacao->setPiloto($registro),
            Cargo::COPILOTO => $this->tripulacao->setCopiloto($registro)
        };
        return log::getInstance()->logWrite($pre, $this);
    }

    /**
     * @return Onibus
     */
    public function getOnibus(): Onibus
    {
        return log::getInstance()->logRead($this->onibus);
    }

    function addHoraDePartidaEHoraDeChegada(DataTempo $hora_de_partida, DataTempo $hora_de_chegada): self
    {
        $pre = clone $this;
        $this->hora_de_partida = $hora_de_partida;
        $this->hora_de_chegada = $hora_de_chegada;
        return log::getInstance()->logWrite($pre, $this);
    }

    /**
     * @return RegistroDeViagem
     */
    public function getRegistro(): RegistroDeViagem
    {
        return log::getInstance()->logRead($this->registro);
    }

    /**
     * @return CodigoVoo
     */
    public function getCodigoDoVoo(): CodigoVoo
    {
        return log::getInstance()->logRead($this->codigo_do_voo);
    }

    /**
     * @return DataTempo
     */
    public function getHoraDePartidaEstimada(): DataTempo
    {
        return log::getInstance()->logRead($this->hora_de_partida_estimada);
    }

    /**
     * @return SiglaAeroporto
     */
    public function getAeroportoDeSaida(): SiglaAeroporto
    {
        return log::getInstance()->logRead($this->aeroporto_de_saida);
    }

    /**
     * @return SiglaAeroporto
     */
    public function getAeroportoDeChegada(): SiglaAeroporto
    {
        return log::getInstance()->logRead($this->aeroporto_de_chegada);
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return log::getInstance()->logRead($this->data);
    }

    public function build(): Viagem
    {
        $this->tripulacao->trancar();
        return log::getInstance()->logCall(new Viagem(
            $this->registro,
            $this->codigo_do_voo,
            $this->aeroporto_de_saida,
            $this->aeroporto_de_chegada,
            $this->hora_de_partida,
            $this->hora_de_chegada,
            $this->aeronave,
            $this->tripulacao,
            $this->tarifa,
            $this->tarifa_franquia,
            $this->assentos,
        ));
    }
}