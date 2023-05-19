<?php
require_once 'passageiro.php';
require_once 'franquia_de_bagagem.php';

abstract class CalculoTarifaStrategy {
    abstract function calcula(FranquiasDeBagagem $franquias);
}

class PassageiroComumCalculoTarifaStrategy extends CalculoTarifaStrategy {
    private float $tarifa;
    private float $tarifa_franquia;

    public function __construct( float $tarifa, float $tarifa_franquia) {
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
    }

    public function calcula(FranquiasDeBagagem $franquias) : float{
        return $this->tarifa * count($franquias->franquias) * $this->tarifa_franquia;
    }
}

class PassageiroVipCalculoTarifaStrategy extends CalculoTarifaStrategy {
    private float $tarifa;
    private float $tarifa_franquia;

    public function __construct(float $tarifa, float $tarifa_franquia) {
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
    }

    public function calcula(FranquiasDeBagagem $franquias) : float {
        $franquias_a_serem_pagas = count($franquias->franquias);
        if ($franquias_a_serem_pagas == 0) {
            return $this->tarifa;
        }
        $franquias_a_serem_pagas--;

        return $this->tarifa * $franquias_a_serem_pagas * $this->tarifa_franquia / 2;
    }
}

function calculo_tarifa_strategy_for(bool $cliente_vip, float $tarifa, float $tarifa_franquia) : CalculoTarifaStrategy {
    if ($cliente_vip) {
        return new PassageiroVipCalculoTarifaStrategy($tarifa, $tarifa_franquia);
    }
    return new PassageiroComumCalculoTarifaStrategy($tarifa, $tarifa_franquia);
}
?>
