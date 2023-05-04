// Raissa
<?php
  include_once("class.voo.php");
  include_once("class.viagem.php");

  class Aeronave {
    private string $fabricante;
    private string $modelo;
    private int $capacidade;
    private float $capacidade_kg;
    private string $registro;
    private $voos = array();  // voos devem ser adicionados com a chamada do método addVoos
    private $viagens = array(); // viagens devem ser adicionados com a chamada do método addViagens

    public function __construct (string $p_fabricante, string $p_modelo, int $p_capacidade, float $p_capacidade_kg) {
      $this->fabricante = $p_fabricante;
      $this->modelo = $p_modelo;
      $this->capacidade = $$p_capacidade;
      $this->capacidade_kg = $p_capacidade_kg;
      // o registro não está aqui no construtor, pois acredito que ele precisa ser validado antes de ser salvo.
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

    public function getRegistro(){
        return $this->registro;
    }

    public function getVoos(){
      // maria eduarda: coloquei essa pq precisei na minha classe
        return $this->voos;
    }
    
    public function addVoos(voo $p_voo) {
        array_push($voos, $p_voo);
    }

    public function addViagens (viagem $p_viagem) {
        array_push($viagens, $p_viagem);
    }

    public function validaRegistro(string $p_registro) {
    
    /*
      Registro da aeronave:
      - Composto pelo prefixo, que contém duas letras
      - Um hífen
      - Seguido de três letras
      - (Ex.: PR-GUO
      - No Brasil, somente são permitidos para voos comerciais os prefixos PT, PR, PP, PS, que devem ser validados
    */
    
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
  }
?>