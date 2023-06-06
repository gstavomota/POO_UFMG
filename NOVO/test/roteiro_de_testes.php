<?php

class RoteiroDeTestesTestCase extends TestCase
{
    protected function getName(): string
    {
        return "RoteiroDeTestes";
    }
    private function buildEndereco(): Endereco
    {

        $logradouro = "Avenida Amazonas";
        $numero = 1;
        $bairro = "Gutierrez";
        $cep = new CEP("30150-312");
        $cidade = "Belo Horizonte";
        $estado = Estado::MG;
        $referencia = "Proximo a Avenida Silva Lobo";
        return new Endereco(
            $logradouro,
            $numero,
            $bairro,
            $cep,
            $cidade,
            $estado,
            $referencia
        );
    }
    private int $lastComissario = 0;

    private function registrarTripulante(CompanhiaAerea &$companhiaAerea, Cargo $cargo): RegistroDeTripulante
    {
        $nome = "";
        match ($cargo) {
            Cargo::PILOTO => $nome = "Pedro",
            Cargo::COPILOTO => $nome = "Gustavo",
            Cargo::COMISSARIO => $nome = ["Bruno", "Raissa", "Maria Eduarda"][$this->lastComissario]
        };
        $sobrenome = "";
        match ($cargo) {
            Cargo::PILOTO => $sobrenome = "Kalil",
            Cargo::COPILOTO => $sobrenome = "Motta",
            Cargo::COMISSARIO => $sobrenome = ["Lima", "Diniz", "Sampaio"][$this->lastComissario]
        };
        $registro = null;
        match ($cargo) {
            Cargo::PILOTO => $registro = new RegistroDeTripulante(0),
            Cargo::COPILOTO => $registro = new RegistroDeTripulante(1),
            Cargo::COMISSARIO => $registro = [new RegistroDeTripulante(2), new RegistroDeTripulante(3), new RegistroDeTripulante(4)][$this->lastComissario]
        };
        $dataDeNascimento = new Data(2003,1,1);
        if ($cargo === Cargo::COMISSARIO) {
            $this->lastComissario++;
        }
        return $companhiaAerea->registrarTripulante(
            $nome,
            $sobrenome,
            new CPF("111.111.111-11"),
            Nacionalidade::BRASIL,
            $dataDeNascimento,
            new Email("tripulante@gmail.com"),
            "CHT",
            $this->buildEndereco(),
            new SiglaAeroporto("GRU"),
            $cargo
        )->getRegistro();
    }

