<?php
require_once "suite.php";
require_once "../classes/encontrar_voo_strategy.php";
require_once "mixins/voos_mixin.php";

class EncontrarVoosSemConexaoStrategyTestCase extends TestCase
{
    use VoosMixin;

    protected function getName(): string
    {
        return "EncontrarVoosSemConexaoStrategy";
    }

    public function run()
    {
        $this->initVoos();
        # nenhum voo
        $strategy = new EncontrarVoosSemConexaoStrategy();
        $sigla = new SiglaCompanhiaAerea("AZ");
        $aeronave2p = $this->aeronaveDoisPassageiros($sigla);
        $hora9am = new Tempo(9, 0, 0);
        $hoje = Data::hoje();
        $amanha = $hoje->add(Duracao::umDia());
        $nenhumVoo = [];
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, $nenhumVoo);
        $todosDiasDaSemana = DiaDaSemana::cases();
        $this->startSection("nenhum voo");
        $this->checkEq($resultado, []);
        # vooDesejado voo confins afonso pena
        $vooConfinsAfonsoPena = $this->vooConfinsAfonsoPena($sigla, $hora9am, $aeronave2p->getRegistro(), $todosDiasDaSemana, 1000);
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, [$vooConfinsAfonsoPena]);
        $this->startSection("vooDesejado voo confins afonso pena");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooDesejado", $vooConfinsAfonsoPena, $hoje, $this->siglaConfins, $this->siglaCongonhas));
        $this->checkEq($resultado, []);
        # vooDesejado voo afonso pena congonhas
        $vooAfonsoPenaCongonhas = $this->vooAfonsoPenaCongonhas($sigla, $hora9am, $aeronave2p->getRegistro(), $todosDiasDaSemana, 1000);
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, [$vooAfonsoPenaCongonhas]);
        $this->startSection("vooDesejado voo afonso pena congonhas");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooDesejado", $vooAfonsoPenaCongonhas, $hoje, $this->siglaConfins, $this->siglaCongonhas));
        $this->checkEq($resultado, []);
        # vooDesejado voo confins congonhas somente dia de amanha da semana
        $vooConfinsCongonhasAmanha = $this->vooConfinsCongonhas($sigla, $hora9am, $aeronave2p->getRegistro(), [$amanha->getDiaDaSemana()], 1000);
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, [$vooConfinsCongonhasAmanha]);
        $this->startSection("vooDesejado voo confins congonhas somente dia de amanha da semana");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooDesejado", $vooConfinsCongonhasAmanha, $hoje, $this->siglaConfins, $this->siglaCongonhas));
        $this->checkEq($resultado, []);
        # vooDesejado voo confins congonhas hoje
        $vooConfinsCongonhasHoje = $this->vooConfinsCongonhas($sigla, $hora9am, $aeronave2p->getRegistro(), [$hoje->getDiaDaSemana()], 1000);
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, [$vooConfinsCongonhasHoje]);
        $this->startSection("vooDesejado voo confins congonhas hoje");
        $this->checkTrue($this->runNonPublicMethod($strategy, "vooDesejado", $vooConfinsCongonhasHoje, $hoje, $this->siglaConfins, $this->siglaCongonhas));
        $this->checkEq($resultado, [[$vooConfinsCongonhasHoje->getCodigo()]]);
        # dois voos confins congonhas hoje
        $vooConfinsCongonhasHoje_2 = $this->vooConfinsCongonhas($sigla, $hora9am, $aeronave2p->getRegistro(), [$hoje->getDiaDaSemana()], 1000);
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, [$vooConfinsCongonhasHoje, $vooConfinsCongonhasHoje_2]);
        $this->startSection("dois voos confins congonhas hoje");
        $this->checkEq($resultado, [[$vooConfinsCongonhasHoje->getCodigo()], [$vooConfinsCongonhasHoje_2->getCodigo()]]);
    }
}

class EncontrarVoosComUmaConexaoStrategyTestCase extends TestCase
{
    use VoosMixin;

    protected function getName(): string
    {
        return "EncontrarVoosComUmaConexaoStrategy";
    }

