<?php
require_once 'passageiro.php';
require_once 'franquia_de_bagagem.php';

abstract class CalculoTarifaStrategy {
    abstract function calcula($franquias);
}

class PassageiroComumCalculoTarifaStrategy extends CalculoTarifaStrategy {
    private $tarifa;
    private $tarifa_franquia;

    public function __construct($tarifa, $tarifa_franquia) {
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
    }

    public function calcula($franquias) {
        return $this->tarifa * count($franquias->franquias) * $this->tarifa_franquia;
    }
}

class PassageiroVipCalculoTarifaStrategy extends CalculoTarifaStrategy {
    private $tarifa;
    private $tarifa_franquia;

    public function __construct($tarifa, $tarifa_franquia) {
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
    }

    public function calcula($franquias) {
        $franquias_a_serem_pagas = count($franquias->franquias);
        if ($franquias_a_serem_pagas == 0) {
            return $this->tarifa;
        }
        $franquias_a_serem_pagas--;

        return $this->tarifa * $franquias_a_serem_pagas * $this->tarifa_franquia / 2;
    }
}

function calculo_tarifa_strategy_for($cliente_vip, $tarifa, $tarifa_franquia) {
    if ($cliente_vip) {
        return new PassageiroVipCalculoTarifaStrategy($tarifa, $tarifa_franquia);
    }
    return new PassageiroComumCalculoTarifaStrategy($tarifa, $tarifa_franquia);
}
?>
