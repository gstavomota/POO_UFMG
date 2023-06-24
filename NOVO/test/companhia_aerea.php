<?php
require_once "../classes/companhia_aerea.php";
require_once "suite.php";
require_once "mixins/tripulante_mixin.php";
require_once "mixins/voos_mixin.php";

class CompanhiaAereaTestCase extends TestCase {
    use TripulanteMixin;
    use VoosMixin;
    protected function getName(): string
    {
        return "CompanhiaAerea";
    }
    public function run()
    {
        // Inicializar mixins
        $this->initTripulante();
        $this->initVoos();
        // Limpar companhias aereas
        CompanhiaAerea::deleteAllRecords();
        $nome = "Azul";
        $codigo = "001";
        $razao_social = "Azul etc corporate speak";
        $sigla = new SiglaCompanhiaAerea("AZ");
        $tarifa_franquia = 10.0;
        # Construtor
        $this->startSection("Constructor");
        /**
         * @var CompanhiaAerea $ca
         */
        $ca = null;
        try {
            $ca = new CompanhiaAerea(
                $nome,
                $codigo,
                $razao_social,
                $sigla,
                $tarifa_franquia,
            );
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }
        # Getters
        $this->startSection("Getters");
        $this->checkEq($ca->getNome(), $nome);
        $this->checkEq($ca->getCodigo(), $codigo);
        $this->checkEq($ca->getRazaoSocial(), $razao_social);
        $this->checkEq($ca->getSigla(), $sigla);
        $this->checkEq($ca->getTarifaFranquia(), $tarifa_franquia);
        # Limpar aeroportos
        $this->limparAeroportos();
        # Registrar voo sem aeroportos e aeronave
        $hora9am = new Tempo(9, 0, 0);
        $hora1pm = new Tempo(13, 0, 0);
        $amanha = Data::hoje()->add(Duracao::umDia());
        $duracao2h = new Duracao(0, 60*60*2);
        $todosDiasDaSemana = DiaDaSemana::cases();
        $registroAeronaveAAA = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "AAA");
        $this->startSection("registrarVoo sem aeroportos e aeronave");
        try {
            $ca->registrarVoo(
                1,
                $this->aeroportoConfins->getSigla(),
                $this->aeroportoGuarulhos->getSigla(),
                $hora9am,
                $duracao2h,
                $todosDiasDaSemana,
                $registroAeronaveAAA,
                1000.0,
                1000,
            );
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
            $this->checkEq($e->getMessage(), "Aeroporto de saida não registrado");
        }
        # registrarVoo com aeroporto de saida sem aeronave
        $this->registrarAeroporto($this->aeroportoConfins->getSigla());
        $this->startSection("registrarVoo com aeroporto de saida sem aeronave");
        try {
            $ca->registrarVoo(
                1,
                $this->aeroportoConfins->getSigla(),
                $this->aeroportoGuarulhos->getSigla(),
                $hora9am,
                $duracao2h,
                $todosDiasDaSemana,
                $registroAeronaveAAA,
                1000.0,
                1000,
            );
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
            $this->checkEq($e->getMessage(), "Aeroporto de chegada não registrado");
        }
        # registrarVoo com aeroportos sem aeronave
        $this->registrarAeroporto($this->aeroportoGuarulhos->getSigla());
        $this->checkNull($ca->encontrarAeronave($registroAeronaveAAA));
        $this->startSection("registrarVoo com aeroportos sem aeronave");
        try {
            $ca->registrarVoo(
                1,
                $this->aeroportoConfins->getSigla(),
                $this->aeroportoGuarulhos->getSigla(),
                $hora9am,
                $duracao2h,
                $todosDiasDaSemana,
                $registroAeronaveAAA,
                1000.0,
                1000,
            );
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
            $this->checkEq($e->getMessage(), "Aeronave não registrada");
        }
        # Registrar outros aeroportos necessarios
        $this->registrarAeroporto($this->aeroportoCongonhas->getSigla());
        $this->registrarAeroporto($this->aeroportoGaleao->getSigla());
        $this->registrarAeroporto($this->aeroportoAfonsoPena->getSigla());
        # registrarAeronave
        $this->startSection("registrarAeronave");
        /**
         * @var Aeronave $aeronaveAAA
         */
        $aeronaveAAA = $this->aeronaveEmbraer175($ca->getSigla());
        try {
            $this->registrarAeronaveNaCompanhia($ca, $aeronaveAAA->getRegistro());
            $this->checkReached();
            /**
             * @var HashMap<RegistroDeAeronave, Aeronave> $aeronaves
             */
            $aeronaves_ca = $this->getNonPublicProperty($ca, "aeronaves");
            $this->checkEq($aeronaves_ca->get($registroAeronaveAAA), $aeronaveAAA);
            $this->checkEq($ca->encontrarAeronave($registroAeronaveAAA), $aeronaveAAA);
        } catch (Exception $e) {
            $this->checkNotReached();
        }
        try {
            $ca->registrarAeronave(
                $aeronaveAAA->getFabricante(),
                $aeronaveAAA->getModelo(),
                $aeronaveAAA->getCapacidadePassageiros(),
                $aeronaveAAA->getCapacidadeCarga(),
                $registroAeronaveAAA
            );
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
            $this->checkEq($e->getMessage(), "Aeronave já presente");
        }
        # registrarVoo com aeroportos e aeronave e dias da semana
        $vooConfinsGuarulhos = $this->vooConfinsGuarulhos($ca->getSigla(),$hora9am, $registroAeronaveAAA, $todosDiasDaSemana, 1000);
        $this->startSection("registrarVoo com aeroportos e aeronave e dias da semana");
        $this->checkNull($ca->encontrarVoo(new CodigoVoo($ca->getSigla(), 1)));
        try {
            $this->registrarVooNaCompanhia($ca, $vooConfinsGuarulhos);
            $this->checkReached();
            /**
             * @var HashMap<CodigoVoo, Voo> $voos_planejados_ca
             */
            $voos_planejados_ca = $this->getNonPublicProperty($ca, "voos_planejados");
            $this->checkEq($voos_planejados_ca->get($vooConfinsGuarulhos->getCodigo()), $vooConfinsGuarulhos);
            $this->checkEq($ca->encontrarVoo($vooConfinsGuarulhos->getCodigo()), $vooConfinsGuarulhos);
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            $this->registrarVooNaCompanhia($ca, $vooConfinsGuarulhos);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
            $this->checkEq($e->getMessage(), "Voo já presente");
        }
        # registrarVoo com data no voos_em_venda e dia da semana disponivel
        // limpar voos planejados
        $this->setNonPublicProperty($ca, "voos_planejados", new HashMap());
        /**
         * @var HashMap<Data, HashMap<RegistroDeViagem, ViagemBuilder>> $voos_em_venda_ca
         */
        $voos_em_venda_ca = $this->getNonPublicProperty($ca, "voos_em_venda");
        // adicionar hoje nos voos em venda
        $voos_em_venda_ca->put(Data::hoje(), new HashMap());
        $this->startSection("registrarVoo com data no voos_em_venda e dia da semana disponivel");
        try {
            $this->registrarVooNaCompanhia($ca, $vooConfinsGuarulhos);
            $this->checkReached();
            $voos_em_venda_hoje = $voos_em_venda_ca->get(Data::hoje());
            $this->checkEq($voos_em_venda_hoje->size(), 1);
            /**
             * @var ViagemBuilder $vb
             */
            $vb = $voos_em_venda_hoje->entries()[0]->value;
            $this->checkEq($vb->getCodigoDoVoo(), $vooConfinsGuarulhos->getCodigo());
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # registrarVoo com data no voos_em_venda e dia da semana indisponivel
        // limpar voos planejados
        $this->setNonPublicProperty($ca, "voos_planejados", new HashMap());
        /**
         * @var HashMap<Data, HashMap<RegistroDeViagem, ViagemBuilder>> $voos_em_venda_ca
         */
        $voos_em_venda_ca = $this->getNonPublicProperty($ca, "voos_em_venda");
        // limpar hoje dos voos em vends
        $voos_em_venda_ca->put(Data::hoje(), new HashMap());
        $vooConfinsAfonsoPenaAmanha = $this->vooConfinsAfonsoPena($ca->getSigla(),$hora9am, $registroAeronaveAAA, [$amanha->getDiaDaSemana()], 1000);
        $this->startSection("registrarVoo com data no voos_em_venda e dia da semana indisponivel");
        try {
            $this->registrarVooNaCompanhia($ca, $vooConfinsAfonsoPenaAmanha);
            $this->checkReached();
            $voos_em_venda_hoje = $voos_em_venda_ca->get(Data::hoje());
            $this->checkEq($voos_em_venda_hoje->size(), 0);
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # getRecordsBySigla
        $this->startSection("getRecordsBySigla");
        $this->checkTrue(empty(CompanhiaAerea::getRecordsBySigla($ca->getSigla())));
        $ca->save();
        $this->checkTrue(count(CompanhiaAerea::getRecordsBySigla($ca->getSigla())), 1);
        # registrarTripulante
        $this->startSection("registrarTripulante");
        $this->checkNull($ca->encontrarTripulante(new RegistroDeTripulante(0)));
        $piloto = $this->registrarTripulante($ca, Cargo::PILOTO);
        $this->checkNotNull($ca->encontrarTripulante($piloto->getRegistro()));
        # Registrar outros tripulantes
        $copiloto = $this->registrarTripulante($ca, Cargo::COPILOTO);
        $comissario = $this->registrarTripulante($ca, Cargo::COMISSARIO);
        $comissario1 = $this->registrarTripulante($ca, Cargo::COMISSARIO);
        $comissario2 = $this->registrarTripulante($ca, Cargo::COMISSARIO);
        # adicionarPassageiro
        $this->startSection("adicionarPassageiro");
        $passageiro = $this->passageiro();
        $this->checkNull($ca->encontrarPassageiro($passageiro->getDocumento()));
        $this->adicionarPassageiroNaCompanhiaAerea($ca, $passageiro);
        $this->checkEq($ca->encontrarPassageiro($passageiro->getDocumento()), $passageiro);
        try {
            $this->adicionarPassageiroNaCompanhiaAerea($ca, $passageiro);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        # Registrar voo confins guarulhos segunda a sexta
        // Resetar voos em venda e voos planejados
        $this->setNonPublicProperty($ca, "voos_em_venda", new HashMap());
        $this->setNonPublicProperty($ca, "voos_planejados", new HashMap());
        // Registrar voo confins guarulhos segunda a sexta
        $voos_em_venda_ca = $this->getNonPublicProperty($ca, "voos_em_venda");
        $segundaASexta = [DiaDaSemana::SEGUNDA, DiaDaSemana::TERCA, DiaDaSemana::QUARTA, DiaDaSemana::QUINTA, DiaDaSemana::SEXTA];
        $vooConfinsGuarulhosSegundaASexta = $ca->registrarVoo(
            1,
            $this->siglaConfins,
            $this->siglaGuarulhos,
            $hora9am,
            $duracao2h,
            $segundaASexta,
            $registroAeronaveAAA,
            1000.0,
            1000,
        );
        # adicionarViagensEmVenda
        /**
         * @var HashMap<Data, HashMap<RegistroDeViagem, ViagemBuilder>> $voos_em_venda_ca
         */
        $voos_em_venda_ca = $this->getNonPublicProperty($ca, "voos_em_venda");
        $this->startSection('adicionarViagensEmVenda');
        $ca->adicionarViagensEmVenda();
        $hoje = Data::hoje();
        for ($i = 0; $i < 30; $i++) {
            $dia = $hoje->add(new Duracao($i, 0));
            /**
             * @var HashMap<RegistroDeViagem, ViagemBuilder> $registro_viagem_viagem_builder
             */
            $registro_viagem_viagem_builder = $voos_em_venda_ca->get($dia);
            if (in_array($dia->getDiaDaSemana(), $segundaASexta)) {
                $this->checkEq($registro_viagem_viagem_builder->size(), 1);
                $this->checkEq($registro_viagem_viagem_builder->values()[0]->getCodigoDoVoo(), $vooConfinsGuarulhosSegundaASexta->getCodigo());
            } else {
                $this->checkEq($registro_viagem_viagem_builder->size(), 0);
            }
        }
        # Registrar voo confins guarulhos e guarulhos afonso pena todos dias da semana
        // Resetar voos em venda e voos planejados
        $this->setNonPublicProperty($ca, "voos_em_venda", new HashMap());
        $this->setNonPublicProperty($ca, "voos_planejados", new HashMap());
        // Registrar voo confins guarulhos todos dias da semana
        $vooConfinsGuarulhos = $ca->registrarVoo(
            1,
            $this->siglaConfins,
            $this->siglaGuarulhos,
            $hora9am,
            $duracao2h,
            $todosDiasDaSemana,
            $registroAeronaveAAA,
            1000.0,
            1000,
        );
        // Registrar voo guarulhos afonso pena todos dias da semana
        $hora1pm = new Tempo(13, 0, 0);
        $vooGuarulhosAfonsoPena = $this->vooGuarulhosAfonsoPena(
            $ca->getSigla(),
            $hora1pm,
            $registroAeronaveAAA,
            $todosDiasDaSemana,
            1000
        );
        $this->registrarVooNaCompanhia($ca, $vooGuarulhosAfonsoPena);
        # comprarPassagem sem conexao sem codigo de assento
        $nenhumaFranquia = new FranquiasDeBagagem([]);
        $registroPassagemSemConexao = $ca->comprarPassagem(
            $passageiro->getDocumento(),
            $amanha,
            $this->siglaConfins,
            $this->siglaGuarulhos,
            $nenhumaFranquia
        );
        $this->assertNotNull($registroPassagemSemConexao);
        $passagemSemConexao = $ca->encontrarPassagem($registroPassagemSemConexao);
        $this->assertNotNull($passagemSemConexao);
        $this->checkEq(count($passagemSemConexao->getAssentos()),1);
        $registroViagem_codigoAssento = $passagemSemConexao->getAssentos()[0];
        $registroViagem = $registroViagem_codigoAssento->key;
        $codigoAssento = $registroViagem_codigoAssento->value;
        /**
         * @var ViagemBuilder $viagemBuilder
         */
        $viagemBuilder = $this->runNonPublicMethod($ca, "findRequiredViagemBuilder", $registroViagem);
        /**
         * @var HashMap<CodigoDoAssento, Assento> $viagemBuilderAssentos
         */
        $viagemBuilderAssentos = $this->getNonPublicProperty($viagemBuilder, "assentos");
        $assento = $viagemBuilderAssentos->get($codigoAssento);
        $this->checkTrue($assento->preenchido());
        $this->checkEq($assento->getPassagem(), $passagemSemConexao->getRegistro());
        $this->checkEq($assento->getFranquias(), $nenhumaFranquia);
        $viagemBuilderByCodigoVooEData = $this->runNonPublicMethod($ca, "findRequiredViagemBuilderByDataECodigoVoo", $passagemSemConexao->getData(), $vooConfinsGuarulhos->getCodigo());
        $this->checkEq($viagemBuilder, $viagemBuilderByCodigoVooEData);
        # encontrarViagem exception
        $this->startSection("encontrarViagem exception");
        try {
            $ca->encontrarViagem($registroViagem);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkEq($e->getMessage(), "A viagem ainda não foi executada");
            $this->checkReached();
        }
        # removeViagemBuilder
        $this->startSection("removeViagemBuilder");
        $removedViagemBuilder = $this->runNonPublicMethod($ca, "removeViagemBuilder", $viagemBuilder->getRegistro());
        $this->checkEq($viagemBuilder, $removedViagemBuilder);
        # encontrarViagem not present
        $this->startSection("encontrarViagem not present");
        try {
            $notFoundRegistroViagem = $ca->encontrarViagem($registroViagem);
            $this->checkReached();
            $this->checkNull($notFoundRegistroViagem);
        } catch (Exception $e) {
            $this->checkNotReached();
        }
        # Resetar companhia aerea
        $this->setNonPublicProperty($passageiro, "passagens", []);
        $this->setNonPublicProperty($ca, "voos_em_venda", new HashMap());
        $this->setNonPublicProperty($ca, "voos_planejados", new HashMap());
        $this->setNonPublicProperty($ca, "passagens", new HashMap());
        # Setar voos
        $vooConfinsGuarulhos = $this->vooConfinsGuarulhos(
            $sigla,
            $hora9am,
            $registroAeronaveAAA,
            $todosDiasDaSemana,
            1000.0,
        );
        $this->registrarVooNaCompanhia($ca, $vooConfinsGuarulhos);
        $vooGuarulhosAfonsoPena = $this->vooGuarulhosAfonsoPena(
            $sigla,
            $hora1pm,
            $registroAeronaveAAA,
            $todosDiasDaSemana,
            1000.0,
        );
        $this->registrarVooNaCompanhia($ca, $vooGuarulhosAfonsoPena);
        # comprar passagem com conexao sem codigo do assento
        $this->startSection("comprar passagem com conexao sem codigo do assento");
        $registroPassagemComConexao = $ca->comprarPassagem(
            $passageiro->getDocumento(),
            $amanha,
            $this->siglaConfins,
            $this->siglaAfonsoPena,
            $nenhumaFranquia
        );
        $this->assertNotNull($registroPassagemComConexao);
        $passagemComConexao = $ca->encontrarPassagem($registroPassagemComConexao);
        /**
         * @var HashMapEntry<RegistroDeViagem, CodigoDoAssento>[] $registroViagems_codigoAssentos
         */
        $registroViagems_codigoAssentos = $passagemComConexao->getAssentos();
        $this->checkEq(count($registroViagems_codigoAssentos), 2);
        foreach ($registroViagems_codigoAssentos as $registroViagem_codigoAssento) {
            $registroViagem = $registroViagem_codigoAssento->key;
            $codigoAssento = $registroViagem_codigoAssento->value;
            /**
             * @var ViagemBuilder $viagemBuilder
             */
            $viagemBuilder = $this->runNonPublicMethod($ca, "findRequiredViagemBuilder", $registroViagem);
            /**
             * @var HashMap<CodigoDoAssento, Assento> $assentos
             */
            $assentos = $this->getNonPublicProperty($viagemBuilder, "assentos");
            $this->checkNotNull($assentos);
            $assento = $assentos->get($codigoAssento);
            $this->checkTrue($assento->preenchido());
            $this->checkEq($assento->getPassagem(), $registroPassagemComConexao);
            $this->checkEq($assento->getFranquias(), $nenhumaFranquia);
        }
        # comprarPassagem sem conexao com codigo de assento
        $this->startSection("comprarPassagem sem conexao com codigo de assento");
        $registroPassagemSemConexaoComCodigoDeAssento = $ca->comprarPassagem(
            $passageiro->getDocumento(),
            $amanha,
            $this->siglaConfins,
            $this->siglaGuarulhos,
            $nenhumaFranquia,
            new CodigoDoAssento(Classe::STANDARD, "B", 1)
        );
        $this->assertNotNull($registroPassagemSemConexaoComCodigoDeAssento);
        $passagemSemConexaoComCodigoDeAssento = $ca->encontrarPassagem($registroPassagemSemConexaoComCodigoDeAssento);
        $this->checkEq(count($passagemSemConexaoComCodigoDeAssento->getAssentos()), 1);
        /**
         * @var HashMapEntry<RegistroDeViagem, CodigoDoAssento> $registroViagems_codigoAssentos
         */
        $registroViagem_codigoAssento = $passagemSemConexaoComCodigoDeAssento->getAssentos()[0];
        $registroViagem = $registroViagem_codigoAssento->key;
        $codigoAssento = $registroViagem_codigoAssento->value;
        $this->checkEq($codigoAssento, new CodigoDoAssento(Classe::STANDARD, "B", 1));
        /**
         * @var ViagemBuilder $viagemBuilder
         */
        $viagemBuilder = $this->runNonPublicMethod($ca, "findRequiredViagemBuilder", $registroViagem);
        /**
         * @var HashMap<CodigoDoAssento, Assento> $assentos
         */
        $assentos = $this->getNonPublicProperty($viagemBuilder, "assentos");
        $this->checkNotNull($assentos);
        $assento = $assentos->get($codigoAssento);
        $this->checkTrue($assento->preenchido());
        $this->checkEq($assento->getPassagem(), $registroPassagemSemConexaoComCodigoDeAssento);
        $this->checkEq($assento->getFranquias(), $nenhumaFranquia);
        # removeViagemBuilder
        /**
         * @var HashMap<RegistroDeViagem, ViagemBuilder> $registroViagem_viagemBuilder
         */
        $registroViagem_viagemBuilder = $this->getNonPublicProperty($ca, "voos_em_venda")->get(Data::hoje());
        $primeiroRegistroDeViagem = $registroViagem_viagemBuilder->keys()[0];
        $primeiroViagemBuilder = $registroViagem_viagemBuilder->get($primeiroRegistroDeViagem);
        $this->startSection("removeViagemBuilder");
        $this->checkEq($this->runNonPublicMethod($ca, "removeViagemBuilder", $primeiroRegistroDeViagem), $primeiroViagemBuilder);
        try {
            $this->runNonPublicMethod($ca, "removeViagemBuilder", $primeiroRegistroDeViagem);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        # comprarPassagem sem passageiro
        $this->startSection("comprarPassagem sem passageiro");
        try {
            $documentoPassageiroNaoExistente = new DocumentoPessoa(new Passaporte("A00000000"));
            $ca->comprarPassagem(
                $documentoPassageiroNaoExistente,
                $amanha,
                $this->siglaConfins,
                $this->siglaCongonhas,
                $nenhumaFranquia
            );
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
            $this->checkEq($e->getMessage(), "Cliente nao cadastrado");
        }
        # comprarPassagem sem voo
        $this->startSection("comprarPassagem sem voo");
        $passagemNula = $ca->comprarPassagem(
            $passageiro->getDocumento(),
            $amanha,
            $this->siglaGaleao,
            $this->siglaCongonhas,
            $nenhumaFranquia
        );
        $this->checkNull($passagemNula);
    }
}