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
            $rg = new RG("MG"."11.111.111");
            $documentoPessoa = new DocumentoPessoa(null, $rg);
            $data = Data::fromString("11/03/2003");
            $email = new Email("mariaeduardamrs0@gmail.com");
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
                new Categoria('branco', 0),
                new Categoria('bronze', 1000),
                new Categoria('prata', 2000),
                new Categoria('ouro', 3000),
                new Categoria('diamante', 5000)
            ];
            $categoriaBase = $categorias[0];
            $numeroDeRegistro = '12345';
            # Construtor
            $this->startSection("Construtor");
            try {
                $categoriasSemBase = [$categorias[1], $categorias[2]];
                new ProgramaDeMilhagem($categoriasSemBase, "Programa Teste");
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                new ProgramaDeMilhagem([], "Programa Teste");
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                $categoriasForaDeOrdem = [$categorias[0], $categorias[2], $categorias[1]];
                new ProgramaDeMilhagem($categoriasForaDeOrdem, "Programa Teste");
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                new ProgramaDeMilhagem($categorias, "");
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
            try {
                new ProgramaDeMilhagem($categorias, "Programa Teste");
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }

            # Update
            $programaDeMilhagem = new ProgramaDeMilhagem($categorias, "Programa Teste");
            $this->startSection("update");
            
            $passageiroSemPontos = new PassageiroVip('Maria', 'Sampaio', $documentoPessoa, $nacionalidade, $cpf, $data , $email,
            $numeroDeRegistro, $programaDeMilhagem);
            $this->checkEq($programaDeMilhagem->update($passageiroSemPontos), $categoriaBase);

            $passageiroComPontosVencidos = new PassageiroVip('Maria', 'Sampaio', $documentoPessoa, $nacionalidade, $cpf, $data , $email,
            $numeroDeRegistro, $programaDeMilhagem);
            foreach ($pontuacaoVencida as $pontos) {
                $passageiroComPontosVencidos->addPontos($pontos->getPontosGanhos(), $pontos->getDataDeObtencao());
            }
            $this->checkEq($programaDeMilhagem->update($passageiroComPontosVencidos), $categoriaBase);

            $passageiroComPontos = new PassageiroVip('Maria', 'Sampaio', $documentoPessoa, $nacionalidade, $cpf, $data , $email, $numeroDeRegistro, $programaDeMilhagem);

            foreach ($pontuacaoPreenchida as $pontos) {
                $passageiroComPontos->addPontos($pontos->getPontosGanhos(), $pontos->getDataDeObtencao());
            }
            $this->checkEq($programaDeMilhagem->update($passageiroComPontos), $categorias[3]);

            $passageiroComPontos->addPontos(4000);
            $this->checkEq($programaDeMilhagem->update($passageiroComPontos), $categorias[4]);

        }
    }
?>