<?php
require_once 'franquia_de_bagagem.php';
require_once 'identificadores.php';

class Assento {
    public $codigo;
    public $passagem;
    public $franquias;

    public function __construct(CodigoDoAssento $codigo) {
        $this->codigo = $codigo;
        $this->passagem = null;
        $this->franquias = null;
    }

    public function classe() {
        return $this->codigo->classe;
    }

    public function preenchido() {
        return $this->passagem !== null || $this->franquias !== null;
    }

    public function vazio() {
        return !$this->preenchido();
    }

    public function liberar() {
        if (!$this->preenchido()) {
            throw new Exception("O assento não está preenchido");
        }
        $passagem = $this->passagem;
        $this->passagem = null;
        $franquias = $this->franquias;
        $this->franquias = null;
        return [$passagem, $franquias];
    }

    public function reservar(RegistroDePassagem $passagem, FranquiasDeBagagem $franquias) {
        if ($this->preenchido()) {
            throw new Exception("O assento está preenchido");
        }
        $this->passagem = $passagem;
        $this->franquias = $franquias;
    }
}