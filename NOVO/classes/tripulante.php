<?php
require_once 'identificadores.php';
require_once 'nacionalidades.php';
require_once 'aeroporto.php';
require_once 'cargo.php';

class Tripulante extends Pessoa
{
    private string $cht; // esse documento ainda não possui um identificador
    private Endereco $endereco;
    private SiglaCompanhiaAerea $companhia;
    private SiglaAeroporto $aeroporto_base;
    private Cargo $cargo;
    private RegistroDeTripulante $registro;


    public function __construct(
        // Pessoa
        string               $nome,
        string               $sobrenome,
        DocumentoPessoa      $documento,
        Nacionalidade        $nacionalidade,
        ?CPF                 $cpf,
        Data                 $data_de_nascimento,
        Email                $email,
        // Tripulante
        string               $cht,
        Endereco             $endereco,
        SiglaCompanhiaAerea  $companhia,
        SiglaAeroporto       $aeroporto_base,
        Cargo                $cargo,
        RegistroDeTripulante $registro)
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
        $this->cht = $cht;
        $this->endereco = $endereco;
        $this->companhia = $companhia;
        $this->aeroporto_base = $aeroporto_base;
        $this->cargo = $cargo;
        $this->registro = $registro;
    }

    public function getCht(): string
    {
        return $this->cht;
    }

    public function getEndereco(): Endereco
    {
        return $this->endereco;
    }

    public function getCompanhia(): SiglaCompanhiaAerea
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


    public function getRegistro(): RegistroDeTripulante
    {
        return $this->registro;
    }
}

    