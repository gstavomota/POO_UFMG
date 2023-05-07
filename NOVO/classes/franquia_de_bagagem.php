<?php

class FranquiaDeBagagem{
    public float $peso;

    public function __construct(float $peso) {
        $this->peso = $peso;
    }

    public function validarPeso(float $peso): float {
        if ($peso > 23.0) {
            throw new Exception("O peso máximo de uma franquia é 23kg");
        }
        return $peso;
    }
}

class FranquiasDeBagagem{
    public array $franquias;

    public function __construct(array $franquias) {
        $this->franquias = $franquias;
    }

    public function validarFranquias(array $franquias): array {
        if (count($franquias) > 3) {
            throw new Exception("No máximo três franquias são suportadas");
        }
        return $franquias;
    }

    public function carga(): float {
        $carga = 0;
        foreach ($this->franquias as $franquia) {
            $carga += $franquia->peso;
        }
        return $carga;
    }
}
