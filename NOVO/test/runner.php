<?php
require_once "assento.php";
require_once "calculo_tarifa_strategy.php";
require_once "endereco.php";
require_once "enum_to_array.php";
require_once "franquia_de_bagagem.php";
require_once "HashMap.php";
require_once "identificadores.php";
require_once "passageiro.php";
require_once "passageiroVip.php";
require_once "passagem.php";
require_once "programaDeMilhagem.php";
require_once "roteiro_de_testes.php";
require_once "suite.php";
require_once "suite_test.php";
require_once "temporal.php";
require_once "tripulacao.php";
require_once "viagem_builder.php";
require_once "voo.php";

(new TestRunner())
    // Inicio assento.php
    ->addCase(new AssentoTestCase())
    // Fim assento.php
    // Inicio calculo_tarifa_strategy.php
    ->addCase(new PassageiroComumCalculoTarifaStrategyTestCase())
    ->addCase(new PassageiroVipCalculoTarifaStrategyTestCase())
    // Fim calculo_tarifa_strategy.php
    // Inicio endereco.php
    ->addCase(new EnderecoTestCase())
    // Fim endereco.php
    // Inicio enum_to_array.php
    ->addCase(new EnumToArrayTestCase())
    // Fim enum_to_array.php
    // Inicio franquia_de_bagagem.php
    ->addCase(new FranquiaDeBagagemTestCase())
    ->addCase(new FranquiasDeBagagemTestCase())
    // Fim franquia_de_bagagem.php
    // Inicio HashMap.php
    ->addCase(new HashMapTestCase())
    // Fim HashMap.php
    // Inicio identificadores.php
    ->addCase(new GeradorDeRegistroNumericoTestCase())
    ->addCase(new SiglaCompanhiaAereaTestCase())
    ->addCase(new CodigoVooTestCase())
    ->addCase(new RegistroDeAeronaveTestCase())
    ->addCase(new RegistroDePassagemTestCase())
    ->addCase(new RegistroDeViagemTestCase())
    ->addCase(new SiglaAeroportoTestCase())
    ->addCase(new RegistroDeTripulanteTestCase())
    ->addCase(new GeradorDeRegistroDeTripulanteTestCase())
    ->addCase(new RGTestCase())
    ->addCase(new PassaporteTestCase())
    ->addCase(new DocumentoPassageiroTestCase())
    ->addCase(new GeradorDeRegistroDeViagemTestCase())
    ->addCase(new GeradorDeRegistroDePassagemTestCase())
    ->addCase(new ClasseTestCase())
    ->addCase(new CodigoDoAssentoTestCase())
    ->addCase(new GeradorDeCodigoDoAssentoTestCase())
    ->addCase(new EmailTestCase())
    ->addCase(new CPFTestCase())
    ->addCase(new CEPTestCase())
    // Fim identificadores.php
    // Inicio passageiro.php
    ->addCase(new PassageiroTestCase())
    // Fim passageiro.php
    // Inicio passageiroVip.php
    ->addCase(new PassageiroVipTestCase())
    // Fim passageiroVip.php
    // Inicio passagem.php
    ->addCase(new PassagemCanceladaTestCase())
    ->addCase(new PassagemCheckInNaoAbertoTestCase())
    ->addCase(new PassagemAguardandoCheckInTestCase())
    ->addCase(new PassagemNaoApareceuTestCase())
    ->addCase(new PassagemCheckedInTestCase())
    ->addCase(new PassagemEmbarcadoTestCase())
    ->addCase(new PassagemConcluidaComSucessoTestCase())
    ->addCase(new PassagemTestCase())
    // Fim passagem.php
    // Inicio HashMap.php
    ->addCase(new HashMapTestCase())
    // Fim HashMap.php
    // Inicio programaDeMilhagem.php
    ->addCase(new ProgramaDeMilhagemTestCase())
    // Fim programaDeMilhagem.php
    // Inicio roteiro_de_testes.php
    ->addCase(new RoteiroDeTestesTestCase())
    // Fim roteiro_de_testes.php
    // Inicio suite_test.php
    ->addCase(new TestSuiteTestCase())
    // Fim suite_test.php
    // Inicio temporal.php
    ->addCase(new DuracaoTestCase())
    ->addCase(new TempoTestCase())
    ->addCase(new DataTestCase())
    ->addCase(new DataTempoTestCase())
    ->addCase(new IntervaloDeTempoTestCase())
    // Fim temporal.php
    // Inicio tripulacao.php
    ->addCase(new TripulacaoTestCase())
    // Fim tripulacao.php
    // Inicio viagem_builder.php
    ->addCase(new ViagemBuilderTestCase())
    // Fim viagem_builder.php
    // Inicio voo.php
    ->addCase(new VooTestCase())
    // Fim voo.php
    ->setCheckShowPolicy(CheckShowPolicy::FAILURE)
    ->run();
