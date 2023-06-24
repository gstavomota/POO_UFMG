<?php
require_once "Usuario.php";
require_once "identificadores.php";
require_once "Equatable.php";
class Sessao implements Equatable
{
    private int $id;
    private Usuario $usuario;
    public function __construct(int $id, Usuario $usuario) {
        $this->id = $id;
        $this->usuario = $usuario;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Usuario
     */
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function eq(Equatable $other): bool
    {
        if (!$other instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->id == $other->id && $this->usuario->eq($other->usuario);
    }
}

class GeradorDeSessao extends GeradorDeRegistroNumerico {
    public function __construct(int $ultimo_id = null)
    {
        parent::__construct($ultimo_id);
    }
    public function gerar(Usuario $usuario): Sessao {
        return new Sessao($this->gerarNumero(), $usuario);
    }
}