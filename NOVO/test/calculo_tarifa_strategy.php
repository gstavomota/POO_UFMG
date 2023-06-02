<?php
include_once 'suite.php';
include_once '../classes/calculo_tarifa_strategy.php';

class PassageiroComumCalculoTarifaStrategyTestCase extends TestCase {

    protected function getName(): string
    {
        return "PassageiroComumCalculoTarifaStrategy";
    }

    public function run()
    {
        // TODO: Implement run() method.
    }
}

class PassageiroVipCalculoTarifaStrategyTestCase extends TestCase {

    protected function getName(): string
    {
        return "PassageiroVipCalculoTarifaStrategy";
    }

    public function run()
    {
        // TODO: Implement run() method.
    }
}

//class PassageiroComumCalculoTarifaTestCase extends TestCase
//{
//    protected function getName(): string
//    {
//        return "CalculaTarifaStrategy";
//    }
//
//    public function run()
//    {
//        $this->startSection("Calcula Tarifa De Passageiro Comum");
//        try {
//
//            $passageiro = new PassageiroComumCalculoTarifaStrategy(10.0, 15.0);
//
//            $franquia1 = new FranquiaDeBagagem(19.0);
//            $franquia2 = new FranquiaDeBagagem(21.0);
//            $arr = [$franquia1, $franquia2];
//            $franquias = new FranquiasDeBagagem($arr);
//
//
//            $this->checkEq(300.0, $passageiro->calcula($franquias));
//            $this->checkReached();
//
//        } catch (InvalidArgumentException $e) {
//            $this->checkReached();
//        }
//
//        $this->startSection("Calcula Tarifa De Passageiro Vip");
//        try {
//
//            $passageiro = new PassageiroVipCalculoTarifaStrategy(10.0, 15.0);
//
//            $franquia1 = new FranquiaDeBagagem(19.0);
//            $franquia2 = new FranquiaDeBagagem(21.0);
//            $arr = [$franquia1, $franquia2];
//            $franquias = new FranquiasDeBagagem($arr);
//
//
//            $this->checkEq(300.0, $passageiro->calcula($franquias));
//            $this->checkReached();
//
//        } catch (InvalidArgumentException $e) {
//            $this->checkReached();
//        }
//    }
//}