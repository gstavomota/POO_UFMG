<?php
require_once 'franquia_de_bagagem.php';
require_once 'identificadores.php';
require_once "log.php";

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
        return log::getInstance()->logRead($this->codigo->getClasse());
    }

    public function preenchido(): bool
    {
        return log::getInstance()->logCall($this->passagem !== null || $this->franquias !== null);
    }

    public function vazio(): bool
    {
        return log::getInstance()->logCall(!$this->preenchido());
    }

    /** Tenta liberar o assento e retorna uma array contendo o registro da passagem e as franquias
     * @return array
     * @throws PreenchimentoDeAssentoException se o assento não está preenchido
     */
    public function liberar(): array
    {
        if (!$this->preenchido()) {
            log::getInstance()->logThrow(new PreenchimentoDeAssentoException("O assento não está preenchido"));
        }
        $passagem = $this->passagem;
        $this->passagem = null;
        $franquias = $this->franquias;
        $this->franquias = null;
        return log::getInstance()->logCall([$passagem, $franquias]);
    }

    /** Tenta reservar o assento
     * @throws PreenchimentoDeAssentoException se o assento está preenchido
     */
    public function reservar(RegistroDePassagem $passagem, FranquiasDeBagagem $franquias): void
    {
        if ($this->preenchido()) {
            log::getInstance()->logThrow(new PreenchimentoDeAssentoException("O assento está preenchido"));
        }
        $this->passagem = $passagem;
        $this->franquias = $franquias;
        log::getInstance()->logCall(null);
    }

    public function getCodigo(): CodigoDoAssento
    {
        return log::getInstance()->logRead($this->codigo);
    }

    public function getPassagem(): ?RegistroDePassagem
    {
        return log::getInstance()->logRead($this->passagem);
    }

    public function getFranquias(): ?FranquiasDeBagagem
    {
        return log::getInstance()->logRead($this->franquias);
    }
}