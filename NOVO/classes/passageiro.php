<?php

require_once('identificadores.php');
require_once('nacionalidades.php');

class Passageiro
{

    public string $nome;
    public string $sobrenome;
    public DocumentoPassageiro $documento;
    public Nacionalidade $nacionalidade;
    public ?CPF $cpf;
    public Data $data_de_nascimento;
    public Email $email;
    public array $passagens;

    public function __construct(
        string              $nome,
        string              $sobrenome,
        DocumentoPassageiro $documento,
        Nacionalidade       $nacionalidade,
        ?CPF                $cpf,
        Data                $data_de_nascimento,
        Email               $email,
        array               $passagens,
    )
    {
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->documento = $documento;
        $this->nacionalidade = $nacionalidade;
        $this->cpf = Passageiro::valida_cpf_se_brasileiro($cpf, $nacionalidade);
        $this->data_de_nascimento = $data_de_nascimento;
        $this->email = $email;
        $this->passagens = $passagens;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @return string
     */
    public function getSobrenome(): string
    {
        return $this->sobrenome;
    }

    /**
     * @return DocumentoPassageiro
     */
    public function getDocumento(): DocumentoPassageiro
    {
        return $this->documento;
    }

    /**
     * @return Nacionalidade
     */
    public function getNacionalidade(): Nacionalidade
    {
        return $this->nacionalidade;
    }

    /**
     * @return CPF|null
     */
    public function getCpf(): ?CPF
    {
        return $this->cpf;
    }

    /**
     * @return Data
     */
    public function getDataDeNascimento(): Data
    {
        return $this->data_de_nascimento;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getPassagens(): array
    {
        return $this->passagens;
    }

    private static function valida_cpf_se_brasileiro(?CPF $cpf, Nacionalidade $nacionalidade): ?CPF
    {
        if ($nacionalidade == Nacionalidade::BRASIL && $cpf === null) {
            throw new InvalidArgumentException('O CPF deve ser definido para brasileiros');
        }
        if ($nacionalidade != Nacionalidade::BRASIL && $cpf !== null) {
            throw new InvalidArgumentException('O CPF deve ser definido somente para brasileiros');
        }
        return $cpf;
    }

}
