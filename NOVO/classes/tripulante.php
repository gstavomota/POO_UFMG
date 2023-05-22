<?php
require_once('identificadores.php');
require_once('nacionalidades.php');
require_once('companhia_area.php');
require_once('aeroporto.php');
require_once('cargo.php');

class Tripulante
{
    private string $nome;
    private string $sobrenome;
    private CPF $cpf;
    private Nacionalidade $nacionalidade;
    private DataTempo $data_de_nascimento;
    private Email $email;
    private string $cht; // esse documento ainda não possui um identificador
    private Endereco $endereco;
    private CompanhiaAerea $companhia;
    private SiglaAeroporto $aeroporto_base;
    private Cargo $cargo;
    private string $registro;


    public function __construct(string         $nome,
                                string         $sobrenome,
                                CPF            $cpf,
                                Nacionalidade  $nacionalidade,
                                DataTempo      $data_de_nascimento,
                                Email          $email,
                                string         $cht,
                                Endereco       $endereco,
                                CompanhiaAerea $companhia,
                                SiglaAeroporto $aeroporto_base,
                                Cargo          $cargo,
                                string         $registro)
    {
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->cpf = $cpf;
        $this->nacionalidade = $nacionalidade;
        $this->data_de_nascimento = $data_de_nascimento;
        $this->email = $email;
        $this->cht = $cht;
        $this->endereco = $endereco;
        $this->companhia = $companhia;
        $this->aeroporto_base = $aeroporto_base;
        $this->cargo = $cargo;
        $this->registro = $registro;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getSobrenome(): string
    {
        return $this->sobrenome;
    }

    public function getCpf(): CPF
    {
        return $this->cpf;
    }

    public function getNacionalidade(): Nacionalidade
    {
        return $this->nacionalidade;
    }

    public function getDataDeNascimento(): DataTempo
    {
        return $this->data_de_nascimento;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getCht(): string
    {
        return $this->cht;
    }

    public function getEndereco(): Endereco
    {
        return $this->endereco;
    }

    public function getCompanhia(): CompanhiaAerea
    {
        return $this->companhia;
    }

    public function getAeroportoBase(): SiglaAeroporto
    {
        return $this->aeroporto_base;
    }


    public function getCargo(): Cargo
    {
        return $this->cargo;
    }


    public function getRegistro(): string
    {
        return $this->registro;
    }
}

    