<?php
    include_once 'suite.php';
    include_once '../classes/passageiro.php';
    require_once '../classes/passagem.php';
    class PassageiroTestCase extends TestCase {

        protected function getName(): string {
                return "Passageiro";
        }

        public function run() {
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
            $documentoCliente = new DocumentoPassageiro($passaporte);

            try {
                // nome vazio
                new Passageiro('', $validSobrenome, $documentoCliente, $validNacionalidade, $validCPF, $validData, $validEmail);
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                // sobrenome vazio
                new Passageiro($validNome, '', $documentoCliente, $validNacionalidade, $validCPF, $validData, $validEmail);
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                // há um cpf, mas a pessoa não é brasileira
                new Passageiro($validNome, $validSobrenome, $documentoCliente, $invalidNacionalidade, $validCPF, $validData, $validEmail);
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                // a pessoa é brasileira, e há um cpf
                new Passageiro($validNome, $validSobrenome, $documentoCliente, $validNacionalidade, $validCPF, $validData, $validEmail);
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }
        }
    }

?>