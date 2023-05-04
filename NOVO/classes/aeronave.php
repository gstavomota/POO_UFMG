<?php
require_once 'identificadores.php';

class Aeronave {
    public $companhia_aerea;
    public $fabricante;
    public $modelo;
    public $capacidade_passageiros;
    public $capacidade_carga;
    public $registro;

    public function __construct(
        SiglaCompanhiaAerea $companhia_aerea,
        string $fabricante,
        string $modelo,
        int $capacidade_passageiros,
        float $capacidade_carga,
        RegistroDeAeronave $registro
    ) {
        $this->companhia_aerea = $companhia_aerea;
        $this->fabricante = $fabricante;
        $this->modelo = $modelo;
        $this->capacidade_passageiros = $capacidade_passageiros;
        $this->capacidade_carga = $capacidade_carga;
        $this->registro = $registro;
    }
}
