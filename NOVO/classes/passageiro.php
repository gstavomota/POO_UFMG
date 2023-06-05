<?php

require_once 'identificadores.php';
require_once 'nacionalidades.php';

class Passageiro
{

    private string $nome;
    private string $sobrenome;
    private DocumentoPassageiro $documento;
    private Nacionalidade $nacionalidade;
    private ?CPF $cpf;
    private Data $data_de_nascimento;
    private Email $email;
    /**
     * @var RegistroDePassagem[]
     */
    private array $passagens;

    /**
     * @param string $nome
     * @param string $sobrenome
     * @param DocumentoPassageiro $documento
     * @param Nacionalidade $nacionalidade
     * @param CPF|null $cpf
     * @param Data $data_de_nascimento
     * @param Email $email
     * @throws InvalidArgumentException se o passageiro for invalido
     */
    public function __construct(
        string              $nome,
        string              $sobrenome,
        DocumentoPassageiro $documento,
        Nacionalidade       $nacionalidade,
        ?CPF                $cpf,
        Data                $data_de_nascimento,
        Email               $email,
    )
    {
        $this->nome = Passageiro::valida_nome($nome);
        $this->sobrenome = Passageiro::valida_sobrenome($sobrenome);
        $this->documento = $documento;
        $this->nacionalidade = $nacionalidade;
        $this->cpf = Passageiro::valida_cpf_se_brasileiro($cpf, $nacionalidade);
        $this->data_de_nascimento = $data_de_nascimento;
        $this->email = $email;
        $this->passagens = [];
    }

    /** Valida um nome
     * @param string $nome
     * @return string
     */
    private static function valida_nome(string $nome) {
        if (empty($nome)) {
            throw new InvalidArgumentException("O nome não pode ser vazio");
        }
        return $nome;
    }

    /** Valida um sobrenome
     * @param string $sobrenome
     * @return string
     */
    private static function valida_sobrenome(string $sobrenome) {
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
     * @return DocumentoPassageiro
     */
    public function getDocumento(): DocumentoPassageiro
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

    /** Retorna as passagens
     * @return RegistroDePassagem[]
     */
    public function getPassagens(): array
    {
        return $this->passagens;
    }

    /** Adiciona uma passagem
     * @param RegistroDePassagem $registroDePassagem
     * @return void
     */
    public function addPassagem(RegistroDePassagem $registroDePassagem) {
        $this->passagens[] = $registroDePassagem;
    }

}
