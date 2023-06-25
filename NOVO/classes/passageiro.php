<?php

require_once 'identificadores.php';
require_once 'nacionalidades.php';
require_once 'Pessoa.php';
require_once "log.php";

class Passageiro extends Pessoa
{


    /**
     * @var RegistroDePassagem[]
     */
    private array $passagens;

    /**
     * @param string $nome
     * @param string $sobrenome
     * @param DocumentoPessoa $documento
     * @param Nacionalidade $nacionalidade
     * @param CPF|null $cpf
     * @param Data $data_de_nascimento
     * @param Email $email
     * @throws InvalidArgumentException se o passageiro for invalido
     */
    public function __construct(
        // Pessoa
        string          $nome,
        string          $sobrenome,
        DocumentoPessoa $documento,
        Nacionalidade   $nacionalidade,
        ?CPF            $cpf,
        Data            $data_de_nascimento,
        Email           $email,
    )
    {
        parent::__construct(
            $nome,
            $sobrenome,
            $documento,
            $nacionalidade,
            $cpf,
            $data_de_nascimento,
            $email,
        );
        $this->passagens = [];
    }

    /** Retorna as passagens
     * @return RegistroDePassagem[]
     */
    public function getPassagens(): array
    {
        return log::getInstance()->logRead($this->passagens);
    }

    /** Adiciona uma passagem
     * @param RegistroDePassagem $registroDePassagem
     * @return void
     */
    public function addPassagem(RegistroDePassagem $registroDePassagem)
    {
        $pre = clone $this;
        $this->passagens[] = $registroDePassagem;
        log::getInstance()->logWrite($pre, $this);
    }

}
