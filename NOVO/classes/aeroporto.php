<?php
require_once 'estado.php';
require_once 'identificadores.php';

class Aeroporto {
    public $sigla;
    public $cidade;
    public $estado;

    public function __construct(
        SiglaAeroporto $sigla,
        string $cidade,
        Estado $estado
    ) {
        $this->sigla = $sigla;
        $this->cidade = $cidade;
        $this->estado = $estado;
    }
}
