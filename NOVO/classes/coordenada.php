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

        $this->checkEq();
    }

    public function eq(self $other): bool {
        return $this->x === $other->x && $this->y === $other->y;
    }

    public function get_x(){
        return $this->x;
    }

    public function get_y(){
        return $this->y;
    }

    private function checkEq() : void {
        $check = new Coordenada();
        if($this->eq($check)){
            throw new EquatableTypeException('As coordenadas fornecidas são iguais');
        }
    }
}