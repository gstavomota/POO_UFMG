<?php

require_once('identificadores.php');
require_once('nacionalidades.php');

namespace MyApp;

use DateTime;
use DocumentoPassageiro;
use CPF;
use Email;
use Nacionalidade;

class Passageiro{

    public string $nome;
    public string $sobrenome;
    public DocumentoPassageiro $documento;
    public string $nacionalidade;
    public ?CPF $cpf;
    public DateTime $data_de_nascimento;
    public Email $email;
    public array $passagens;

    public function __construct(
        string $nome,
        string $sobrenome,
        DocumentoPassageiro $documento,
        string $nacionalidade,
        ?CPF $cpf,
        DateTime $data_de_nascimento,
        Email $email,
        array $passagens,
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

    public static function valida_cpf_se_brasileiro($cpf, $nacionalidade) {
        if ($nacionalidade == Nacionalidade::BRASIL && $cpf === null) {
            throw new \Exception('O CPF deve ser definido para brasileiros');
        }
        if ($nacionalidade != Nacionalidade::BRASIL && $cpf !== null) {
            throw new \Exception('O CPF deve ser definido somente para brasileiros');
        }
        return $cpf;
    }

}
