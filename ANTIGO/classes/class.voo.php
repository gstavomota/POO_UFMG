<?php
//Raphael
  include_once( "persist.php" );
  include_once("class.aeroporto.php");
  include_once("class.viagem.php");
  include_once("class.companhia.php")

  class Voo {
    private int $frequencia_voo;
    private DateTime $horario_decolagem_voo;
    private DateTime $horario_aterrissagem_voo;
    private int $duracao_voo;
    private string $sigla_voo;
    private Aeroporto $aeroporto_saida;
    private Aeroporto $aeroporto_chegada;
    private $viagem = array();
    private string $codigo; 
    static protected $local_filename = "vooTst.txt";

    public function __construct(int $frequencia, DateTime $decolagem, DateTime $aterrissagem, int $duracao, string $sigla, Aeroporto $aeroporto_chegada, Aeroporto $aeroporto_saida string $codigo){
      $this->frequencia_voo = $frequencia;
      $this->horario_decolagem_voo = $decolagem;
      $this->horario_aterrissagem_voo = $aterrissagem;
      $this->duracao_voo = $duracao;
      $this->sigla_voo = $sigla;
      $this->aeroporto_chegada = $aeroporto_chegada;
      $this->aeroporto_saida = $aeroporto_saida;
      $this->codigo = $codigo;
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

    public function getAeroportoChegada(){
      return $this->aeroporto_chegada;
    }
    
    public function getAeroportoSaida(){
      return $this->aeroporto_saida;
    }
    
    public function getSigla(){
      return $this->sigla_voo;
    }

    public function getCodigo(){
      return $this->codigo;
    }
    
    public function exibeVoo(string $sigla_voo): void{
      echo "RELATÓRIO DO VOO ". $this->sigla_voo. "\n";
      echo $this->getFrequencia(); echo "\n";
      echo $this->getHorarioDecolagem()->format('d-m-Y H:i:s'); echo "\n";
      echo $this->getHorarioAterrissagem()->format('d-m-Y H:i:s'); echo "\n";
      echo $this->getDuracao(); echo "\n";
      echo $this->getSigla(); echo "\n";
      echo $this->aeroporto_chegada->getEstado(); echo "\n";
      echo $this->aeroporto_saida->getEstado(); echo "\n";
    }
    public function validaDuracao(int $Duracao): void{
      if(is_null($Duracao)){
        print_r("A duração do voo não pode ser nula.");
        return;
      }
    }
    
    public function validaCodigo(string $codigo): void{
      $duas_letras = substr($codigo, 0, 2);
      $tamanho = strlen($codigo);
      
      if(is_null($codigo)){
        print_r("O código do voo não pode ser nulo.");
        return;
      }
      if($tamanho != 6){
        throw new Exception("Código inválido");
      } else if($tamanho == 6 && !($duas_letras)){
        throw new Exception("Código inválido");
      } else if($tamanho == 6 && ($duas_letras == $p_sigla)){
        print_r("Codigo salvo");
      }
    }
    public function vooDiretoOuNao(): void{
      $conexoes = [];
        foreach ($this->$aeroporto_origem as $cidade) {
          if ($cidade != $aeroporto_destino) {
            for($i = 0; $i <= $voos.lenght; $i++) {
                if ($voos.aeroporto_origem == $voo.aeroporto_origem && $voos.aeroporto_destino == $voo.aeroporto_destino) {
                    continue;
                } else {
                    foreach ($voos as $voo) {
                        $conexoes[] = new VoocomCone
    }
    public function excluirVoo(Voo $voo1): void{
      for ( $i = 0; $i < count( $this->voos ); $i++ ) {
            if ( $this->voos[$i]->getSigla() == $voo1->getSigla() ) {
                array_splice( $this->voos, $i, 1 );
            }
        }
    }
  }
?>