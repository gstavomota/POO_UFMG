<?php
require_once "../classes/temporal.php";
require_once "suite.php";
class DuracaoTestCase extends TestCase {
    protected function getName(): string {
        return "Duracao";
    }
    public function run()
    {
        $duracaoZero = new Duracao(0,0);
        $duracaoUm = new Duracao(0, 1);
        # Positive comparissions
        $this->startSection("Positive Comparission");
        $this->checkGt($duracaoUm, $duracaoZero);
        $this->checkGte($duracaoUm, $duracaoZero);
        $this->checkSt($duracaoZero, $duracaoUm);
        $this->checkSte($duracaoZero, $duracaoUm);
        $this->checkNeq($duracaoZero, $duracaoUm);
        $duracaoUm_2 = new Duracao(0, 1);
        # Equal comparissions
        $this->startSection("Equal Comparission");
        $this->checkGte($duracaoUm, $duracaoUm_2);
        $this->checkSte($duracaoUm, $duracaoUm_2);
        $this->checkEq($duracaoUm, $duracaoUm_2);
        $duracaoMenosUm = new Duracao(0, -1);
        # Negative comparissions
        $this->startSection("Negative Comparission");
        $this->checkGt($duracaoUm, $duracaoMenosUm);
        $this->checkGte($duracaoUm, $duracaoMenosUm);
        $this->checkSt($duracaoMenosUm, $duracaoZero);
        $this->checkSte($duracaoMenosUm, $duracaoZero);
        $this->checkNeq($duracaoMenosUm, $duracaoZero);
        $this->checkNeq($duracaoMenosUm, $duracaoUm);
        # Add
        $this->startSection("Add");
        $this->checkEq($duracaoUm->add($duracaoMenosUm), $duracaoZero);
        # Sub
        $this->startSection("Sub");
        $this->checkEq($duracaoUm->sub($duracaoUm), $duracaoZero);
        # Div and mul
        $this->startSection("Mul");
        $this->checkEq($duracaoMenosUm, $duracaoUm->mul(-1));
        $this->startSection("Div");
        $this->checkEq($duracaoMenosUm->div(-1), $duracaoUm);
        # Normalization
        $duracaoUmDia = new Duracao(0, 24*60*60);
        $duracaoUmDia_2 = new Duracao(1, 0);
        $this->startSection("Normalization");
        $this->checkEq($duracaoUmDia, $duracaoUmDia_2);
        # Stringification
        $duracaoMenosUmDia = new Duracao(-1, 0);
        $this->startSection("Stringfication");
        $this->checkEq("{$duracaoUm}", "+0d1s");
        $this->checkEq("{$duracaoMenosUm}", "-0d1s");
        $this->checkEq("{$duracaoUmDia}", "+1d0s");
        $this->checkEq("{$duracaoMenosUmDia}", "-1d0s");
        # Constants
        $this->startSection("Constants");
        $this->checkEq(Duracao::umMes(), new Duracao(31, 0));
        $this->checkEq(Duracao::umDia(), new Duracao(1, 0));
        $this->checkEq(Duracao::umaHora(), new Duracao(0, 60*60));
        $this->checkEq(Duracao::meiaHora(), new Duracao(0, 30*60));
        $this->checkEq(Duracao::umaHoraEMeia(), new Duracao(0, 90*60));
        # fromDateInterval
        $hoje = new DateTime();
        $amanha = new DateTime();
        $amanha->modify("+1 day");
        $dateIntervalUmDia = $amanha->diff($hoje);
        $this->startSection("FromDateInterval");
        $this->checkEq(Duracao::fromDateInterval($dateIntervalUmDia), $duracaoUmDia);
        # Getters
        $this->startSection("Getters");
        $duracaoUmUm = new Duracao(1,1);
        $this->checkEq($duracaoUmUm->getDia(), 1);
        $this->checkEq($duracaoUmUm->getSegundo(), 1.0);
        $duracaoMenosUmMenosUm = new Duracao(-1,-1);
        $this->checkEq($duracaoMenosUmMenosUm->getDia(), -1);
        $this->checkEq($duracaoMenosUmMenosUm->getSegundo(), -1.0);
    }
}
class TempoTestCase extends TestCase {
    protected function getName(): string {
        return "Tempo";
    }
    public function run()
    {
        $tempoMeiaNoite = new Tempo(0,0,0);
        $tempoUmaDaManha = new Tempo(1,0,0);
        $tempoUmaDaManha_2 = new Tempo(1,0,0);
        $tempoDuasDaManha = new Tempo(2,0,0);
        # Comparissions
        $this->startSection("Comparission");
        $this->checkGt($tempoUmaDaManha, $tempoMeiaNoite);
        $this->checkGte($tempoUmaDaManha, $tempoMeiaNoite);
        $this->checkSt($tempoMeiaNoite, $tempoUmaDaManha);
        $this->checkSte($tempoMeiaNoite, $tempoUmaDaManha);
        $this->checkEq($tempoUmaDaManha, $tempoUmaDaManha_2);
        # Addition and subtraction
        $this->startSection("Add");
        $this->checkEq($tempoUmaDaManha, $tempoMeiaNoite->add(Duracao::umaHora()));
        $this->startSection("Sub");
        $this->checkEq($tempoMeiaNoite, $tempoUmaDaManha->sub(Duracao::umaHora()));
        # Multiplication and division
        $this->startSection("Mul");
        $this->checkEq($tempoDuasDaManha, $tempoUmaDaManha->mul(2));
        $this->startSection("Div");
        $this->checkEq($tempoUmaDaManha, $tempoDuasDaManha->div(2));
        # Stringification
        $this->startSection("Stringfication");
        $this->checkEq("{$tempoMeiaNoite}", "0h0m0s");
        # Validation that Tempo is positive
        $this->startSection("Constructor");
        try {
            new Tempo(0,0,-1);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        # Getters
        $tempoUmUmUm = new Tempo(1,1,1);
        $this->startSection("Getters");
        $this->checkEq($tempoUmUmUm->getHora(), 1);
        $this->checkEq($tempoUmUmUm->getMinuto(), 1);
        $this->checkEq($tempoUmUmUm->getSegundo(), 1.0);
        # Dt
        $duracaoUmaHora = $tempoDuasDaManha->dt($tempoUmaDaManha);
        $duracaoMenosUmaHora = $tempoUmaDaManha->dt($tempoDuasDaManha);
        $umaHoraEmSegundos = 60*60.0;
        $this->startSection("Delta");
        $this->checkApproximate($duracaoUmaHora->getSegundo(), $umaHoraEmSegundos);
        $this->checkEq($duracaoUmaHora->getDia(), 0);
        $this->checkApproximate($duracaoMenosUmaHora->getSegundo(), -$umaHoraEmSegundos);
        $this->checkEq($duracaoMenosUmaHora->getDia(), 0);
    }
}

class DataTestCase extends TestCase {
    protected function getName(): string {
        return "Data";
    }
    public function run()
    {
        # Validations
        $this->startSection("Constructor");
        try {
            new Data(0, 1, 1);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }

        try {
            new Data(1, 0, 1);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }

        try {
            new Data(1, 1, 0);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }

        try {
            new Data(2023, 12, 31);
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }
        try {
            new Data(2023, 13, 0);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        try {
            new Data(2023, 12, 32);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        $data240523 = new Data(2023, 05, 24);
        $data240523_2 = new Data(2023, 05, 24);
        $data250523 = new Data(2023, 05, 25);
        $data260523 = new Data(2023, 05, 26);
        $data270523 = new Data(2023, 05, 27);
        $data280523 = new Data(2023, 05, 28);
        $data290523 = new Data(2023, 05, 29);
        $data300523 = new Data(2023, 05, 30);
        # Comparissions
        $this->startSection("Comparission");
        $this->checkGt($data250523, $data240523);
        $this->checkGte($data250523, $data240523);
        $this->checkGte($data240523, $data240523);
        $this->checkSte($data240523, $data240523);
        $this->checkSt($data240523, $data250523);
        $this->checkSte($data240523, $data250523);
        $this->checkGte($data240523, $data240523_2);
        $this->checkSte($data240523, $data240523_2);
        $this->checkEq($data240523, $data240523_2);
        # Addition and subtraction
        $duracaoUmDia = new Duracao(1, 0);
        $this->startSection("Add");
        $this->checkEq($data250523, $data240523->add($duracaoUmDia));
        $this->startSection("Sub");
        $this->checkEq($data240523, $data250523->sub($duracaoUmDia));
        # Getters
        $this->startSection("Getters");
        $this->checkEq($data240523->getAno(), 2023);
        $this->checkEq($data240523->getMes(), 5);
        $this->checkEq($data240523->getDia(), 24);
        # Dia da semana
        $this->startSection("DiaDaSemana");
        $this->checkEq($data240523->getDiaDaSemana(), DiaDaSemana::QUARTA);
        $this->checkEq($data250523->getDiaDaSemana(), DiaDaSemana::QUINTA);
        $this->checkEq($data260523->getDiaDaSemana(), DiaDaSemana::SEXTA);
        $this->checkEq($data270523->getDiaDaSemana(), DiaDaSemana::SABADO);
        $this->checkEq($data280523->getDiaDaSemana(), DiaDaSemana::DOMINGO);
        $this->checkEq($data290523->getDiaDaSemana(), DiaDaSemana::SEGUNDA);
        $this->checkEq($data300523->getDiaDaSemana(), DiaDaSemana::TERCA);
        # Stringification
        $this->startSection("Stringfication");
        $this->checkEq("{$data240523}", "24/5/2023");
        # From string
        $data240523_3 = Data::fromString("24/5/2023");
        $this->startSection("FromString");
        $this->checkEq($data240523_3, $data240523);
        # Delta
        $duracaoUmDia = new Duracao(1, 0);
        $duracaoUmDia_2 = $data250523->dt($data240523);
        $this->startSection("Delta");
        $this->checkEq($duracaoUmDia, $duracaoUmDia_2);
        $duracaoMenosUmDia = new Duracao(-1, 0);
        $duracaoMenosUmDia_2 = $data240523->dt($data250523);
        $this->checkEq($duracaoMenosUmDia, $duracaoMenosUmDia_2);
    }
}
class DataTempoTestCase extends TestCase {
    protected function getName(): string {
        return "DataTempo";
    }
    public function run()
    {
        $data240523 = new Data(2023, 05, 24);
        $data250523 = new Data(2023, 05, 25);
        $tempoMeiaNoite = Tempo::meiaNoite();
        $tempoMeioDia = new Tempo(12, 0, 0);
        $dataTempo240523_meiaNoite = new DataTempo($data240523, $tempoMeiaNoite);
        # Tempo com Data
        $this->startSection("Tempo comData");
        $this->checkEq($tempoMeiaNoite->comData($data240523), $dataTempo240523_meiaNoite);
        # Data com tempo
        $this->startSection("Data comTempo");
        $this->checkEq($data240523->comTempo($tempoMeiaNoite), $dataTempo240523_meiaNoite);
        # Getters
        $this->startSection("Getters");
        $this->checkEq($dataTempo240523_meiaNoite->getData(), $data240523);
        $this->checkEq($dataTempo240523_meiaNoite->getTempo(), $tempoMeiaNoite);
        $this->checkEq($dataTempo240523_meiaNoite->getDiaDaSemana(), $data240523->getDiaDaSemana());
        # Add
        $dataTempo250523_meioDia = new DataTempo($data250523, $tempoMeioDia);
        $duracaoUmDiaEDozeHoras = new Duracao(1, 12*60*60);
        $dataTempo250523_meioDia_2 = $dataTempo240523_meiaNoite->add($duracaoUmDiaEDozeHoras);
        $this->startSection("Add");
        $this->checkEq($dataTempo250523_meioDia_2, $dataTempo250523_meioDia);
        # Sub
        $this->startSection("Sub");
        $dataTempo240523_meiaNoite_2 = $dataTempo250523_meioDia->sub($duracaoUmDiaEDozeHoras);
        $this->checkEq($dataTempo240523_meiaNoite_2, $dataTempo240523_meiaNoite);
        # Comparissions
        $this->startSection("Comparission");
        $this->checkGt($dataTempo250523_meioDia, $dataTempo240523_meiaNoite);
        $this->checkGte($dataTempo250523_meioDia, $dataTempo240523_meiaNoite);
        $this->checkGte($dataTempo250523_meioDia, $dataTempo250523_meioDia_2);
        $this->checkSt($dataTempo240523_meiaNoite, $dataTempo250523_meioDia);
        $this->checkSte($dataTempo240523_meiaNoite, $dataTempo250523_meioDia);
        $this->checkSte($dataTempo250523_meioDia, $dataTempo250523_meioDia_2);
        $this->checkEq($dataTempo250523_meioDia, $dataTempo250523_meioDia_2);
        # Stringification
        $this->startSection("Stringfication");
        $this->checkEq("{$dataTempo240523_meiaNoite}", "24/5/2023 0h0m0s");
        # Delta
        $this->startSection("Delta");
        $this->checkEq($dataTempo250523_meioDia->dt($dataTempo240523_meiaNoite), $duracaoUmDiaEDozeHoras);
        $this->checkEq($dataTempo240523_meiaNoite->dt($dataTempo250523_meioDia), $duracaoUmDiaEDozeHoras->mul(-1));
    }
}
class IntervaloDeTempoTestCase extends TestCase {
    protected function getName(): string {
        return "IntervaloDeTempo";
    }
    public function run()
    {
        $tempoMeiaNoite = Tempo::meiaNoite();
        $tempoMeioDia = new Tempo(12,0,0);
        $data220523 = new Data(2023, 05, 22);
        $data230523 = new Data(2023, 05, 23);
        $data240523 = new Data(2023, 05, 24);
        $data250523 = new Data(2023, 05, 25);
        $data260523 = new Data(2023, 05, 26);
        $dataTempo220523_meiaNoite = new DataTempo($data220523, $tempoMeiaNoite);
        $dataTempo230523_meiaNoite = new DataTempo($data230523, $tempoMeiaNoite);
        $dataTempo240523_meiaNoite = new DataTempo($data240523, $tempoMeiaNoite);
        $dataTempo250523_meioDia = new DataTempo($data250523, $tempoMeioDia);
        $dataTempo260523_meiaNoite = new DataTempo($data260523, $tempoMeiaNoite);
        $intervalo220523_meiaNoite_ate_230523_meiaNoite = new IntervaloDeTempo($dataTempo220523_meiaNoite, $dataTempo230523_meiaNoite);
        $intervalo240523_meiaNoite_ate_260523_meiaNoite = new IntervaloDeTempo($dataTempo240523_meiaNoite, $dataTempo260523_meiaNoite);
        # DataTempo ate
        $this->startSection("DataTempo ate");
        $intervalo240523_meiaNoite_ate_260523_meiaNoite_2 = $dataTempo240523_meiaNoite->ate($dataTempo260523_meiaNoite);
        $this->checkEq($intervalo240523_meiaNoite_ate_260523_meiaNoite, $intervalo240523_meiaNoite_ate_260523_meiaNoite_2);
        # Construtor
        $this->startSection("Construtor");
        try {
            $dataTempo260523_meiaNoite->ate($dataTempo240523_meiaNoite);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        # Contem
        $this->startSection("Contem");
        $this->checkTrue($intervalo240523_meiaNoite_ate_260523_meiaNoite->contem($dataTempo250523_meioDia));
        $this->checkFalse($intervalo240523_meiaNoite_ate_260523_meiaNoite->contem($dataTempo230523_meiaNoite));
        # Comparission
        $this->startSection("Comparission");
        $this->checkGt($intervalo240523_meiaNoite_ate_260523_meiaNoite, $intervalo220523_meiaNoite_ate_230523_meiaNoite);
        $this->checkGte($intervalo240523_meiaNoite_ate_260523_meiaNoite, $intervalo220523_meiaNoite_ate_230523_meiaNoite);
        $this->checkGte($intervalo240523_meiaNoite_ate_260523_meiaNoite, $intervalo240523_meiaNoite_ate_260523_meiaNoite_2);
        $this->checkSt($intervalo220523_meiaNoite_ate_230523_meiaNoite, $intervalo240523_meiaNoite_ate_260523_meiaNoite);
        $this->checkSte($intervalo220523_meiaNoite_ate_230523_meiaNoite, $intervalo240523_meiaNoite_ate_260523_meiaNoite);
        $this->checkSte($intervalo240523_meiaNoite_ate_260523_meiaNoite, $intervalo240523_meiaNoite_ate_260523_meiaNoite_2);
        $this->checkEq($intervalo240523_meiaNoite_ate_260523_meiaNoite, $intervalo240523_meiaNoite_ate_260523_meiaNoite_2);

    }
}