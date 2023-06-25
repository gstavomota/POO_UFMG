<?php
require_once "suite.php";
require_once "mixins/aeroportos_mixin.php";
require_once "mixins/tripulante_mixin.php";
require_once "../classes/calculo_rota_strategy.php";
class RoteiroDeTestesTestCase extends TestCase
{
    use AeroportosMixin;
    use TripulanteMixin;
    protected function getName(): string
    {
        return "RoteiroDeTestes";
    }
    private function cartaoDeEmbarqueToString(CartaoDeEmbarque $cartaoDeEmbarque) {
        $s = "";
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
        $s = $s.")";
        return $s;
    }
    public function run()
    {
        $this->initTripulante();
        $this->initAeroportos();
        $this->limparAeroportos();
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
        $this->assertNotNull($aeronaveLatam);
        $this->assertNotNull($latam->encontrarAeronave($registroAeronaveLatam));
        $latam->save();
        $aeronaveAzul_1 = $azul->registrarAeronave("Embraer", "175", 180, 600, $registroAeronaveAzul_1);
        $azul->save();

        // Cadastre os aeroportos de Confins, Guarulhos, Congonhas, Galeão e Afonso Pena. Os
        // dados desse aeroporto podem ser encontrados na internet.
        $this->registrarAeroportos();

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
                $this->siglaConfins,
                $this->siglaGuarulhos,
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
            $this->siglaConfins,
            $this->siglaGuarulhos,
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
            $this->siglaConfins,
            $this->siglaCongonhas,
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
            $this->siglaCongonhas,
            $this->siglaConfins,
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
            $this->siglaCongonhas,
            $this->siglaAfonsoPena,
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
            $this->siglaAfonsoPena,
            $this->siglaCongonhas,
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
        $documentoPessoa = new DocumentoPessoa(new Passaporte("A12345678"));
        $passageiro = new PassageiroVip(
            "Joao",
            "Alves",
            $documentoPessoa,
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
        $registroPassagem = $azul->comprarPassagem($documentoPessoa, $amanha, $this->siglaConfins, $this->siglaAfonsoPena, $franquias);
        $passagem = $azul->encontrarPassagem($registroPassagem);
        $this->checkNotNull($registroPassagem);
        $cartoesDeEmbarque = $azul->fazerCheckIn($registroPassagem);
        // 3. Imprimir os cartoes de embarque
        $s = "";
        foreach ($cartoesDeEmbarque as $cartaoDeEmbarque) {
            $s = $s.$this->cartaoDeEmbarqueToString($cartaoDeEmbarque);
            $s = $s."\n";
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
            /**
             * @var ViagemBuilder $viagemBuilder
             */
            $viagemBuilder = $this->runNonPublicMethod($azul, "findRequiredViagemBuilder", $registroViagem);
            $azul->registrarTripulanteNaViagem($registroViagem, $piloto->getRegistro(), $this->coordenadaTripulante($viagemBuilder->getAeroportoDeSaida()));
            $azul->registrarTripulanteNaViagem($registroViagem, $copiloto->getRegistro(), $this->coordenadaTripulante($viagemBuilder->getAeroportoDeSaida()));
            $azul->registrarTripulanteNaViagem($registroViagem, $comissario->getRegistro(), $this->coordenadaTripulante($viagemBuilder->getAeroportoDeSaida()));
            $azul->registrarTripulanteNaViagem($registroViagem, $comissario1->getRegistro(), $this->coordenadaTripulante($viagemBuilder->getAeroportoDeSaida()));
            $azul->registrarTripulanteNaViagem($registroViagem, $comissario2->getRegistro(), $this->coordenadaTripulante($viagemBuilder->getAeroportoDeSaida()));
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
        // 1. Pegar a data daqui a dois dias
        $doisDias = new Duracao(2, 0);
        $doisDiasAposAIda = $amanha->add($doisDias);
        // 2. Registrar voo de ida e de volta na latam
        $vooLatam1330 = $latam->registrarVoo(
            1330,
            $this->siglaConfins,
            $this->siglaAfonsoPena,
            $hora9am,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveLatam,
            1000.0,
            1000
        );
        $vooLatam1331 = $latam->registrarVoo(
            1331,
            $this->siglaAfonsoPena,
            $this->siglaConfins,
            $hora1pm,
            $duracaoDuasHoras,
            $todosOsDias,
            $registroAeronaveLatam,
            1000.0,
            1000
        );
        // 3. Registrar passageiro na latam
        $latam->adicionarPassageiro($passageiro);
        // 4. Comprar a passagem de ida
        $registroPassagemIda = $latam->comprarPassagem($documentoPessoa, $amanha, $this->siglaConfins, $this->siglaAfonsoPena, $franquias);
        $this->assertNotNull($registroPassagemIda);
        // 5. Comprar a passagem de volta
        $registroPassagemVolta = $latam->comprarPassagem($documentoPessoa, $doisDiasAposAIda, $this->siglaAfonsoPena, $this->siglaConfins, $franquias);
        $this->assertNotNull($registroPassagemVolta);
        // 6. Cancelar passagem de volta
        $passagemVolta = $latam->cancelarPassagem($registroPassagemVolta);
        $valorRessarcido = $passagemVolta->reembolsar();
        $valorDevendo = $passagemVolta->valorDevendo();
        echo "Valor ressarcido: $valorRessarcido\n";
        echo "Valor devendo: $valorDevendo\n";
        // Cadastre e planeje a tripulação que atuará na primeira viagem do vôo de ida do
        // passageiro. A rota da van que vai buscar a tripulação para a realização da viagem
        // também deve ser planejada. Os horários em que cada tripulante embarca na van devem
        // ser exibidos.
        // Ao final todos os logs das operações realizadas devem ser exibidos na tela.
        // 1. Cadastrar tripulação na companhia aerea
        $pilotoLatam = $this->registrarTripulante($latam, Cargo::PILOTO);
        $copilotoLatam = $this->registrarTripulante($latam, Cargo::COPILOTO);
        $comissarioLatam = $this->registrarTripulante($latam, Cargo::COMISSARIO);
        $comissario1Latam = $this->registrarTripulante($latam, Cargo::COMISSARIO);
        $comissario2Latam = $this->registrarTripulante($latam, Cargo::COMISSARIO);
        // 2. Registrar tripulação na viagem de ida
        $passagemIda = $latam->encontrarPassagem($registroPassagemIda);
        /**
         * @var RegistroDeViagem $registroViagem
         */
        $registroViagem = $passagemIda->getAssentos()[0]->key;
        $latam->registrarTripulanteNaViagem($registroViagem, $pilotoLatam->getRegistro(), $this->coordenadaTripulante($this->siglaConfins));
        $latam->registrarTripulanteNaViagem($registroViagem, $copilotoLatam->getRegistro(), $this->coordenadaTripulante($this->siglaConfins));
        $latam->registrarTripulanteNaViagem($registroViagem, $comissarioLatam->getRegistro(), $this->coordenadaTripulante($this->siglaConfins));
        $latam->registrarTripulanteNaViagem($registroViagem, $comissario1Latam->getRegistro(), $this->coordenadaTripulante($this->siglaConfins));
        $latam->registrarTripulanteNaViagem($registroViagem, $comissario2Latam->getRegistro(), $this->coordenadaTripulante($this->siglaConfins));
        // 3. Pegar o onibus
        /**
         * @var ViagemBuilder $viagemBuilder
         */
        $viagemBuilder = $this->runNonPublicMethod($latam, "findRequiredViagemBuilder", $registroViagem);
        $onibus = $viagemBuilder->getOnibus();
        // 4. Calcular a hora de saida
        $horasDeSaida = $onibus->horaDeSaida(new CalculoRotaAproximadaStrategy());
        // 5. Imprimir a hora de saida
        echo $horasDeSaida;

    }
}