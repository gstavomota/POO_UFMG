<?php

require_once "log.php";
abstract class Pessoa
{
    private string $nome;
    private string $sobrenome;
    private DocumentoPessoa $documento;
    private Nacionalidade $nacionalidade;
    private ?CPF $cpf;
    private Data $data_de_nascimento;
    private Email $email;
    /**
     * @param string $nome
     * @param string $sobrenome
     * @param DocumentoPessoa $documento
     * @param Nacionalidade $nacionalidade
     * @param CPF|null $cpf
     * @param Data $data_de_nascimento
     * @param Email $email
     * @throws InvalidArgumentException se a pessoa for invalida
     */
    public function __construct(
        string          $nome,
        string          $sobrenome,
        DocumentoPessoa $documento,
        Nacionalidade   $nacionalidade,
        ?CPF            $cpf,
        Data            $data_de_nascimento,
        Email           $email,
    )
    {
        $this->nome = static::validaNome($nome);
        $this->sobrenome = static::validaSobrenome($sobrenome);
        $this->documento = $documento;
        $this->nacionalidade = $nacionalidade;
        $this->cpf = static::validaCpfSeBrasileiro($cpf, $nacionalidade);
        $this->data_de_nascimento = $data_de_nascimento;
        $this->email = $email;
    }

    /** Valida um nome
     * @param string $nome
     * @return string
     */
    private static function validaNome(string $nome): string
    {
        if (empty($nome)) {
            throw new InvalidArgumentException("O nome não pode ser vazio");
        }
        return $nome;
    }

    /** Valida um sobrenome
     * @param string $sobrenome
     * @return string
     */
    private static function validaSobrenome(string $sobrenome): string
    {
        if (empty($sobrenome)) {
            throw new InvalidArgumentException("O sobrenome não pode ser vazio");
        }
        return $sobrenome;
    }


    /** Valida um cpf
     * @param CPF|null $cpf
     * @param Nacionalidade $nacionalidade
     * @return CPF|null
     */
    private static function validaCpfSeBrasileiro(?CPF $cpf, Nacionalidade $nacionalidade): ?CPF
    {
        if ($nacionalidade == Nacionalidade::BRASIL && $cpf === null) {
            throw new InvalidArgumentException('O CPF deve ser definido para brasileiros');
        }
        if ($nacionalidade != Nacionalidade::BRASIL && $cpf !== null) {
            throw new InvalidArgumentException('O CPF deve ser definido somente para brasileiros');
        }
        return $cpf;
    }

    /** Retorna o nome
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /** Retorna o sobrenome
     * @return string
     */
    public function getSobrenome(): string
    {
        return $this->sobrenome;
    }

    /** Retorna o documento
     * @return DocumentoPessoa
     */
    public function getDocumento(): DocumentoPessoa
    {
        return $this->documento;
    }

    /** Retorna a nacionalidade
     * @return Nacionalidade
     */
    public function getNacionalidade(): Nacionalidade
    {
        return $this->nacionalidade;
    }

    /** Retorna o cpf
     * @return CPF|null
     */
    public function getCpf(): ?CPF
    {
        return $this->cpf;
    }

    /** Retorna a data de nascimento
     * @return Data
     */
    public function getDataDeNascimento(): Data
    {
        return $this->data_de_nascimento;
    }

    /** Retorna o email
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

}