<?php


require_once 'Equatable.php';
require_once "log.php";

interface ICoordenada extends Equatable
{
    /** Retorna a coordenada X
     * @return float
     */
    public function getX(): float;

    /** Retorna a coordenada Y
     * @return float
     */
    public function getY(): float;
}

class Coordenada implements ICoordenada
{

    private float $x;
    private float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;

    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->x == $outro->x && $this->y == $outro->y;
    }

    public function getX(): float
    {
        return log::getInstance()->logRead($this->x);
    }

    public function getY(): float
    {
        return log::getInstance()->logRead($this->y);
    }
}