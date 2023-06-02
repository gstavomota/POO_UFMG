<?php
include_once("passageiroVip.php");
include_once "suite.php";

class PassageiroVipTestCase extends TestCase{
    protected function getName(): string
    {
        return "PassageiroVip";
    }
    public function run(){
        # Constructor
        $this->startSection("Constructor");
        $validNome = 'Raphael';
        $validSobrenome = 'Amaral';
        $validNacionalidade = Nacionalidade::BRASIL;
        $invalidNacionalidade = Nacionalidade::ALEMANHA;
        $validCPF = new CPF("123.456.789-99");
        $validEmail = new Email('raphael@gmail.com');
        $validDataNascimento = new Data(2021, 07, 06);
        $passaporte = new Passaporte('A00000000');
        $documentoPassageiro = new DocumentoPassageiro($passaporte);
        $validPassagens = array(0);
        $pontuacaoVazia = [];
        $numero_de_registro = '12345';
        $categorias = [];
        $dataTempoParaTeste = null;
        $programaDeMilhagem = new ProgramaDeMilhagem($categorias, "Programa Teste");

        $this->startSection("metodos");
        #método addPontos
        $passageiroTeste = new PassageiroVip($validNome, $validSobrenome, $documentoPassageiro, $validNacionalidade, $validCPF, 
        $validDataNascimento, $validEmail, $validPassagens, $pontuacaoVazia, $numero_de_registro, $categorias, $programaDeMilhagem);
        $pontos = 10;
        $passageiroTeste->addPontos($pontos);

        try {
            $this->checkEq($passageiroTeste->getPontuacao(), $pontos, true);
        } catch (InvalidArgumentException $e) {
            echo 'Método addPontos não funciona';
        }

        #método alterarCategoria
        try {
            $categoria1 = new Categoria('ouro', 100);
            $categorias = $categoria1;
            $categoria2 = new Categoria('prata', 50);
            $passageiroTeste->alterarCategoria($categoria2, $dataTempoParaTeste);
            $this->checkEq($categorias, $categoria2, true);
        } catch (InvalidArgumentException $e) {
            echo 'Método alterarCategoria não funciona';
        }
    }
}
?>