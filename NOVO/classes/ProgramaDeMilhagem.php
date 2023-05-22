<?php


require_once("passageiroVip.php");
require_once("categoria.php");

class ProgramaDeMilhagem
{
    private array $categorias;
    private string $nome_do_programa;

    public function __construct(array $categoria, string $nome_do_programa)
    {
        $this->categorias = $categoria;
        $this->nome_do_programa = $nome_do_programa;
    }

    public function getCategorias(): array
    {
        return $this->categorias;
    }

    public function getNomeDoPrograma(): string
    {
        return $this->nome_do_programa;
    }

    public function update(PassageiroVip $passageiro): Categoria|null
    {
        $categoriaDoPassageiro = $passageiro->getCategoriaDoPrograma();
        $categoria = $this->categorias[0];
        $pontuacao = $passageiro->getPontosValidos();
        foreach ($this->categorias as $categoriaAlvo) {
            if ($categoriaAlvo->getPontuacao() <= $pontuacao) {
                $categoria = $categoriaAlvo;
            }
        }
        if (count($categoriaDoPassageiro) > 1 and $categoriaDoPassageiro[count($categoriaDoPassageiro) - 1] == $categoria) {
            return null;
        }
        $passageiro->alterarCategoria($categoria);
        return $categoria;
    }
}

?>