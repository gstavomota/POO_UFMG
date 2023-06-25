<?php
require_once 'identificadores.php';
require_once "Equatable.php";
require_once "log.php";

class Aeronave implements Equatable
{
    private SiglaCompanhiaAerea $companhia_aerea;
    private string $fabricante;
    private string $modelo;
    private int $capacidade_passageiros;
    private float $capacidade_carga;
    private RegistroDeAeronave $registro;

    public function __construct(
        SiglaCompanhiaAerea $companhia_aerea,
        string              $fabricante,
        string              $modelo,
        int                 $capacidade_passageiros,
        float               $capacidade_carga,
        RegistroDeAeronave  $registro
    )
    {
        $this->companhia_aerea = $companhia_aerea;
        $this->fabricante = $fabricante;
        $this->modelo = $modelo;
        $this->capacidade_passageiros = $capacidade_passageiros;
        $this->capacidade_carga = $capacidade_carga;
        $this->registro = $registro;
    }

    public function getSigla(): SiglaCompanhiaAerea
    {
        return log::getInstance()->logRead($this->companhia_aerea);
    }

    public function getFabricante(): string
    {
        return log::getInstance()->logRead($this->fabricante);
    }

    public function getModelo(): string
    {
        return log::getInstance()->logRead($this->modelo);
    }

    public function getCapacidadePassageiros(): int
    {
        return log::getInstance()->logRead($this->capacidade_passageiros);
    }

    public function getCapacidadeCarga(): float
    {
        return log::getInstance()->logRead($this->capacidade_carga);
    }

    public function getRegistro(): RegistroDeAeronave
    {
        return log::getInstance()->logRead($this->registro);
    }

    public function eq(Equatable $other): bool
    {
        if (!$other instanceof self) {
            throw new EquatableTypeException();
        }

        return $this->companhia_aerea->eq($other->companhia_aerea) &&
        $this->fabricante === $other->fabricante &&
        $this->modelo === $other->modelo &&
        $this->capacidade_passageiros === $other->capacidade_passageiros &&
        $this->capacidade_carga === $other->capacidade_carga &&
        $this->registro->eq($other->registro);
    }
}
