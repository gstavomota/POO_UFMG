<?php
require_once "../classes/endereco.php";
require_once "../classes/identificadores.php";

class EnderecoTestCase extends TestCase {

    protected function getName(): string
    {
        return "Endereco";
    }

    public function run()
    {
        # Values
        $logradouro = "Avenida Amazonas";
        $logradouro_2 = "Avenida Francisco Sá";
        $numero = 1;
        $bairro = "Gutierrez";
        $cep = new CEP("30150-312");
        $cidade = "Belo Horizonte";
        $estado = Estado::MG;
        $referencia = "Proximo a Avenida Silva Lobo";
        # Constructor
        $this->startSection("Constructor");
        try {
            new Endereco("", $numero, $bairro, $cep, $cidade, $estado, $referencia);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Endereco($logradouro, -1, $bairro, $cep, $cidade, $estado, $referencia);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Endereco($logradouro, $numero, "", $cep, $cidade, $estado, $referencia);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Endereco($logradouro, $numero, $bairro, $cep, "", $estado, $referencia);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Endereco($logradouro, $numero, $bairro, $cep, $cidade, $estado, "");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Endereco($logradouro, $numero, $bairro, $cep, $cidade, $estado, null);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new Endereco($logradouro, $numero, $bairro, $cep, $cidade, $estado, $referencia);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Equality
        $endereco = new Endereco($logradouro, $numero, $bairro, $cep, $cidade, $estado, $referencia);
        $endereco_2 = new Endereco($logradouro, $numero, $bairro, $cep, $cidade, $estado, $referencia);
        $outro_endereco = new Endereco($logradouro_2, $numero, $bairro, $cep, $cidade, $estado, $referencia);
        $this->startSection("Equality");
        $this->checkEq($endereco, $endereco_2);
        $this->checkNeq($endereco, $outro_endereco);
    }
}
