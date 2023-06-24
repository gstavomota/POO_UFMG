<?php
require_once("../classes/passageiroVip.php");
require_once "suite.php";

class PassageiroVipTestCase extends TestCase{
    protected function getName(): string
    {
        return "PassageiroVip";
    }
    public function run(){
        # Constructor
        $validNome = 'Raphael';
        $validSobrenome = 'Amaral';
        $validNacionalidade = Nacionalidade::BRASIL;
        $invalidNacionalidade = Nacionalidade::ALEMANHA;
        $validCPF = new CPF("111.111.111-11");
        $validEmail = new Email('raphael@gmail.com');
        $validDataNascimento = new Data(2021, 07, 06);
        $passaporte = new Passaporte('A00000000');
        $documentoPessoa = new DocumentoPessoa($passaporte);
        $numero_de_registro = '12345';
        $categorias = [
            new Categoria('branca', 0),
            new Categoria('prata', 50),
            new Categoria('ouro', 100)
        ];
        $dataTempoParaTeste = null;
        $programaDeMilhagem = new ProgramaDeMilhagem($categorias, "Programa Teste");
        $passageiroTeste = new PassageiroVip($validNome, $validSobrenome, $documentoPessoa, $validNacionalidade, $validCPF,$validDataNascimento, $validEmail, $numero_de_registro, $programaDeMilhagem);
        # Add pontos
        $this->startSection("addPontos");
        $anoRetrasado = DataTempo::agora()->sub(new Duracao(365*2, 0));
        $agora = DataTempo::agora();
        $passageiroTeste->addPontos(5, $anoRetrasado);
        $passageiroTeste->addPontos(10, $agora);

        $pontuacao = $this->getNonPublicProperty($passageiroTeste,"pontuacao");
        $this->checkEq(count($pontuacao), 2);

        $ponto5 = $pontuacao[0];
        $this->checkEq($ponto5->getPontosGanhos(), 5);
        $this->checkEq($ponto5->getDataDeObtencao(), $anoRetrasado);

        $ponto10 = $pontuacao[1];
        $this->checkEq($ponto10->getPontosGanhos(), 10);
        $this->checkEq($ponto10->getDataDeObtencao(), $agora);

        # getPontosValidos
        $this->startSection("getPontosValidos");
        $this->checkEq($passageiroTeste->getPontosValidos(), 10);
        $passageiroTeste->addPontos(10);
        $this->checkEq($passageiroTeste->getPontosValidos(), 20);

        # alterarCategoria
        $categoria = $categorias[1];
        $categoria1 = $categorias[2];
        $categoriaInvalida = new Categoria("invalida", 10);
        $this->startSection("alterarCategoria");
        try {
            $passageiroTeste->alterarCategoria($categoriaInvalida, $agora);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $passageiroTeste->alterarCategoria($categoria, $agora);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $passageiroTeste->alterarCategoria($categoria1, $agora);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            $passageiroTeste->alterarCategoria($categoria, $agora);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        $passageiroTeste->addPontos(30);
        try {
            $passageiroTeste->alterarCategoria($categoria, $agora);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            $passageiroTeste->alterarCategoria($categoria1, $agora);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        $passageiroTeste->addPontos(50);
        try {
            $passageiroTeste->alterarCategoria($categoria1, $agora);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }

        $categoriasDoPassageiro = $passageiroTeste->getCategoriaDoPrograma();
        $this->checkEq(count($categoriasDoPassageiro), 2);
        $this->checkEq($categoriasDoPassageiro[0]->getCategoria(), $categoria);
        $this->checkEq($categoriasDoPassageiro[0]->getDataDeEntrada(), $agora);

        $this->checkEq($categoriasDoPassageiro[1]->getCategoria(), $categoria1);
        $this->checkEq($categoriasDoPassageiro[1]->getDataDeEntrada(), $agora);
    }
}
?>