<?php
require_once "Equatable.php";
class Usuario implements Equatable
{
    private string $usuario;
    private string $senha;
    private string $email;
    public function __construct(
        string $usuario,
        string $senha,
        string $email,
    ) {
        $this->usuario = $usuario;
        $this->senha = $senha;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsuario(): string
    {
        return $this->usuario;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSenha(): string
    {
        return $this->senha;
    }

    public function eq(Equatable $other): bool
    {
        if (!$other instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->usuario == $other->usuario && $this->email == $other->email && $this->senha == $other->senha;
    }
}