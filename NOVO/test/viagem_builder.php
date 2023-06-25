<?php
require_once "../classes/viagem_builder.php";
require_once "suite.php";
require_once "mixins/aeroportos_mixin.php";
require_once "mixins/tripulante_mixin.php";
class ViagemBuilderTestCase extends TestCase
{
    use AeroportosMixin;
    use TripulanteMixin;

    protected function getName(): string
    {
        return 'ViagemBuilder';
    }

    public function run()
    {
        $this->initAeroportos();
        $this->limparAeroportos();
        $this->registrarAeroportos();
        $this->initTripulante();
        # addTarifaFranquia
        $vb = new ViagemBuilder();
        $tarifaFranquia = 10.0;
        $this->startSection("addTarifaFranquia");
        $this->checkEq($vb, $vb->addTarifaFranquia($tarifaFranquia));
        $this->checkEq($this->getNonPublicProperty($vb, "tarifa_franquia"), $tarifaFranquia);
        # adicionarGeradorDeRegistro
        $geradorRegistroViagem = new GeradorDeRegistroDeViagem();
        $this->startSection("adicionarGeradorDeRegistro");
        $this->checkEq($vb, $vb->adicionarGeradorDeRegistro($geradorRegistroViagem));
        $this->checkEq($this->getNonPublicProperty($vb, "gerador_de_registro"), $geradorRegistroViagem);
        # gerarRegistro
        $registro = new RegistroDeViagem("AA", 0);
        $this->startSection("gerarRegistro");
        $this->checkEq($vb, $vb->gerarRegistro());
        $this->checkEq($vb->getRegistro(), $registro);
        # addData
        $data = Data::hoje()->add(Duracao::umDia());
        $this->startSection("addData");
        $this->checkEq($vb, $vb->addData($data));
        $this->checkEq($vb->getData(), $data);
        # addVoo
        $codigoVoo = new CodigoVoo(new SiglaCompanhiaAerea("LT"), 1);
        $aeroportoSaida = $this->siglaConfins;
        $aeroportoChegada = $this->siglaAfonsoPena;
        $horaPartida = new Tempo(9, 0, 0);
        $duracao = new Duracao(0, 60 * 60 * 2);
        $todosDiasDaSemana = DiaDaSemana::cases();
        $registroAeronave = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "AAA");
        $capacidadePassageiros = 2;
        $capacidadeCarga = 30.0;
        $tarifa = 1000.0;
        $pontuacaoMilhagem = 1000;
        $voo = new Voo($codigoVoo, $aeroportoSaida, $aeroportoChegada, $horaPartida, $duracao, $todosDiasDaSemana, $registroAeronave, $capacidadePassageiros, $capacidadeCarga, $tarifa, $pontuacaoMilhagem);
        $this->startSection("addVoo");
        $this->checkEq($vb, $vb->addVoo($voo));
        $this->checkEq($this->getNonPublicProperty($vb, "carga"), $capacidadeCarga);
        $this->checkEq($this->getNonPublicProperty($vb, "passageiros"), $capacidadePassageiros);
        $this->checkEq($vb->getCodigoDoVoo(), $codigoVoo);
        $this->checkEq($this->getNonPublicProperty($vb, "tarifa"), $tarifa);
        $this->checkEq($vb->getAeroportoDeSaida(), $aeroportoSaida);
        $this->checkEq($vb->getAeroportoDeChegada(), $aeroportoChegada);
        $this->checkEq($vb->getHoraDePartidaEstimada(), $horaPartida->comData($data));
        /**
         * @var HashMap<CodigoDoAssento, Assento> $assentos
         */
        $assentos = $this->getNonPublicProperty($vb, "assentos");
        $codigoAssento = new CodigoDoAssento(Classe::STANDARD, "A", 1);
        $codigoAssento1 = new CodigoDoAssento(Classe::STANDARD, "B", 1);
        $this->checkEq($assentos->get($codigoAssento)->getCodigo(), $codigoAssento);
        $this->checkTrue($assentos->get($codigoAssento)->vazio());
        $this->checkEq($assentos->get($codigoAssento1)->getCodigo(), $codigoAssento1);
        $this->checkTrue($assentos->get($codigoAssento1)->vazio());
        $this->checkEq($this->getNonPublicProperty($vb, "pontuacaoMilhagem"), $pontuacaoMilhagem);
        # addAeronave
        try {
            $aeronaveInvalida = new Aeronave($codigoVoo->getSiglaDaCompanhia(), "Boeing", "747", $capacidadePassageiros - 1, $capacidadeCarga, $registroAeronave);
            $vb->addAeronave($aeronaveInvalida);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $aeronaveInvalida = new Aeronave($codigoVoo->getSiglaDaCompanhia(), "Boeing", "747", $capacidadePassageiros, $capacidadeCarga - 1, $registroAeronave);
            $vb->addAeronave($aeronaveInvalida);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $aeronaveInvalida = new Aeronave($codigoVoo->getSiglaDaCompanhia(), "Boeing", "747", $capacidadePassageiros - 1, $capacidadeCarga - 1, $registroAeronave);
            $vb->addAeronave($aeronaveInvalida);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        $aeronave = new Aeronave($codigoVoo->getSiglaDaCompanhia(), "Boeing", "747", $capacidadePassageiros, $capacidadeCarga, $registroAeronave);
        $this->startSection("addAeronave");
        $this->checkEq($vb, $vb->addAeronave($aeronave));
        $this->checkEq($this->getNonPublicProperty($vb, "aeronave"), $registroAeronave);
        # temAssentosLiberados & assentoEstaLiberado & codigoAssentoLiberado
        $this->startSection("temAssentosLiberados & assentoEstaLiberado & codigoAssentoLiberado");
        $this->checkTrue($vb->temAssentosLiberados());
        $this->checkTrue($vb->assentoEstaLiberado($codigoAssento));
        $this->checkTrue($vb->assentoEstaLiberado($codigoAssento1));
        $this->checkEq($vb->codigoAssentoLiberado(), $codigoAssento);
        $registroPassagem = new RegistroDePassagem(0);
        $registroPassagem1 = new RegistroDePassagem(1);
        $nenhumaFranquia = new FranquiasDeBagagem([]);
        try {
            $vb->reservarAssento(false, $registroPassagem, $nenhumaFranquia, $codigoAssento);
            $this->checkFalse($vb->assentoEstaLiberado($codigoAssento));
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }
        $this->checkTrue($vb->temAssentosLiberados());
        $this->checkEq($vb->codigoAssentoLiberado(), $codigoAssento1);
        try {
            $vb->reservarAssento(false, $registroPassagem1, $nenhumaFranquia, $codigoAssento1);
            $this->checkFalse($vb->assentoEstaLiberado($codigoAssento1));
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }
        $this->checkFalse($vb->temAssentosLiberados());
        try {
            $codigoAssentoInvalido = new CodigoDoAssento(Classe::STANDARD, 'C', 1);
            $vb->assentoEstaLiberado($codigoAssentoInvalido);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $vb->codigoAssentoLiberado();
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        # temCargaDisponivelParaFranquias
        $franquiasMetade = new FranquiasDeBagagem([new FranquiaDeBagagem($capacidadeCarga / 2)]);
        $franquiasMaxima = new FranquiasDeBagagem([new FranquiaDeBagagem($capacidadeCarga / 2), new FranquiaDeBagagem($capacidadeCarga / 2)]);
        $franquiasAcima = new FranquiasDeBagagem([new FranquiaDeBagagem($capacidadeCarga / 2), new FranquiaDeBagagem($capacidadeCarga / 2), new FranquiaDeBagagem($capacidadeCarga / 2)]);
        $this->startSection("temCargaDisponivelParaFranquias");
        $vb->liberarAssento($registroPassagem, $codigoAssento);
        $vb->liberarAssento($registroPassagem1, $codigoAssento1);
        $this->checkTrue($vb->temCargaDisponivelParaFranquias($nenhumaFranquia));
        $this->checkTrue($vb->temCargaDisponivelParaFranquias($franquiasMetade));
        $this->checkTrue($vb->temCargaDisponivelParaFranquias($franquiasMaxima));
        $this->checkFalse($vb->temCargaDisponivelParaFranquias($franquiasAcima));
        $vb->reservarAssento(false, $registroPassagem, $franquiasMetade, $codigoAssento);
        $this->checkTrue($vb->temCargaDisponivelParaFranquias($nenhumaFranquia));
        $this->checkTrue($vb->temCargaDisponivelParaFranquias($franquiasMetade));
        $this->checkFalse($vb->temCargaDisponivelParaFranquias($franquiasMaxima));
        $this->checkFalse($vb->temCargaDisponivelParaFranquias($franquiasAcima));
        $vb->reservarAssento(false, $registroPassagem1, $franquiasMetade, $codigoAssento1);
        $this->checkTrue($vb->temCargaDisponivelParaFranquias($nenhumaFranquia));
        $this->checkFalse($vb->temCargaDisponivelParaFranquias($franquiasMetade));
        $this->checkFalse($vb->temCargaDisponivelParaFranquias($franquiasMaxima));
        $this->checkFalse($vb->temCargaDisponivelParaFranquias($franquiasAcima));
        # reservarAssento
        $this->startSection("reservarAssento");
        $vb->liberarAssento($registroPassagem, $codigoAssento);
        $vb->liberarAssento($registroPassagem1, $codigoAssento1);
        $vb->reservarAssento(false, $registroPassagem, $franquiasMaxima, $codigoAssento);
        try {
            $vb->reservarAssento(false, $registroPassagem1, $franquiasMetade, $codigoAssento1);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        try {
            $codigoAssentoInvalido = new CodigoDoAssento(Classe::STANDARD, 'C', 1);
            $vb->reservarAssento(false, $registroPassagem1, $nenhumaFranquia, $codigoAssentoInvalido);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $vb->reservarAssento(false, $registroPassagem1, $nenhumaFranquia, $codigoAssento);
            $this->checkNotReached();
        } catch (PreenchimentoDeAssentoException $e) {
            $this->checkReached();
        }
        $vb->liberarAssento($registroPassagem, $codigoAssento);
        $tarifaNaoVip = $vb->reservarAssento(false, $registroPassagem, $franquiasMaxima, $codigoAssento);
        $this->checkApproximate($tarifaNaoVip, $tarifa + 2 * $tarifaFranquia);
        $vb->liberarAssento($registroPassagem, $codigoAssento);
        $tarifaVip = $vb->reservarAssento(true, $registroPassagem, $franquiasMaxima, $codigoAssento);
        $this->checkApproximate($tarifaVip, $tarifa + $tarifaFranquia / 2);
        # addTripulante
        $companhia = new SiglaCompanhiaAerea("AZ");
        $piloto = $this->buildTripulante(Cargo::PILOTO, $companhia);
        $copiloto = $this->buildTripulante(Cargo::COPILOTO, $companhia);
        $comissario = $this->buildTripulante(Cargo::COMISSARIO, $companhia);
        $comissario1 = $this->buildTripulante(Cargo::COMISSARIO, $companhia);
        $comissario2 = $this->buildTripulante(Cargo::COMISSARIO, $companhia);
        $this->startSection("addTripulante");
        $this->checkEq($vb, $vb->addTripulante($piloto, $this->coordenadaTripulante($aeroportoSaida)));
        try {
            $vb->addTripulante($piloto, $this->coordenadaTripulante($aeroportoSaida));
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        $this->checkEq($vb, $vb->addTripulante($copiloto, $this->coordenadaTripulante($aeroportoSaida)));
        try {
            $vb->addTripulante($copiloto, $this->coordenadaTripulante($aeroportoSaida));
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        $this->checkEq($vb, $vb->addTripulante($comissario, $this->coordenadaTripulante($aeroportoSaida)));
        try {
            $vb->addTripulante($comissario, $this->coordenadaTripulante($aeroportoSaida));
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        $this->checkEq($vb, $vb->addTripulante($comissario1, $this->coordenadaTripulante($aeroportoSaida)));
        try {
            $vb->addTripulante($comissario1, $this->coordenadaTripulante($aeroportoSaida));
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        $this->checkEq($vb, $vb->addTripulante($comissario2, $this->coordenadaTripulante($aeroportoSaida)));
        try {
            $vb->addTripulante($comissario2, $this->coordenadaTripulante($aeroportoSaida));
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        # addHoraDePartidaEHoraDeChegada
        $horaDePartida = $voo->getHoraDePartida()->comData($data);
        $horaDeChegada = $horaDePartida->add($voo->getDuracaoEstimada());
        $this->startSection("addHoraDePartidaEHoraDeChegada");
        $this->checkEq($vb, $vb->addHoraDePartidaEHoraDeChegada($horaDePartida, $horaDeChegada));
        $this->checkEq($this->getNonPublicProperty($vb, "hora_de_partida"), $horaDePartida);
        $this->checkEq($this->getNonPublicProperty($vb, "hora_de_chegada"), $horaDeChegada);
        # build
        $this->startSection("build");
        $viagem = $vb->build();
        $this->checkEq($viagem->getRegistro(), $registro);
        $this->checkEq($viagem->getCodigoDoVoo(), $codigoVoo);
        $this->checkEq($viagem->getAeroportoDeSaida(), $aeroportoSaida);
        $this->checkEq($viagem->getAeroportoDeChegada(), $aeroportoChegada);
        $this->checkEq($viagem->getAeronave(), $aeronave->getRegistro());
        $this->checkEq($viagem->getTripulacao(), $this->getNonPublicProperty($vb, 'tripulacao'));
        $this->checkEq($viagem->getTarifa(), $tarifa);
        $this->checkEq($viagem->getTarifaFranquia(), $tarifaFranquia);
        $this->checkEq($viagem->getAssentos(), $this->getNonPublicProperty($vb, 'assentos'));
    }
}