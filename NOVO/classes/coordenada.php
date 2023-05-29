<?php

namespace Coordenada;
use Equatable;
use EquatableTypeException;

require_once ('Equatable.php');

class Coordenada implements Equatable {

    private float $x;
    private float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;

    }

    public function eq(Equatable $outro): bool {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->x == $outro->x && $this->y == $outro->y;
    }

    public function get_x(){
        return $this->x;
    }

    public function get_y(){
        return $this->y;
    }

}