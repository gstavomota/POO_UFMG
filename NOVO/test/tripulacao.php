<?php
    include_once "suite.php";
    include_once "../classes/tripulacao.php";

    class tripulacaoTestCase extends TestCase {
        protected function getName(): string {
            return "Tripulacao";
        }

        public function run() {
            $gerador_tripulante = new GeradorDeRegistroDeTripulante();
            $piloto1 = $gerador_tripulante->gerar();
            $piloto2 = $gerador_tripulante->gerar();
            $copiloto1 = $gerador_tripulante->gerar();
            $copiloto2 = $gerador_tripulante->gerar();
            $comissario1 = $gerador_tripulante->gerar();
            $comissario2 = $gerador_tripulante->gerar();
            $comissario3 = $gerador_tripulante->gerar();

            #Getters
            $this->startSection("Getters");

            $tripulacaoVazia = new Tripulacao(null, null, null);
            
            $this->checkEq($tripulacaoVazia->getCopiloto(), null);
            $this->checkEq($tripulacaoVazia->getPiloto(), null);
            $this->checkNeq($tripulacaoVazia->getComissarios(), null);
            $this->checkTrue($tripulacaoVazia->getTrancado() === false);

            #Métodos
            $this->startSection("Métodos");

            $tripulacao = new Tripulacao(null, null, null);

            // setar pela primeira vez o piloto
            $tripulacao->setPiloto($piloto1);
            $this->checkEq($tripulacao->getPiloto(), $piloto1);

            // setar pela primeira vez copiloto
            $tripulacao->setCopiloto($copiloto1);
            $this->checkEq($tripulacao->getCopiloto(), $copiloto1);

            // adicionar comissário
            $tripulacao->addComissario($comissario1);
            $this->checkEq($tripulacao->getComissarios(), $comissario1);

            //adicionar comissário já adicinoado
            try {
                $tripulacao->addComissario($comissario1);
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }
            
            // setar pela segunda vez o piloto
            try {
                $tripulacao->setPiloto($piloto2);
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            // setar pela segunda vez o copiloto
            try {
                $tripulacao->setCopiloto($copiloto2);
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }            
            
            #Método de trancar voo
            
            $tripulacaoSemPiloto = new Tripulacao(null, $copiloto1, null);
            $tripulacaoSemPiloto->addComissario($comissario1);
            $tripulacaoSemPiloto->addComissario($comissario2);
            
            // se lança exceção quando não há piloto
            try {
                $tripulacaoSemPiloto->trancar();
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            $tripulacaoSemCopiloto = new Tripulacao($piloto1, null, null);
            $tripulacaoSemCopiloto->addComissario($comissario1);
            $tripulacaoSemCopiloto->addComissario($comissario2);

            //se lança exceção quando não há copiloto
            try {
                $tripulacaoSemCopiloto->trancar();
                $this->checkReached();
            } catch(InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            $tripulacaoSemComissariosSuficientes = new Tripulacao($piloto1, $copiloto1, null);
            $tripulacaoSemComissariosSuficientes->addComissario($comissario1);
            
            // se lança exceção quando há menos de 2 comissarios
            try {
                $tripulacaoSemComissariosSuficientes->trancar();
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }
            
            $tripulacaoCompleta = new Tripulacao($piloto2, $copiloto2, null);
            $tripulacaoCompleta->addComissario($comissario1);
            $tripulacaoCompleta->addComissario($comissario2);
            $tripulacaoCompleta->trancar();

            $this->checkEq($tripulacaoCompleta->getTrancado(), true);

            //se lança exceção quando já está trancado e tenta trancar novamente
            try {
                $tripulacaoCompleta->trancar();
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            //testa setar o piloto com voo trancado
            try {
                $tripulacaoCompleta->setPiloto($piloto1);
                $this->checkReached();
            } catch(InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            // testa setar o copiloto com voo trancado
            try {
                $tripulacaoCompleta->setCopiloto($copiloto1);
                $this->checkReached();
            } catch(InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            // testa adicionar comissário com voo trancado
            try {
                $tripulacaoCompleta->addComissario($comissario3);
                $this->checkReached();
            } catch(InvalidArgumentException $e) {
                $this->checkNotReached();
            }
        }
    }
?>