<?php
require_once 'franquia_de_bagagem.php';
require_once "log.php";

interface CalculoTarifaStrategy
{
    function calcula(FranquiasDeBagagem $franquias): float;
}

class PassageiroComumCalculoTarifaStrategy implements CalculoTarifaStrategy
{
    private float $tarifa;
    private float $tarifa_franquia;

    public function __construct(float $tarifa, float $tarifa_franquia)
    {
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
    }

    public function calcula(FranquiasDeBagagem $franquias): float
    {
        return log::getInstance()->logCall($this->tarifa + $franquias->numeroDeFranquias() * $this->tarifa_franquia);
    }
}

class PassageiroVipCalculoTarifaStrategy implements CalculoTarifaStrategy
{
    private float $tarifa;
    private float $tarifa_franquia;

    public function __construct(float $tarifa, float $tarifa_franquia)
    {
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
    }

    public function calcula(FranquiasDeBagagem $franquias): float
    {
        $franquias_a_serem_pagas = $franquias->numeroDeFranquias();
        if ($franquias_a_serem_pagas == 0) {
            return log::getInstance()->logCall($this->tarifa);
        }
        $franquias_a_serem_pagas--;

        return log::getInstance()->logCall($this->tarifa + $franquias_a_serem_pagas * $this->tarifa_franquia / 2);
    }
}

function calculo_tarifa_strategy_for(bool $cliente_vip, float $tarifa, float $tarifa_franquia): CalculoTarifaStrategy
{
    if ($cliente_vip) {
        return new PassageiroVipCalculoTarifaStrategy($tarifa, $tarifa_franquia);
    }
    return new PassageiroComumCalculoTarifaStrategy($tarifa, $tarifa_franquia);
}

?>
