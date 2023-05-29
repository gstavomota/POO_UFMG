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
    $codigo = new CodigoDoAssento(Classe::EXECUTIVA, 'A', 3);
    try {
        // checando se o objeto foi construído com sucesso
        new Assento($codigo);
        $this->checkReached();
    } catch (InvalidArgumentException $e) {
        $this->checkNotReached();
    }

    $assentoTeste = new Assento($codigo);
    $registro = new RegistroDePassagem(12345);
    $franquias = new FranquiasDeBagagem([20, 10]);
    # Classe
    $this->startSection("Classe");
    $this->checkEq(Classe::EXECUTIVA, $assentoTeste->classe());

    # Preenchido
    $this->startSection("Preenchido");
    $this->checkEq(false, $assentoTeste->preenchido());

    # Vazio;
    $this->startSection("Vazio");
    $this->checkEq(true, $assentoTeste->vazio());

    # Liberar
    $this->startSection("Liberar");
    $this->checkEq("O assento não está preenchido", $assentoTeste->liberar());

    # Reservar
    $this->startSection("Reservar");
    $this->checkNeq("O assento está preenchido", $assentoTeste->reservar($registro, $franquias));
    $this->checkEq($registro, $assentoTeste->getPassagem());
    $this->checkEq($franquias, $assentoTeste->getFranquias());
    }

}