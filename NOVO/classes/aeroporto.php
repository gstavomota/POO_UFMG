<?php
require_once ("estado.php");
require_once ("identificadores.php");
require_once 'coordenada.php';
require_once "persist.php";

/**
 * @extends persist<Aeroporto>
 */
class Aeroporto extends persist
{
    private SiglaAeroporto $sigla;
    private string $nome;
    private string $cidade;
    private Estado $estado;
    private ICoordenada $coordenada;
    private static string $local_filename = "aeroporto.txt";

    public function __construct(
        SiglaAeroporto $sigla,
        string $nome,
        string $cidade,
        Estado $estado,
        ICoordenada $coordenada,
        ...$args
    )
    {
        $this->sigla = $sigla;
        $this->nome = $nome;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->coordenada = $coordenada;
        parent::__construct(...$args);
    }

    public function getSigla(): SiglaAeroporto
    {
        return $this->sigla;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function getEstado(): Estado
    {
        return $this->estado;
    }

    /**
     * @return ICoordenada
     */
    public function getCoordenada(): ICoordenada
    {
        return $this->coordenada;
    }


    static public function getFilename(): string
    {
        return self::$local_filename;
    }

    /**
     * @param SiglaAeroporto $sigla
     * @return Aeroporto[]
     * @throws Exception
     */
    static public function getRecordsBySigla(SiglaAeroporto $sigla): array {
        return parent::getRecordsByField("sigla", $sigla);
    }
}
