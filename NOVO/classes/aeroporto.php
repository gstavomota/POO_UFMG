<?php
require_once ("estado.php");
require_once ("identificadores.php");
require_once 'coordenada.php';

class Aeroporto
{
    private SiglaAeroporto $sigla;
    private string $cidade;
    private Estado $estado;

    public function __construct(
        SiglaAeroporto $sigla,
        string $cidade,
        Estado $estado,
    )
    {
        $this->sigla = $sigla;
        $this->cidade = $cidade;
        $this->estado = $estado;
    }

    public function getSigla(): SiglaAeroporto
    {
        return $this->sigla;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function getEstado(): Estado
    {
        return $this->estado;
    }



}
