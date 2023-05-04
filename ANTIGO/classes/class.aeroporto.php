<?php
// Bruno
include_once( "class.companhia.php" );
include_once( "persist.php" );
include_once( "class.voo.php" );

class Aeroporto {
    private string $sigla;
    private string $cidade;
    private string $estado;
    private $companhias = array();
    private $voos = array();
    private $aeroportos = array();
    static protected $local_filename = "aeroportoTst.txt";

    public function __construct( string $p_cidade, string $p_estado, string $p_sigla ) {
        $this->cidade = $p_cidade;
        $this->estado = $p_estado;
        $this->validaSigla( $p_sigla );
      /*
        $this->voos = array(); // preciso de um array de voos de um aeroporto
        $this->aeroportos = array();
        como arrays são inicializados vazios eles não precisam estar no construtor
      */
    }

    static public function getFilename() {
        return static::$local_filename;
    }

    public function setVoos( array $p_voos ) {
      $this->voos = $p_voos;
    }
  
    public function getVoos() {
      return $this->voos; // método usado em voocomconexao
    }

    public function getSigla() {
        return $this->sigla;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function addCompanhia( Companhia $p_companhia ) {
        array_push( $this->companhias, $p_companhia );
    }

    public function getCompanhias() {
        return $this->companhias;
    }

    public function validaSigla( string $p_sigla ) {
        if ( $p_sigla === null ) {
            throw new Exception("A sigla do aeroporto não pode ser nula.");
        }

        $tamanho = strlen( $p_sigla );
        $caracteres = substr( $p_sigla, 0, 3 );
        $caracteres_validos = ctype_alpha( $caracteres );

        if ( $tamanho == 3 && $caracteres_validos ) {
            $this->sigla = $p_sigla;
        } else {
            throw new Exception( "A sigla do aeroporto não está em seu padrão correto. Certifique-se de que ela possua apenas três caracteres alfabéticos." );
        }
    }

    public function cadastrarAeroporto ( Aeroporto $p_aeroporto ) {
      array_push( $this->aeroportos, $p_aeroporto );
    }

    public function excluirAeroporto( Aeroporto $p_aeroporto ) {
        for ( $i = 0; $i < count( $this->aeroportos ); $i++ ) {
            if ( $this->aeroportos[$i]->getSigla() == $p_aeroporto->getSigla() ) {
                array_splice( $this->aeroportos, $i, 1 );
            }
        }
    }

    public function alterarAeroporto( Aeroporto $p_aeroporto1, Aeroporto $p_aeroporto2 ) {
        for ( $i = 0; $i < count( $this->aeroportos ); $i++ ) {
            if ( $this->aeroportos[$i]->getSigla() == $p_aeroporto1->getSigla() ) {
                $this->aeroportos[$i] = $p_aeroporto2;
            }
        }
    }

    public function listarAeroporto () {
      foreach($this->aeroportos as $p_aeroporto) {
        echo "Sigla: " . $p_aeroporto->getSigla() . ", Cidade: " . $p_aeroporto->getCidade() . ", Estado: " . $p_aeroporto->getEstado() . "\n";
      }
    }
}
?>

