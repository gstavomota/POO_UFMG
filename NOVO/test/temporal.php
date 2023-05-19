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
        $this->checkGt($duracaoUm, $duracaoZero);
        $this->checkGte($duracaoUm, $duracaoZero);
        $this->checkSt($duracaoZero, $duracaoUm);
        $this->checkSte($duracaoZero, $duracaoUm);
        $duracaoUm_2 = new Duracao(0, 1);
        $this->checkGte($duracaoUm, $duracaoUm_2);
        $this->checkSte($duracaoUm, $duracaoUm_2);
        $this->checkEq($duracaoUm, $duracaoUm_2);
    }
}