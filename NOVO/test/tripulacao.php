<?php
    require_once "suite.php";
    require_once "../classes/tripulacao.php";

    class TripulacaoTestCase extends TestCase {
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
            
            $this->checkNull($tripulacaoVazia->getCopiloto());
            $this->checkNull($tripulacaoVazia->getPiloto());
            $this->checkNotNull($tripulacaoVazia->getComissarios());
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
            $this->checkEq($tripulacao->getComissarios(), [$comissario1]);

            //adicionar comissário já adicinoado
            try {
                $tripulacao->addComissario($comissario1);
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            
            // setar pela segunda vez o piloto
            try {
                $tripulacao->setPiloto($piloto2);
                $this->checkNotReached();
            } catch (Exception $e) {
                $this->checkReached();
            }

            // setar pela segunda vez o copiloto
            try {
                $tripulacao->setCopiloto($copiloto2);
                $this->checkNotReached();
            } catch (Exception $e) {
                $this->checkReached();
            }            
            
            #Método de trancar voo
            
            $tripulacaoSemPiloto = new Tripulacao(null, $copiloto1, null);
            $tripulacaoSemPiloto->addComissario($comissario1);
            $tripulacaoSemPiloto->addComissario($comissario2);
            
            // se lança exceção quando não há piloto
            try {
                $tripulacaoSemPiloto->trancar();
                $this->checkNotReached();
            } catch (Exception $e) {
                $this->checkReached();
            }

            $tripulacaoSemCopiloto = new Tripulacao($piloto1, null, null);
            $tripulacaoSemCopiloto->addComissario($comissario1);
            $tripulacaoSemCopiloto->addComissario($comissario2);

            //se lança exceção quando não há copiloto
            try {
                $tripulacaoSemCopiloto->trancar();
                $this->checkNotReached();
            } catch(Exception $e) {
                $this->checkReached();
            }

            $tripulacaoSemComissariosSuficientes = new Tripulacao($piloto1, $copiloto1, null);
            $tripulacaoSemComissariosSuficientes->addComissario($comissario1);
            
            // se lança exceção quando há menos de 2 comissarios
            try {
                $tripulacaoSemComissariosSuficientes->trancar();
                $this->checkNotReached();
            } catch (Exception $e) {
                $this->checkReached();
            }
            
            $tripulacaoCompleta = new Tripulacao($piloto2, $copiloto2, null);
            $tripulacaoCompleta->addComissario($comissario1);
            $tripulacaoCompleta->addComissario($comissario2);
            $tripulacaoCompleta->trancar();

            $this->checkEq($tripulacaoCompleta->getTrancado(), true);

            //se lança exceção quando já está trancado e tenta trancar novamente
            try {
                $tripulacaoCompleta->trancar();
                $this->checkNotReached();
            } catch (Exception $e) {
                $this->checkReached();
            }

            //testa setar o piloto com voo trancado
            try {
                $tripulacaoCompleta->setPiloto($piloto1);
                $this->checkNotReached();
            } catch(Exception $e) {
                $this->checkReached();
            }

            // testa setar o copiloto com voo trancado
            try {
                $tripulacaoCompleta->setCopiloto($copiloto1);
                $this->checkNotReached();
            } catch(Exception $e) {
                $this->checkReached();
            }

            // testa adicionar comissário com voo trancado
            try {
                $tripulacaoCompleta->addComissario($comissario3);
                $this->checkNotReached();
            } catch(Exception $e) {
                $this->checkReached();
            }
        }
    }
?>