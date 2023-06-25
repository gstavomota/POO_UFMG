<?php

require_once "log.php";
class FranquiaDeBagagem
{
    private float $peso;

    public function __construct(float $peso)
    {
        $this->peso = FranquiaDeBagagem::validarPeso($peso);
    }

    private static function validarPeso(float $peso): float
    {
        if ($peso > 23.0) {
            throw new Exception("O peso máximo de uma franquia é 23kg");
        }
        return $peso;
    }

    /**
     * @return float
     */
    public function getPeso(): float
    {
        return log::getInstance()->logRead($this->peso);
    }
}

class FranquiasDeBagagem
{
    /**
     * @var FranquiaDeBagagem[]
     */
    private array $franquias;

    public function __construct(array $franquias)
    {
        $this->franquias = FranquiasDeBagagem::validarFranquias($franquias);
    }

    private static function validarFranquias(array $franquias): array
    {
        if (count($franquias) > 3) {
            throw new Exception("No máximo três franquias são suportadas");
        }
        return $franquias;
    }

    public function carga(): float
    {
        $carga = 0;
        foreach ($this->franquias as $franquia) {
            $carga += $franquia->getPeso();
        }
        return log::getInstance()->logCall($carga);
    }

    public function numeroDeFranquias(): int
    {
        return log::getInstance()->logCall(count($this->franquias));
    }
}
