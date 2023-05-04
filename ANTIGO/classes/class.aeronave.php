<?php
// Raissa

  include_once("class.voo.php");
  include_once("class.viagem.php");
  
  class Aeronave {
    private $fabricante;
    private $modelo;
    private $capacidade;
    private $capacidade_kg;
    private $registro;
    private $voos = [];  // voos devem ser adicionados com a chamada do método addVoos
    private $viagens = []; // viagens devem ser adicionados com a chamada do método addViagens

    static $local_filename = "aeronave.txt";

    public function __construct (string $p_fabricante, string $p_modelo, int $p_capacidade, float $p_capacidade_kg, string $p_registro) {
      $this->fabricante = $p_fabricante;
      $this->modelo = $p_modelo;
      $this->capacidade = $$p_capacidade;
      $this->capacidade_kg = $p_capacidade_kg;
      $this->voos = array();
      $this->viagens = array();
      $this->validaRegistro($p_registro); // o registro precisa ser validado
    }

    public function getFabricante(){
        return $this->fabricante;
    }

    public function getModelo(){
        return $this->modelo;
    }

    public function getCapacidade(){
        return $this->capacidade;
    }
    
    public function getCapacidade_kg(){
        return $this->capacidade_kg;
    }

    public function getRegistro() {
        return $this->registro;
    }

    public function getVoos() {
        return $this->voos;
    }

    public function getViagens(){
      return $this->viagens;
  }

    public function setFabricante(string $p_fabricante){
      $this->fabricante = $p_fabricante;
    }

    public function setModelo(string $p_modelo){
      $this->modelo = $p_modelo;
    }

    public function setCapacidade(int $p_capacidade){
      $this->capacidade = $$p_capacidade;
    }

    public function setCapacidade_kg(float $p_capacidade_kg){
      $this->capacidade_kg = $p_capacidade_kg;
    }

    public function setRegistro(string $p_registro){
      $this->validaRegistro($p_registro); 
    }
     
    public function addVoo(Voo $p_voo) { // Voo $p_voo
        array_push($this->voos, $p_voo);
    }

    public function addViagem (Viagem $p_viagem) { // Viagem $p_viagem
        array_push($this->viagens, $p_viagem);
    }

    public function validaRegistro(string $p_registro) {
    /* - Exemplo de uma sigla válida: PR-GUO */

    $tamanho = strlen($p_registro);
    $prefixo = substr($p_registro, 0, 2); 
    $prefixos_validos = array('PT','PR','PP','PS');
    $letras_finais = substr($p_registro, 3, 6);
    $letras_finais_validas = ctype_alpha($letras_finais);

      if ($tamanho == 6 && 
      (in_array($prefixo, $prefixos_validos))  && 
      $p_registro[2] == '-' &&
      $letras_finais_validas) {
        $this->registro = $p_registro;
        print_r('Registro salvo com sucesso.');
      } else {
        print_r('A sigla do registro não está de acordo com o padrão nacional. Certifique-se que ele se assemelha ao seguinte exemplo: PR-GUO.');
      }
    }

    public function listarAeronave() {
      echo "-- REGISTRO DA AERONAVE {$this->registro}: -- \n";
      echo "Fabricante: {$this->fabricante}\n";
      echo "Modelo: {$this->modelo}\n";
      echo "Capacidade de passageiros: {$this->capacidade}\n";
      echo "Capacidade em kilos: {$this->capacidade_kg}\n";
      echo "Fabricante: {$this->fabricante}\n";
    }
  }
?>