<?php

require_once "log.php";
class Categoria
{

    private string $nome;
    private int $pontuacao;

    public function __construct(string $p_nome, int $p_pontuacao)
    {
        $this->nome = $p_nome;
        $this->pontuacao = $p_pontuacao;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPontuacao(): int
    {
        return $this->pontuacao;
    }
}

?>