    public function run()
    {
        // Cadastre duas companhias aéreas
        // • Nome: Latam
        // • Código: 001
        // • Razão Social: Latam Airlines do Brasil S.A.
        // • Sigla: LA
        $latam = new CompanhiaAerea("Latam", "001", "Latam Airlines do Brasil S.A.", new SiglaCompanhiaAerea("LA"), 10);
        $latam->save();
        // • Nome: Azul
        // • Código: 002
        // • Razão Social: Azul Linhas Aéreas Brasileiras S.A.
        // • Sigla: AD
        $azul = new CompanhiaAerea("Azul", "002", "Azul Linhas Aéreas Brasileiras S.A.", new SiglaCompanhiaAerea("AD"), 10);
        $azul->save();

        // Cadastre duas aeronaves do modelo 175 da fabricante Embraer, com capacidade de 180
        // passageiros e 600 kg de carga. A primeira aeronave deve pertencer a Latam e a segunda
        // à Azul.
        //     Defina a sigla da primeira aeronave como PX-RUZ. Seu código deve validar a sigla e tratar
        // a exceção. Em seguida a sigla deve ser corrigida para PP-RUZ.
        try {
            $latam->registrarAeronave("Embraer", "175", 180, 600, new RegistroDeAeronave(PrefixoRegistroDeAeronave::PX, "RUZ"));
            $this->checkNotReached();
        } catch (Error $e) {
            $this->checkReached();
        }
        $registroAeronaveLatam = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "RUZ");
        $registroAeronaveAzul_1 = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "FOO");
        $aeronaveLatam = $latam->registrarAeronave("Embraer", "175", 180, 600, $registroAeronaveLatam);
        $latam->save();
        $aeronaveAzul_1 = $azul->registrarAeronave("Embraer", "175", 180, 600, $registroAeronaveAzul_1);
        $azul->save();

        // Cadastre os aeroportos de Confins, Guarulhos, Congonhas, Galeão e Afonso Pena. Os
        // dados desse aeroporto podem ser encontrados na internet.
        $aeroportoConfins = new Aeroporto(new SiglaAeroporto("CNF"), "Confins", "Belo Horizonte", Estado::MG);
        $aeroportoConfins->save();
        $aeroportoGuarulhos = new Aeroporto(new SiglaAeroporto("GRU"), "Guarulhos", "São Paulo", Estado::SP);
        $aeroportoGuarulhos->save();
        $aeroportoCongonhas = new Aeroporto(new SiglaAeroporto("CGH"), "Congonhas", "São Paulo", Estado::SP);
        $aeroportoCongonhas->save();
        $aeroportoGaleao = new Aeroporto(new SiglaAeroporto("GIG"), "Galeão", "Rio de Janeiro", Estado::RJ);
        $aeroportoGaleao->save();
        $aeroportoAfonsoPena = new Aeroporto(new SiglaAeroporto("CWB"), "Afonso Pena", "São José dos Pinhais", Estado::PR);
        $aeroportoAfonsoPena->save();

        // Cadastre o voo AC1329 da Azul ligando os aeroportos de Confins e Guarulhos. Seu
        // código de testes deve validar o código do voo, tratar a exceção e em seguida alterar o
        // código para utilizar a sigla correta da companhia aérea.
        $hora9am = new Tempo(9, 0, 0);
        $duracaoUmaHora = Duracao::umaHora();
        $duracaoDuasHoras = $duracaoUmaHora->mul(2);
        $hora1pm = new Tempo(13, 0, 0);
        $hora6pm = new Tempo(18, 0, 0);
        $hora9pm = new Tempo(21, 0, 0);
        $todosOsDias = DiaDaSemana::cases();
        try {
            // Esse estado invalido é impossivel de ser representado com a arquitetura atual
            $vooAzulInvalido = new Voo(
                new CodigoVoo(new SiglaCompanhiaAerea("AC"), 1329),
                $aeroportoConfins->getSigla(),
                $aeroportoGuarulhos->getSigla(),
                $hora9am,
                $duracaoDuasHoras,
                $todosOsDias,
                $registroAeronaveAzul_1,
                $aeronaveAzul_1->getCapacidadePassageiros(),
                $aeronaveAzul_1->getCapacidadeCarga(),
                1000.0,
                1000
            );
            $azul->adicionarVoo($vooAzulInvalido);
            $this->checkNotReached();
        } catch (Error $e) {
            $this->checkReached();
        }
        $vooAzul1329 = $azul->registrarVoo(
            1329,
            $aeroportoConfins->getSigla(),
            $aeroportoGuarulhos->getSigla(),
            $hora9am,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveAzul_1,
            $aeronaveAzul_1->getCapacidadePassageiros(),
            $aeronaveAzul_1->getCapacidadeCarga(),
            1000.0,
            1000
        );
        $this->checkEq($vooAzul1329->getCodigo(), new CodigoVoo($azul->getSigla(), 1329));

        // Cadastre dois voos diários de ida e volta adicionais, sendo um pela manhã e outro pela
        // tarde, entre os aeroportos abaixo:
        // • Confins – Guarulhos
        // • Confins – Congonhas
        // • Guarulhos – Galeão
        // • Congonhas – Afonso Pena

        // Ida confins-congonhas manha
        $registroAeronaveAzul_2 = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "AAA");
        $aeronaveAzul_2 = $azul->registrarAeronave("Embraer", "175", 180, 600, $registroAeronaveAzul_2);
        $vooAzul0001 = $azul->registrarVoo(
            1,
            $aeroportoConfins->getSigla(),
            $aeroportoCongonhas->getSigla(),
            $hora9am,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveAzul_2,
            $aeronaveAzul_2->getCapacidadePassageiros(),
            $aeronaveAzul_2->getCapacidadeCarga(),
            1000.0,
            1000
        );
        // Volta confins-congonhas manha
        $vooAzul0002 = $azul->registrarVoo(
            2,
            $aeroportoCongonhas->getSigla(),
            $aeroportoConfins->getSigla(),
            $hora1pm,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveAzul_2,
            $aeronaveAzul_2->getCapacidadePassageiros(),
            $aeronaveAzul_2->getCapacidadeCarga(),
            1000.0,
            1000
        );

        // Ida congonhas-afonso pena tarde
        $registroAeronaveAzul_3 = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "AAB");
        $aeronaveAzul_3 = $azul->registrarAeronave("Embraer", "175", 180, 600, $registroAeronaveAzul_3);
        $vooAzul0003 = $azul->registrarVoo(
            3,
            $aeroportoCongonhas->getSigla(),
            $aeroportoAfonsoPena->getSigla(),
            $hora6pm,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveAzul_3,
            $aeronaveAzul_3->getCapacidadePassageiros(),
            $aeronaveAzul_3->getCapacidadeCarga(),
            1000.0,
            1000
        );
        // Volta congonhas-afonso pena tarde
        $vooAzul0004 = $azul->registrarVoo(
            4,
            $aeroportoAfonsoPena->getSigla(),
            $aeroportoCongonhas->getSigla(),
            $hora9pm,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveAzul_3,
            $aeronaveAzul_3->getCapacidadePassageiros(),
            $aeronaveAzul_3->getCapacidadeCarga(),
            1000.0,
            1000
        );

        // Com base nos voos cadastrados o sistema deve-se gerar todas as viagens disponíveis
        // para compra pelos próximos 30 dias, utilizando aeronaves previamente cadastradas no
        // sistema
        $azul->adicionarViagensEmVenda();
        // Um cliente deve realizar a compra da passagem somente de ida para um passageiro Vip
        // para amanhã (essa data deve ser um parâmetro no código de testes), entre os
        // aeroportos de Confins (Belo Horizonte-MG) e Afonso Pena (Curitiba-PR). Esse passageiro
        // deve ser previamente cadastrado. Ele faz parte do Programa de Milhagem da Azul. Os
        // vôos de ida devem ser da Azul.
        // Deve ser feito o checkin da passagem e os cartões de embarque gerados e impressos na
        // tela.
        // Feito isto, simule a realização das viagens envolvidas.

        // 1. registrar passageiro vip
        $programaDeMilhagemAzul = new ProgramaDeMilhagem([
            new Categoria("branco", 0),
            new Categoria("bronze", 1000),
            new Categoria("prata", 2000),
            new Categoria("ouro", 3000)
        ], "Azul");
        $documentoPassageiro = new DocumentoPassageiro(new Passaporte("A12345678"));
        $passageiro = new PassageiroVip(
            "Joao",
            "Alves",
            $documentoPassageiro,
            Nacionalidade::BRASIL,
            new CPF("111.111.111-11"),
            new Data(1970, 1, 1),
            new Email("joaoalves@gmail.com"),
            "123",
            $programaDeMilhagemAzul
        );
        $azul->adicionarPassageiro($passageiro);
        // 2. Comprar passagem somente de ida de Confins (Belo Horizonte-MG) e Afonso Pena (Curitiba-PR)
        $franquias = new FranquiasDeBagagem([new FranquiaDeBagagem(20.0)]);
        $amanha = Data::hoje()->add(Duracao::umDia());
        $registroPassagem = $azul->comprarPassagem($documentoPassageiro, $amanha, $aeroportoConfins->getSigla(), $aeroportoAfonsoPena->getSigla(), $franquias);
        $passagem = $azul->encontrarPassagem($registroPassagem);
        $this->checkNeq($registroPassagem, null);
        $cartoesDeEmbarque = $azul->fazerCheckIn($registroPassagem);
        $s = "";
        // 3. Imprimir os cartoes de embarque
        foreach ($cartoesDeEmbarque as $cartaoDeEmbarque) {
            $s = $s."CartaoDeEmbarque(";
            $s = $s.$cartaoDeEmbarque->getId();
            $s = $s.", ";
            $s = $s.'"';
            $s = $s.$cartaoDeEmbarque->getNomePassageiro();
            $s = $s." ";
            $s = $s.$cartaoDeEmbarque->getSobrenomePassageiro();
            $s = $s.'"';
            $s = $s.", ";
            $s = $s.$cartaoDeEmbarque->getSiglaAeroportoDeSaida();
            $s = $s.", ";
            $s = $s.$cartaoDeEmbarque->getSiglaAeroportoDeChegada();
            $s = $s.", ";
            $s = $s.$cartaoDeEmbarque->getMomentoMaximoDeEmbarque();
            $s = $s.", ";
            $s = $s.$cartaoDeEmbarque->getAssento();
            $s = $s.")\n";
        }
        echo $s;
        // 4. Embarcar
        $azul->embarcar($passagem->getRegistro());
        // 5. Simule as viagens envolvidas
        $piloto = $this->registrarTripulante($azul, Cargo::PILOTO);
        $copiloto = $this->registrarTripulante($azul, Cargo::COPILOTO);
        $comissario = $this->registrarTripulante($azul, Cargo::COMISSARIO);
        $comissario1 = $this->registrarTripulante($azul, Cargo::COMISSARIO);
        $comissario2 = $this->registrarTripulante($azul, Cargo::COMISSARIO);
        foreach ($passagem->getAssentos() as $viagem_assento) {
            /**
             * @var RegistroDeViagem $registroViagem
             */
            $registroViagem = $viagem_assento->key;
            print_r($registroViagem);
            $azul->registrarTripulanteNaViagem($registroViagem, $piloto);
            $azul->registrarTripulanteNaViagem($registroViagem, $copiloto);
            $azul->registrarTripulanteNaViagem($registroViagem, $comissario);
            $azul->registrarTripulanteNaViagem($registroViagem, $comissario1);
            $azul->registrarTripulanteNaViagem($registroViagem, $comissario2);
        }
        /**
         * @var RegistroDeViagem $registroViagemConfinsCongonhas
         */
        $registroViagemConfinsCongonhas = $passagem->getAssentos()[0]->key;
        /**
         * @var RegistroDeViagem $registroViagemCongonhasAfonsoPena
         */
        $registroViagemCongonhasAfonsoPena = $passagem->getAssentos()[1]->key;
        $azul->registrarAeronaveNaViagem($registroViagemConfinsCongonhas, $registroAeronaveAzul_2);
        $azul->registrarAeronaveNaViagem($registroViagemCongonhasAfonsoPena, $registroAeronaveAzul_3);
        $azul->registrarQueViagemAconteceu(
            $vooAzul0001->getHoraDePartida()->comData($amanha),
            $vooAzul0001->getHoraDePartida()->add($vooAzul0001->getDuracaoEstimada())->comData($amanha),
            $registroViagemConfinsCongonhas
        );
        $azul->registrarQueViagemAconteceu(
            $vooAzul0003->getHoraDePartida()->comData($amanha),
            $vooAzul0003->getHoraDePartida()->add($vooAzul0003->getDuracaoEstimada())->comData($amanha),
            $registroViagemCongonhasAfonsoPena
        );
        $viagemConfinsCongonhas = $azul->encontrarViagem($registroViagemConfinsCongonhas);
        $viagemCongonhasAfonsoPena = $azul->encontrarViagem($registroViagemCongonhasAfonsoPena);
        // Deve ser adquirida também uma passagem de volta em pelo menos um vôo da Latam
        // dois dias após a ida. Deve-se tentar fazer checkin dessa passagem.
        // Logo após essa passagem deve ser cancelada. Os valores de ressarcimento devem ser
        // calculados e exibidos na tela.
        //
        // Cadastre e planeje a tripulação que atuará na primeira viagem do vôo de ida do
        // passageiro. A rota da van que vai buscar a tripulação para a realização da viagem
        // também deve ser planejada. Os horários em que cada tripulante embarca na van devem
        // ser exibidos.
        // Ao final todos os logs das operações realizadas devem ser exibidos na tela.




    }
}