    public function run()
    {
        $this->initVoos();
        # vooIntermediarioDesejado amanha
        $strategy = new EncontrarVoosComUmaConexaoStrategy();
        $sigla = new SiglaCompanhiaAerea("AZ");
        $aeronave2p = $this->aeronaveDoisPassageiros($sigla);
        $aeronave2p_2 = $this->aeronaveDoisPassageiros($sigla);
        $hora9am = new Tempo(9, 0, 0);
        $hora1pm = new Tempo(13, 0, 0);
        $hoje = Data::hoje();
        $amanha = $hoje->add(Duracao::umDia());
        $todosDiasDaSemana = DiaDaSemana::cases();
        $vooConfinsAfonsoPenaAmanha = $this->vooConfinsAfonsoPena($sigla, $hora9am, $aeronave2p->getRegistro(), [$amanha->getDiaDaSemana()], 1000);
        $this->startSection("vooIntermediarioDesejado amanha");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooIntermediarioDesejado", $vooConfinsAfonsoPenaAmanha, $hoje, $this->siglaConfins));
        # vooIntermediarioDesejado outro aeroporto
        $vooAfonsoPenaCongonhas = $this->vooAfonsoPenaCongonhas($sigla, $hora1pm, $aeronave2p->getRegistro(), $todosDiasDaSemana, 1000);
        $this->startSection("vooIntermediarioDesejado outro aeroporto");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooIntermediarioDesejado", $vooAfonsoPenaCongonhas, $hoje, $this->siglaConfins));
        # vooIntermediarioDesejado correto
        $vooConfinsAfonsoPena = $this->vooConfinsAfonsoPena($sigla, $hora9am, $aeronave2p->getRegistro(), $todosDiasDaSemana, 1000);
        $vooConfinsGaleao = $this->vooConfinsGaleao($sigla, $hora9am, $aeronave2p_2->getRegistro(), $todosDiasDaSemana, 2000);
        $this->startSection("vooIntermediarioDesejado correto");
        $this->checkTrue($this->runNonPublicMethod($strategy, "vooIntermediarioDesejado", $vooConfinsAfonsoPena, $hoje, $this->siglaConfins));
        $this->checkTrue($this->runNonPublicMethod($strategy, "vooIntermediarioDesejado", $vooConfinsGaleao, $hoje, $this->siglaConfins));
        # vooFinalDesejado amanha
        $vooAfonsoPenaCongonhasAmanha = $this->vooAfonsoPenaCongonhas($sigla, $hora1pm, $aeronave2p->getRegistro(), [$amanha->getDiaDaSemana()], 1000);
        $vooGaleaoCongonhasAmanha = $this->vooGaleaoCongonhas($sigla, $hora1pm, $aeronave2p_2->getRegistro(), [$amanha->getDiaDaSemana()], 1000);
        $this->startSection("vooFinalDesejado amanha");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooAfonsoPenaCongonhasAmanha, $vooConfinsAfonsoPena, $hoje, $this->siglaCongonhas));
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooGaleaoCongonhasAmanha, $vooConfinsGaleao, $hoje, $this->siglaCongonhas));
        # vooFinalDesejado aeroporto incorreto
        $this->startSection("vooFinalDesejado aeroporto incorreto");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooConfinsAfonsoPena, $vooConfinsAfonsoPena, $hoje, $this->siglaCongonhas));
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooConfinsGaleao, $vooConfinsGaleao, $hoje, $this->siglaCongonhas));
        # vooFinalDesejado hora de partida
        $vooAfonsoPenaCongonhasMuitoCedo = $this->vooAfonsoPenaCongonhas($sigla, $hora9am, $aeronave2p->getRegistro(), $todosDiasDaSemana, 1000);
        $vooGaleaoCongonhasMuitoCedo = $this->vooGaleaoCongonhas($sigla, $hora9am, $aeronave2p_2->getRegistro(), $todosDiasDaSemana, 2000);
        $this->startSection("vooFinalDesejado hora de partida");
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooAfonsoPenaCongonhasMuitoCedo, $vooConfinsAfonsoPena, $hoje, $this->siglaCongonhas));
        $this->checkFalse($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooGaleaoCongonhasMuitoCedo, $vooConfinsGaleao, $hoje, $this->siglaCongonhas));
        # vooFinalDesejado correto
        $vooAfonsoPenaCongonhas = $this->vooAfonsoPenaCongonhas($sigla, $hora1pm, $aeronave2p->getRegistro(), $todosDiasDaSemana, 1000);
        $vooGaleaoCongonhas = $this->vooGaleaoCongonhas($sigla, $hora1pm, $aeronave2p_2->getRegistro(), $todosDiasDaSemana, 2000);
        $this->startSection("vooFinalDesejado correto");
        $this->checkTrue($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooAfonsoPenaCongonhas, $vooConfinsAfonsoPena, $hoje, $this->siglaCongonhas));
        $this->checkTrue($this->runNonPublicMethod($strategy, "vooFinalDesejado", $vooGaleaoCongonhas, $vooConfinsGaleao, $hoje, $this->siglaCongonhas));
        # encontrar
        $voos = [$vooAfonsoPenaCongonhas, $vooConfinsAfonsoPena, $vooGaleaoCongonhas, $vooConfinsGaleao];
        $resultado = $strategy->encontrar($hoje, $this->siglaConfins, $this->siglaCongonhas, $voos);
        $this->startSection("encontrar");
        $this->checkEq($resultado, [
            [$vooConfinsAfonsoPena->getCodigo(), $vooAfonsoPenaCongonhas->getCodigo()],
            [$vooConfinsGaleao->getCodigo(), $vooGaleaoCongonhas->getCodigo()]
        ]);
    }
}