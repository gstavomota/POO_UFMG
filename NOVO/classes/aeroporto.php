<?php
require_once ("estado.php");
require_once ("identificadores.php");
require_once ("coordenada.php");

use Coordenada;

class Aeroporto
{
    private SiglaAeroporto $sigla;
    private string $cidade;
    private Estado $estado;

    private Coordenada $local_aeroporto;

    public function __construct(
        SiglaAeroporto $sigla,
        string $cidade,
        Estado $estado,
        Coordenada $local_aeroporto
    )
    {
        $this->sigla = $sigla;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->local_aeroporto = $local_aeroporto;
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

    public function getCoordenada(): Coordenada
    {
        return $this->local_aeroporto;
    }
}
