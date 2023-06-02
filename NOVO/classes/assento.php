<?php
require_once 'franquia_de_bagagem.php';
require_once 'identificadores.php';

class PreenchimentoDeAssentoException extends Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class Assento    
{
    private CodigoDoAssento $codigo;
    private ?RegistroDePassagem $passagem;
    private ?FranquiasDeBagagem $franquias;

    public function __construct(CodigoDoAssento $codigo)
    {
        $this->codigo = $codigo;
        $this->passagem = null;
        $this->franquias = null;
    }

    public function classe(): Classe
    {
        return $this->codigo->getClasse();
    }

    public function preenchido(): bool
    {
        return $this->passagem !== null || $this->franquias !== null;
    }

    public function vazio(): bool
    {
        return !$this->preenchido();
    }

    /** Tenta liberar o assento e retorna uma array contendo o registro da passagem e as franquias
     * @return array
     * @throws PreenchimentoDeAssentoException se o assento não está preenchido
     */
    public function liberar(): array
    {
        if (!$this->preenchido()) {
            throw new PreenchimentoDeAssentoException("O assento não está preenchido");
        }
        $passagem = $this->passagem;
        $this->passagem = null;
        $franquias = $this->franquias;
        $this->franquias = null;
        return [$passagem, $franquias];
    }

    /** Tenta reservar o assento
     * @throws PreenchimentoDeAssentoException se o assento está preenchido
     */
    public function reservar(RegistroDePassagem $passagem, FranquiasDeBagagem $franquias): void
    {
        if ($this->preenchido()) {
            throw new PreenchimentoDeAssentoException("O assento está preenchido");
        }
        $this->passagem = $passagem;
        $this->franquias = $franquias;
    }

    public function getCodigo(): CodigoDoAssento
    {
        return $this->codigo;
    }

    public function getPassagem(): ?RegistroDePassagem
    {
        return $this->passagem;
    }

    public function getFranquias(): ?FranquiasDeBagagem
    {
        return $this->franquias;
    }
}