<?php
require_once 'temporal.php';
require_once 'identificadores.php';
require_once 'passageiro.php';
require_once 'ProgramaDeMilhagem.php';
require_once 'pontos.php';
require_once 'categoria.php';
require_once 'categoria_com_data.php';

class PassageiroVip extends Passageiro
{
    /**
     * @var Pontos[]
     */
    private array $pontuacao;
    private string $numero_de_registro;
    /**
     * @var CategoriaComData[]
     */
    private array $categoria_do_programa;
    private ProgramaDeMilhagem $programa_de_milhagem;

    public function __construct(
        //passageiro
        string             $nome,
        string             $sobrenome,
        DocumentoPessoa    $documento,
        Nacionalidade      $nacionalidade,
        ?CPF               $cpf,
        Data               $data_de_nascimento,
        Email              $email,

        //passageiro VIP
        string             $numero_de_registro,
        ProgramaDeMilhagem $programa_de_milhagem,
    )
    {
        parent::__construct($nome, $sobrenome, $documento, $nacionalidade, $cpf, $data_de_nascimento, $email);
        $this->pontuacao = [];
        $this->numero_de_registro = $numero_de_registro;
        $this->categoria_do_programa = [];
        $this->programa_de_milhagem = $programa_de_milhagem;
    }

    public function getNumeroDeRegistro(): string
    {
        return $this->numero_de_registro;
    }

    /**
     * @return CategoriaComData[]
     */
    public function getCategoriaDoPrograma(): array
    {
        return $this->categoria_do_programa;
    }

    public function getProgramaDeMilhagem(): ProgramaDeMilhagem
    {
        return $this->programa_de_milhagem;
    }

    public function addPontos(int $pontos, DataTempo $dataTempoParaTeste = null): void
    {
        $this->pontuacao[] = new Pontos($pontos, $dataTempoParaTeste ?? DataTempo::agora());
    }


    public function alterarCategoria(Categoria $categoria, DataTempo $dataTempoParaTeste = null)
    {
        if (!in_array($categoria, $this->programa_de_milhagem->getCategorias())) {
            throw new InvalidArgumentException("A categoria deve estar no programa de milhagem");
        }
        $pontuacao = $this->getPontosValidos();
        if ($pontuacao < $categoria->getPontuacao()) {
            throw new InvalidArgumentException("A categoria precisa de mais pontos que o passageiro tem");
        }
        $this->categoria_do_programa[] = new CategoriaComData($categoria, $dataTempoParaTeste ?? DataTempo::agora());
    }

    public function getPontosValidos(): int
    {
        $now = DataTempo::agora();
        $oneYear = new Duracao(365, 0);
        $lastYear = $now->sub($oneYear);
        $pontuacao = 0;

        foreach ($this->pontuacao as $ponto) {
            $dataNoPonto = $ponto->getDataDeObtencao();
            if ($dataNoPonto->gte($lastYear)) {
                $pontuacao += $ponto->getPontosGanhos();
            }
        }
        return $pontuacao;
    }
}