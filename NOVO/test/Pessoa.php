<?php

require_once 'suite.php';
require_once '../classes/Pessoa.php';

class PessoaBasica extends Pessoa {
    /**
     * @param string $nome
     * @param string $sobrenome
     * @param DocumentoPessoa $documento
     * @param Nacionalidade $nacionalidade
     * @param CPF|null $cpf
     * @param Data $data_de_nascimento
     * @param Email $email
     * @throws InvalidArgumentException se o PessoaBasica for invalido
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
        parent::__construct(
            $nome,
            $sobrenome,
            $documento,
            $nacionalidade,
            $cpf,
            $data_de_nascimento,
            $email,
        );
    }
}
class PessoaTestCase extends TestCase
{

    protected function getName(): string
    {
        return "Pessoa";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        $validNome = 'João';
        $validSobrenome = 'Silva';
        $validNacionalidade = Nacionalidade::BRASIL;
        $invalidNacionalidade = Nacionalidade::ARGENTINA;
        $validCPF = new CPF("239.111.040-57");
        $validEmail = new Email('joao@gmail.com');
        $validData = new Data(2023, 05, 01);
        $passaporte = new Passaporte('A00000000');
        $documentoCliente = new DocumentoPessoa($passaporte);

        try {
            // nome vazio
            new PessoaBasica('', $validSobrenome, $documentoCliente, $validNacionalidade, $validCPF, $validData, $validEmail);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            // sobrenome vazio
            new PessoaBasica($validNome, '', $documentoCliente, $validNacionalidade, $validCPF, $validData, $validEmail);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            // há um cpf, mas a pessoa não é brasileira
            new PessoaBasica($validNome, $validSobrenome, $documentoCliente, $invalidNacionalidade, $validCPF, $validData, $validEmail);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            // não há um cpf, mas a pessoa é brasileira
            new PessoaBasica($validNome, $validSobrenome, $documentoCliente, $validNacionalidade, null, $validData, $validEmail);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            // a pessoa é brasileira, e há um cpf
            new PessoaBasica($validNome, $validSobrenome, $documentoCliente, $validNacionalidade, $validCPF, $validData, $validEmail);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
    }
}

