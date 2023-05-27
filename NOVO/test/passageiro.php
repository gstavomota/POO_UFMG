<?php
include_once 'suite.php';
include_once '../classes/passageiro.php';

class PassageiroTestCase extends TestCase {

    protected function getName(): string {
            return "Passageiro";
    }

    public function run() {
    # Constructor
    $this->startSection("Constructor");
    try {
        // TODO
        $this->checkNotReached();
    } catch (InvalidArgumentException $e) {
        $this->checkReached();
    }
    try {
        // TODO
        $this->checkReached();
    } catch (InvalidArgumentException $e) {
        $this->checkNotReached();
    }

    # Valida_cpf_se_brasileiro
    // TODO
    $this->startSection("Valida_cpf_se_brasileiro");
    // TODO
    }
}