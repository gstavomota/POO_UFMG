<?php
include_once 'suite.php';
include_once '../classes/assento.php';

class AssentoTestCase extends TestCase {

    protected function getName(): string {
            return "Assento";
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

    # Vazio
    // TODO
    $this->startSection("Vazio");
    // TODO

    # Liberar
    // TODO
    $this->startSection("Liberar");
    // TODO

    # Reservar
    $this->startSection("Reservar");
    // TODO
    }

}