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
    try {
        $assentoTeste->liberar();
        $this->checkNotReached();
    } catch (PreenchimentoDeAssentoException $e) {
        $this->checkReached();
    }
    $assentoTeste->reservar($registro, $franquias);
    [$registro_2, $franquias_2] = $assentoTeste->liberar();
    $this->checkEq($registro_2, $registro);
    $this->checkEq($franquias_2, $franquias);

    # Reservar
    $this->startSection("Reservar");
    try {
        $assentoTeste->reservar($registro, $franquias);
        $this->checkReached();
    } catch (PreenchimentoDeAssentoException $e) {
        $this->checkNotReached();
    }
    $this->checkEq($registro, $assentoTeste->getPassagem());
    $this->checkEq($franquias, $assentoTeste->getFranquias());
    try {
        $assentoTeste->reservar($registro, $franquias);
        $this->checkNotReached();
    } catch (PreenchimentoDeAssentoException $e) {
        $this->checkReached();
    }
    }

}