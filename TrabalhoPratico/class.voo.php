//Raphael
<?php
  include_once("class.aeroporto.php");
  include_once("class.viagem.php");

  class Voo {
    private int $frequencia_voo;
    private DateTime $horario_decolagem_voo;
    private DateTime $horario_aterrissagem_voo;
    private int $duracao_voo;
    private string $sigla_voo;
    private Aeroporto $aeroporto_saida;
    private Aeroporto $aeroporto_chegada;
    private $viagem = array();

    public function __construct(int $frequencia, DateTime $decolagem, DateTime $aterrissagem, int $duracao, string $sigla){
      $this->frequencia_voo = $frequencia;
      $this->horario_decolagem_voo = $decolagem;
      $this->horario_aterrissagem_voo = $aterrissagem;
      $this->duracao_voo = $duracao;
      $this->sigla_voo = $sigla;
    }
    public function getFrequencia(){
      return $this->frequencia_voo;
    }
    public function getHorarioDecolagem(){
      return $this->horario_decolagem_voo;
    }
    public function getHorarioAterrissagem(){
      return $this->horario_aterrissagem_voo;
    }
    public function getDuracao(){
      return $this->duracao_voo;
    }
    public function getSigla(){
      return $this->sigla_voo;
    }

    public function exibeVoo(string $sigla_voo): void{
      echo "RELATÓRIO DO VOO $sigla_voo \n"
      echo getFrequencia(); echo "\n";
      echo getHorarioDecolagem(); echo "\n";
      echo getHorarioAterrissagem(); echo "\n";
      echo getDuracao(); echo "\n";
      echo getSigla(); echo "\n";
      echo $this->aeroporto_chegada.getEstado(); echo "\n";
      echo $this->aeroporto_saida.getEstado(); echo "\n";
    }
    public function validaDuracao(int $Duracao): void{
      if(is_null($Duracao)){
        print_r("A duração do voo não pode ser nula.");
        return;
      }
    }
  }

?>