<?php

include_once 'suite.php';
include_once '../classes/franquia_de_bagagem.php';

class FranquiaDeBagagemTestCase extends TestCase {

    protected function getName(): string {
        return "FranquiaDeBagagem";
    }

    public function run() {
        # Constructor
        // Aqui já testo o construtor e a validação de peso??
        $this->startSection("Constructor");
        try {
            $franquia = new FranquiaDeBagagem(25.0);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        try {
            $franquia = new FranquiaDeBagagem(20.0);
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }
    }
}

class FranquiasDeBagagemTestCase extends TestCase {

    protected function getName(): string {
        return "FranquiasDeBagagem";
    }

    public function run() {
        # Constructor
        // Aqui já testo o construtor e a validação de franquias??
        $this->startSection("Constructor");
        try {
            $arr = ['franquia1', 'franquia2', 'franquia3', 'franquia4'];
            $franquias = new FranquiasDeBagagem($arr);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }
        try {
            $arr = ['franquia1', 'franquia2', 'franquia3'];
            $franquias = new FranquiasDeBagagem($arr);
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }

        # carga
        $this->startSection("Carga");
        try {
            $franquia1 = new FranquiaDeBagagem(19.0);
            $franquia2 = new FranquiaDeBagagem(21.0);
            $arr = [$franquia1, $franquia2];
            $franquias = new FranquiasDeBagagem($arr);
            $this->checkEq(40.0, $franquias->carga());
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }

        # numero_de_franquias
        $this->startSection("Número de Franquias");
        try {
            $franquia1 = new FranquiaDeBagagem(15.0);
            $franquia2 = new FranquiaDeBagagem(22.0);
            $arr = [$franquia1, $franquia2];
            $franquias = new FranquiasDeBagagem($arr);
            $this->checkEq(2, $franquias->numeroDeFranquias());
            $this->checkReached();
        } catch (Exception $e) {
            $this->checkNotReached();
        }
    }
}
