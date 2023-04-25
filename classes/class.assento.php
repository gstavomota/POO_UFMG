<?php
// Raissa

class Assento {
    private $numero;
    private $classe; // lembrar de usar o enum Classe quando se instanciar esse atributo.
}

public function getNumero () {
  return $this->numero;
}

public function getClasse () {
  return $this->classe;
}
