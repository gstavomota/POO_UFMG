<?
require_once('passageiro.php');
require_once('pontos.php');
class PassageiroVip extends Passageiro
{
    private array $pontuacao;
    private string $numero_de_registro;
    private array $categoria_do_programa;
    private ProgramaDeMilhagem $programa_de_milhagem;

    public function __construct(
        //passageiro
        string $nome,
        string $sobrenome,
        DocumentoPassageiro $documento,
        string $nacionalidade,
        ?CPF $cpf,
        DateTime $data_de_nascimento,
        Email $email,
        array $passagens,
        
        //passageiro VIP
        array $pontuacao,
        string $numero_de_registro,
        array $categoria_do_programa,
        ProgramaDeMilhagem $programa_de_milhagem,
    ) {
        parent::__construct($nome, $sobrenome, $documento, $nacionalidade, $cpf, $data_de_nascimento, $email, $passagens);
        $this->numero_de_registro = $numero_de_registro;
        $this->categoria_do_programa = $categoria_do_programa;
        $this->programa_de_milhagem = $programa_de_milhagem;
    }

    public function addPontos(int $pontos){
        array_push($this->pontuacao, new Pontos($pontos, new DateTime()));
    }
    public function getCategoriaDoPrograma(){
        return $this->categoria_do_programa;
    }
    public function alterarCategoria(Categoria $categoria){
        array_push($this->categoria_do_programa, new CategoriaComData($categoria, new DateTime()));
    }
    public function getPontosValidos(){
        $now = new DateTime();
        $lastYear = $now->modify('-1 year');
        $pontuacao = 0;

        foreach($this->pontuacao as $ponto){
            $dataNoPonto = $ponto->getDataDeObtencao();
            if($dataNoPonto->compare($lastYear) == 1){
                $pontuacao += $ponto->getPontosGanhos();
            }
        }
        return $pontuacao;
    }
}