<?
require_once('Passageiro.php');
class PassageiroVip extends Passageiro
{
    private DateTime $data_de_inicio;
    private DateTime $data_do_upgrade;
    private DateTime $data_do_futuro_downgrade;
    private array $pontuacao;
    private string $numero_de_registro;
    private ProgramaDeMilhagem $categoria_do_programa;
    private ProgramaDeMilhagem $programa_de_milhagem;
    public function __construct(
        DateTime $data_do_upgrade,
        array $pontuacao,
        string $numero_de_registro,
        ProgramaDeMilhagem $categoria_do_programa,
        ProgramaDeMilhagem $programa_de_milhagem,
    ) {
        $dataDeAgora = new DateTime();
        $this->data_de_inicio = $dataDeAgora->format('Y-m-d H:i:s');
        $this->data_do_upgrade = $data_do_upgrade;
        $this->numero_de_registro = $numero_de_registro;
        $this->categoria_do_programa = $categoria_do_programa;
        $this->programa_de_milhagem = $programa_de_milhagem;
    }

    public function getVip(): self{
        return $this;
    }

    public function calculaDataDoDowngrade(){
        $intervalo = new DateInterval('P12M');
        $this->data_do_futuro_downgrade = $this->data_de_inicio->format('Y-m-d');
    }

    public function addPontos(){
        array_push($this->pontuacao, $this->programa_de_milhagem->retornaPontos());
    }
}