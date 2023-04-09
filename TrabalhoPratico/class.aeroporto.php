//Bruno
<?php
  include_once( "class.companhia.php" );

  class Aeroporto {
    private string $sigla;
    private string $cidade;
    private string $estado;
    private $companhias = array();

    public function __construct( string $p_cidade, string $p_estado, string $p_sigla ) {
        $this->cidade = $p_cidade;
        $this->estado = $p_estado;
        $this->validaSigla($p_sigla);
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

    public function validaSigla( string $p_sigla ) {
        if (is_null( $p_sigla )) {
            throw new Exception("A sigla do aeroporto não pode ser nula.");
        }

        $tamanho = strlen( $p_sigla );
        $caracteres = substr( $p_sigla, 0, 3 );
        $caracteres_validos = ctype_alpha( $caracteres );

        if ($tamanho == 3 && $caracteres_validos) {
            $this->sigla = $p_sigla;
        } else {
            throw new Exception("A sigla do aeroporto não está em seu padrão correto. Certifique-se de que ela possua apenas três caracteres alfabéticos.");
        }
    }
}
?>
