<?php
    include_once "suite.php";
    include_once "../classes/ProgramaDeMilhagem.php";

    class ProgramaDeMilhagemTestCase extends TestCase {
        protected function getName(): string {
            return "ProgramaDeMilhagem";
        }

        public function run() {
            $cpf = new CPF('123.456.789-09');
            $nacionalidade = Nacionalidade::BRASIL;
            $documentoPassageiro = new DocumentoPassageiro(null, null);
            $data = Data::fromString("11/03/2003");
            $email = new Email("mariaeduardamrs0@gmail.com");
            $passagens = [];
            $pontuacaoVazia = [];
            $pontuacaoPreenchida = [
                new Pontos(2000, DataTempo::fromDateTime(DateTime::createFromFormat("d/m/Y", "01/01/2020"))),
                new Pontos(2000, DataTempo::agora()),
                new Pontos(1000, DataTempo::agora()),
                new Pontos(750, DataTempo::agora())
            ];
            $pontuacaoVencida = [
                new Pontos(2000, DataTempo::fromDateTime(DateTime::createFromFormat("d/m/Y", "01/01/2019"))),
                new Pontos(2000, DataTempo::fromDateTime(DateTime::createFromFormat("d/m/Y", "02/03/2020"))),
                new Pontos(1000, DataTempo::fromDateTime(DateTime::createFromFormat("d/m/Y", "04/05/2021"))),
                new Pontos(750, DataTempo::fromDateTime(DateTime::createFromFormat("d/m/Y", "06/01/2022")))
            ];
            $categorias = [
                new Categoria('bronze', 1000),
                new Categoria('prata', 2000),
                new Categoria('ouro', 3000),
                new Categoria('diamante', 5000)
            ];
            $numeroDeRegistro = '12345';
            
            $programaDeMilhagem = new ProgramaDeMilhagem($categorias, "Programa Teste");
            
            #Método update
            $this->startSection("Método update");
            
            $passageiroSemPontos = new PassageiroVip('Maria', 'Sampaio', $documentoPassageiro, $nacionalidade, $cpf, $data , $email,  $passagens, 
            $pontuacaoVazia, $numeroDeRegistro, $categorias, $programaDeMilhagem);
            $this->checkEq($programaDeMilhagem->update($passageiroSemPontos), null);

            $passageiroComPontosVencidos = new PassageiroVip('Maria', 'Sampaio', $documentoPassageiro, $nacionalidade, $cpf, $data , $email,  $passagens, 
            $pontuacaoVencida, $numeroDeRegistro, $categorias, $programaDeMilhagem);
            $this->checkEq($programaDeMilhagem->update($passageiroComPontosVencidos), null);

            $passageiroComPontos = new PassageiroVip('Maria', 'Sampaio', $documentoPassageiro, $nacionalidade, $cpf, $data , $email,  $passagens, 
            $pontuacaoPreenchida, $numeroDeRegistro, $categorias, $programaDeMilhagem);
            $this->checkEq($programaDeMilhagem->update($passageiroComPontos), $categorias[2]);

            $passageiroComPontos->addPontos(4000);
            $this->checkEq($programaDeMilhagem->update($passageiroComPontos), $categorias[3]);

        }
    }
?>