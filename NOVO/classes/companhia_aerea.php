<?php

require_once 'aeronave.php';
require_once 'cartaoDeEmbarque.php';
require_once 'encontrar_melhor_voo_strategy.php';
require_once 'passageiro.php';
require_once 'franquia_de_bagagem.php';
require_once 'identificadores.php';
require_once 'passagem.php';
require_once 'persist.php';
require_once 'temporal.php';
require_once 'viagem.php';
require_once 'viagem_builder.php';
require_once 'voo.php';
require_once 'tripulante.php';
require_once 'coordenada.php';
require_once "log.php";

/**
 * @extends persist<CompanhiaAerea>
 */
class CompanhiaAerea extends Persist
{
    private string $nome;
    private string $codigo;
    private string $razao_social;
    private SiglaCompanhiaAerea $sigla;
    /**
     * @var HashMap<RegistroDeAeronave, Aeronave>
     */
    private HashMap $aeronaves;
    /**
     * @var HashMap<CodigoVoo, Voo>
     */
    private HashMap $voos_planejados;
    /**
     * @var HashMap<Data, HashMap<RegistroDeViagem, ViagemBuilder>>
     */
    private HashMap $voos_em_venda;
    /**
     * @var HashMap<RegistroDeViagem, Viagem>
     */
    private HashMap $voos_executados;
    private GeradorDeRegistroDeViagem $gerador_de_registro_de_viagem;
    private GeradorDeRegistroDePassagem $gerador_de_registro_de_passagem;
    private float $tarifa_franquia;
    /**
     * @var HashMap<RegistroDePassagem, Passagem>
     */
    private HashMap $passagens;
    /**
     * @var HashMap<DocumentoPessoa, Passageiro>
     */
    private HashMap $passageiros;
    /**
     * @var HashMap<RegistroDeTripulante, Tripulante>
     */
    private HashMap $tripulantes;
    private GeradorDeRegistroDeTripulante $gerador_de_registro_de_tripulante;
    /**
     * @var HashMap<RegistroDeCartaoDeEmbarque, CartaoDeEmbarque>
     */
    private HashMap $cartoesDeEmbarque;
    private GeradorDeRegistroDeCartaoDeEmbarque $geradorDeRegistroDeCartaoDeEmbarque;
    private static $local_filename = "companhia_aerea.txt";

    public function __construct(string              $nome,
                                string              $codigo,
                                string              $razao_social,
                                SiglaCompanhiaAerea $sigla,
                                float               $tarifa_franquia,
                                                    ...$args)
    {

        $this->nome = $nome;
        $this->codigo = $codigo;
        $this->razao_social = $razao_social;
        $this->sigla = $sigla;
        $this->aeronaves = new HashMap();
        $this->voos_planejados = new HashMap();
        $this->voos_em_venda = new HashMap();
        $this->voos_executados = new HashMap();
        $this->gerador_de_registro_de_viagem = new GeradorDeRegistroDeViagem();
        $this->gerador_de_registro_de_passagem = new GeradorDeRegistroDePassagem();
        $this->tarifa_franquia = $tarifa_franquia;
        $this->passagens = new HashMap();
        $this->passageiros = new HashMap();
        $this->tripulantes = new HashMap();
        $this->gerador_de_registro_de_tripulante = new GeradorDeRegistroDeTripulante();
        $this->cartoesDeEmbarque = new HashMap();
        $this->geradorDeRegistroDeCartaoDeEmbarque = new GeradorDeRegistroDeCartaoDeEmbarque($sigla);
        parent::__construct(...$args);
    }

    public function getNome(): string
    {
        return log::getInstance()->logRead($this->nome);
    }

    public function getCodigo(): string
    {
        return log::getInstance()->logRead($this->codigo);
    }

    public function getRazaoSocial(): string
    {
        return log::getInstance()->logRead($this->razao_social);
    }

    public function getSigla(): SiglaCompanhiaAerea
    {
        return log::getInstance()->logRead($this->sigla);
    }

    public function getTarifaFranquia(): float
    {
        return $this->tarifa_franquia;
    }

    public static function getFilename(): string
    {
        return self::$local_filename;
    }

    /**
     * Procura de voos
     */


    /**
     * Bookkeeping para ViagemBuilder
     */

    // TESTED
    public function adicionarViagensEmVenda(): void
    {
        /**
         * @var Data[] $datas_atuais
         */
        $datas_atuais = $this->voos_em_venda->keys();
        $hoje = Data::hoje();
        /**
         * @var Data[] $datas_alvo
         */
        $datas_alvo = array_map(function (int $i) use ($hoje) {
            return $hoje->add(Duracao::umDia()->mul($i));
        }, range(0, 30));
        /**
         * @var Data[] $datas_nao_preenchidas
         */
        $datas_nao_preenchidas = array_diff_equatable($datas_alvo, $datas_atuais);

        foreach ($datas_nao_preenchidas as $data) {
            /**
             * @var Voo[] $voos_nesse_dia_da_semana
             */
            $voos_nesse_dia_da_semana = array_filter($this->voos_planejados->values(), function (Voo $voo) use ($data) {
                return in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana());
            });
            /**
             * @var HashMap<RegistroDeViagem, Viagem> $viagens_nesse_dia_da_semana
             */
            $viagens_nesse_dia_da_semana = $this->voos_em_venda->put($data, new HashMap());

            foreach ($voos_nesse_dia_da_semana as $voo_que_ira_acontecer) {
                $viagem_builder = (new ViagemBuilder())
                    ->addTarifaFranquia($this->tarifa_franquia)
                    ->adicionarGeradorDeRegistro($this->gerador_de_registro_de_viagem)
                    ->gerarRegistro()
                    ->addData($data)
                    ->addVoo($voo_que_ira_acontecer);

                $registro_da_viagem = $viagem_builder->getRegistro();
                $viagens_nesse_dia_da_semana->put($registro_da_viagem, $viagem_builder);
            }
        }
        log::getInstance()->logCall(null);
    }

    public function registrarAeronaveNaViagem(RegistroDeViagem $registroDeViagem, RegistroDeAeronave $registroDeAeronave)
    {
        if (!$this->aeronaves->containsKey($registroDeAeronave)) {
            log::getInstance()->logThrow(new Exception("Aeronave não presente na companhia"));
        }
        /**
         * @var Aeronave $aeronave
         */
        $aeronave = $this->aeronaves->get($registroDeAeronave);
        $vb = $this->findRequiredViagemBuilder($registroDeViagem);
        $vb->addAeronave($aeronave);
        log::getInstance()->logCall(null);
    }

    public function registrarTripulanteNaViagem(RegistroDeViagem $registroDeViagem, RegistroDeTripulante $registroDeTripulante, ICoordenada $coordenada)
    {
        if (!$this->tripulantes->containsKey($registroDeTripulante)) {
            log::getInstance()->logThrow(new Exception("Tripulante não presente na companhia"));
        }
        /**
         * @var Tripulante $tripulante
         */
        $tripulante = $this->tripulantes->get($registroDeTripulante);
        $vb = $this->findRequiredViagemBuilder($registroDeViagem);
        $vb->addTripulante($tripulante, $coordenada);
        log::getInstance()->logCall(null);
    }

    public function registrarQueViagemAconteceu(DataTempo $hora_de_partida, DataTempo $hora_de_chegada, RegistroDeViagem $registro_de_viagem)
    {
        $builder = $this->removeViagemBuilder($registro_de_viagem);
        $builder->addHoraDePartidaEHoraDeChegada($hora_de_partida, $hora_de_chegada);
        try {
            $viagem = $builder->build();
        } catch (Throwable $e) {
            $this->voos_em_venda->get($builder->getData())->put($builder->getRegistro(), $builder);
            log::getInstance()->logThrow(new Exception("Não foi possivel buildar a Viagem", $e->getCode(), $e));
        }
        $this->voos_executados->put($viagem->getRegistro(), $viagem);
        /**
         * @var Assento $assento
         */
        foreach ($viagem->getAssentos()->values() as $assento) {
            if ($assento->vazio())
                continue;

            $registro_passagem = $assento->getPassagem();
            /**
             * @var Passagem $passagem
             */
            $passagem = $this->passagens->get($registro_passagem);
            $passagem->acionarEvento(Evento::CONCLUIR);
        }
        log::getInstance()->logCall(null);
    }

    /**
     * Metodos para passagens
     */

    public function cancelarPassagem(RegistroDePassagem $registroDePassagem): Passagem
    {
        if (!$this->passagens->containsKey($registroDePassagem)) {
            log::getInstance()->logThrow(new Exception("Passagem não está na companhia"));
        }

        /**
         * @var Passagem $passagem
         */
        $passagem = $this->passagens->get($registroDePassagem);

        if (!$passagem->acionarEvento(Evento::CANCELAR)) {
            log::getInstance()->logThrow(new Exception("A passagem não pode ser cancelada agora"));
        }

        $data = $passagem->getData();

        foreach ($passagem->getAssentos() as $entry) {
            /**
             * @var RegistroDeViagem $registro_viagem
             */
            $registro_viagem = $entry->key;
            /**
             * @var CodigoDoAssento $codigo_assento
             */
            $codigo_assento = $entry->value;
            $viagem_builder = $this->findRequiredViagemBuilder($registro_viagem, $data);
            $viagem_builder->liberarAssento($passagem->getRegistro(), $codigo_assento);
        }
        return log::getInstance()->logCall($passagem);
    }

    public function abrirCheckInParaPassagens(RegistroDePassagem ...$args)
    {
        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        if (!empty($args)) {
            foreach ($args as $registro_passagem) {
                if (!$this->passagens->containsKey($registro_passagem)) {
                    log::getInstance()->logThrow(new Exception("Passagem não está na companhia"));
                }

                /**
                 * @var Passagem $passagem
                 */
                $passagem = $this->passagens->get($registro_passagem);

                $delta = $passagem->getData()->dt($hoje);
                if ($delta->st($twoDays)) {
                    continue;
                }

                log::getInstance()->logThrow(new Exception("Passagem está a mais de 48h de distância"));
            }

            foreach ($args as $registro_passagem) {
                /**
                 * @var Passagem $passagem
                 */
                $passagem = $this->passagens->get($registro_passagem);
                $passagem->acionarEvento(Evento::ABRIR_CHECK_IN);
            }

            return log::getInstance()->logCall(null);
        }

        /**
         * @var Passagem $passagem
         */
        foreach ($this->passagens->values() as $passagem) {
            $delta = $passagem->getData()->dt($hoje);
            if ($delta->st($twoDays)) {
                $passagem->acionarEvento(Evento::ABRIR_CHECK_IN);
            }
        }
        log::getInstance()->logCall(null);
    }

    /** Retorna o historico de viagens de um passageiro
     * @param DocumentoPessoa $documentoPessoa
     * @return Viagem[]
     * @throws ComparableTypeException
     * @throws EquatableTypeException
     */
    public function acessarHistoricoDeViagens(DocumentoPessoa $documentoPessoa): array
    {
        if (!$this->passageiros->containsKey($documentoPessoa)) {
            log::getInstance()->logThrow(new Exception("Passageiro nao cadastrado"));
        }

        /**
         * @var Passageiro $passageiro
         */
        $passageiro = $this->passageiros->get($documentoPessoa);
        $registros_de_passagens = $passageiro->getPassagens();
        /**
         * @var Passagem[] $passagens
         */
        $passagens = array_map(function (RegistroDePassagem $passagem) {
            return $this->passagens->get($passagem);
        }, $registros_de_passagens);

        /**
         * @var Viagem[] $viagens
         */
        $viagens = [];

        foreach ($passagens as $passagem) {
            /**
             * @var RegistroDeViagem[] $registros_de_viagens
             */
            $registros_de_viagens = $passagem->getAssentos()->keys();
            /**
             * @var Viagem[] $viagens_na_passagem
             */
            $viagens_na_passagem = array_map(function (RegistroDeViagem $registro_de_viagem) {
                return $this->voos_executados->get($registro_de_viagem);
            }, $registros_de_viagens);

            foreach ($viagens_na_passagem as $viagem) {
                $viagens[] = $viagem;
            }
        }

        usort($viagens, function (Viagem $a, Viagem $b): int {
            if ($a->getHoraDePartida()->eq($b->getHoraDePartida())) {
                return 0;
            }
            return $a->getHoraDePartida()->st($b->getHoraDePartida()) ? -1 : 1;
        });
        return $viagens;
    }

    /**
     * @param RegistroDePassagem $registroDePassagem
     * @return CartaoDeEmbarque[]
     * @throws ComparableTypeException
     */
    function fazerCheckIn(RegistroDePassagem $registroDePassagem): array
    {
        if (!$this->passagens->containsKey($registroDePassagem)) {
            log::getInstance()->logThrow(new Exception("Passagem não está na companhia"));
        }

        /**
         * @var Passagem $passagem
         */
        $passagem = $this->passagens->get($registroDePassagem);

        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        $delta = $passagem->getData()->dt($hoje);
        if ($delta->st($twoDays)) {
            $passagem->acionarEvento(Evento::ABRIR_CHECK_IN);
        }

        if (!$passagem->acionarEvento(Evento::FAZER_CHECK_IN)) {
            log::getInstance()->logThrow(new Exception("Não é possível fazer check-in agora"));
        }
        $passageiro = $this->passageiros->get($passagem->getDocumentoCliente());
        /**
         * @var CartaoDeEmbarque[] $cartoesDeEmbarque
         */
        $cartoesDeEmbarque = [];
        foreach ($passagem->getAssentos() as $viagem_assento) {
            /**
             * @var RegistroDeViagem $registroViagem
             */
            $registroViagem = $viagem_assento->key;
            /**
             * @var CodigoDoAssento $codigoAssento
             */
            $codigoAssento = $viagem_assento->value;
            $registroDeCartaoDeEmbarque = $this->geradorDeRegistroDeCartaoDeEmbarque->gerar();
            $viagemBuilder = $this->findRequiredViagemBuilder($registroViagem);
            $horaPrimeiroVoo = $viagemBuilder->getHoraDePartidaEstimada();
            $duracao40min = new Duracao(0, 40 * 60);
            $horaDeEmbarque = $horaPrimeiroVoo->sub($duracao40min);
            $cartaoDeEmbarque = new CartaoDeEmbarque(
                $registroDeCartaoDeEmbarque,
                $passageiro->getNome(),
                $passageiro->getSobrenome(),
                $viagemBuilder->getAeroportoDeSaida(),
                $viagemBuilder->getAeroportoDeChegada(),
                $horaDeEmbarque,
                $codigoAssento,
            );
            $this->cartoesDeEmbarque->put($registroDeCartaoDeEmbarque, $cartaoDeEmbarque);
            $cartoesDeEmbarque[] = $cartaoDeEmbarque;
        }
        return log::getInstance()->logCall($cartoesDeEmbarque);
    }

    function embarcar(RegistroDePassagem $registroDePassagem): void
    {
        if (!$this->passagens->containsKey($registroDePassagem)) {
            log::getInstance()->logThrow(new Exception("Passagem não está na companhia"));
        }

        /**
         * @var Passagem $passagem
         */
        $passagem = $this->passagens->get($registroDePassagem);
        if (!$passagem->acionarEvento(Evento::EMBARCAR)) {
            log::getInstance()->logThrow(new Exception("Não é possivel embarcar agora"));
        }
    }


    // TESTED kinda
    function comprarPassagem(DocumentoPessoa $documentoPessoa, Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada, FranquiasDeBagagem $franquias, ?CodigoDoAssento $assento = null): ?RegistroDePassagem
    {
        $this->adicionarViagensEmVenda();
        if (!$this->passageiros->containsKey($documentoPessoa)) {
            log::getInstance()->logThrow(new Exception("Cliente nao cadastrado"));
        }

        /**
         * @var Passageiro $passageiro
         */
        $passageiro = $this->passageiros->get($documentoPessoa);
        /**
         * @var EncontrarMelhorVooStrategy $strategy
         */
        $strategy = new EncontrarMelhorVooPreferindoSemConexaoEEmPelaMenorTarifaStrategy();
        $voos = $strategy->encontrar($passageiro instanceof PassageiroVip, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias, $this->tarifa_franquia, $this->voos_planejados);

        if (count($voos) == 0) {
            return log::getInstance()->logCall(null);
        }

        /**
         * @var ViagemBuilder[] $viagem_builders
         */
        $viagem_builders = array_map(function (CodigoVoo $codigoVoo) use ($data) {
            # Exception not tested
            return $this->findRequiredViagemBuilderByDataECodigoVoo($data, $codigoVoo);
        }, $voos);

        foreach ($viagem_builders as $viagem_builder) {
            if (!$viagem_builder->getData()->eq($data)) {
                log::getInstance()->logThrow(new Exception("spookie dookie"));
            }
            if ($assento === null) {
                if (!$viagem_builder->temAssentosLiberados()) {
                    # Not tested
                    return log::getInstance()->logCall(null);
                }
            } else if (!$viagem_builder->assentoEstaLiberado($assento)) {
                # Not tested
                return log::getInstance()->logCall(null);
            }
            if (!$viagem_builder->temCargaDisponivelParaFranquias($franquias)) {
                # Not tested
                return log::getInstance()->logCall(null);
            }
        }

        $registro_passagem = $this->gerador_de_registro_de_passagem->gerar();
        /**
         * @var HashMapEntry<RegistroDeViagem, CodigoDoAssento>[] $viagens_assentos
         */
        $viagens_assentos = [];
        $valor_total = 0.0;

        foreach ($viagem_builders as $viagem_builder) {
            $assento_desejado = $assento ?? $viagem_builder->codigoAssentoLiberado();
            $valor = $viagem_builder->reservarAssento($passageiro instanceof PassageiroVip, $registro_passagem, $franquias, $assento_desejado);
            $valor_total += $valor;
            $viagens_assentos[] = new HashMapEntry($viagem_builder->getRegistro(), $assento_desejado);
        }

        $status = new PassagemCheckInNaoAberto();
        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        $delta = $data->dt($hoje);
        if ($delta->st($twoDays)) {
            $status = $status->abrir_check_in();
        }

        $primeiro_voo = $voos[0];
        $ultimo_voo = end($voos);
        $passagem = new Passagem(
            $registro_passagem,
            $this->voos_planejados->get($primeiro_voo)->getAeroportoSaida(),
            $this->voos_planejados->get($ultimo_voo)->getAeroportoChegada(),
            $this->sigla,
            $documentoPessoa,
            $data,
            $valor_total,
            0,
            $viagens_assentos,
            DataTempo::agora(),
            $status
        );

        $passageiro->addPassagem($registro_passagem);
        $this->passagens->put($registro_passagem, $passagem);
        return log::getInstance()->logCall($registro_passagem);
    }

    /**
     * Procura de ViagemBuilder
     */

    /** Remove um viagem builder pelo RegistroDeViagem e arremessa Exception se não encontrar
     * @param RegistroDeViagem $registroDeViagem
     * @return ViagemBuilder
     * @throws Exception
     */
    // TESTED
    private function removeViagemBuilder(RegistroDeViagem $registroDeViagem): ViagemBuilder
    {
        $vb = $this->findRequiredViagemBuilder($registroDeViagem);
        $data = $vb->getData();
        if (!$this->voos_em_venda->get($data)->remove($registroDeViagem)) {
            log::getInstance()->logThrow(new Exception("Não foi possivel remover o viagem builder"));
        }
        return log::getInstance()->logCall($vb);
    }

    /** Encontra um viagem builder pelo RegistroDeViagem e arremessa Exception se não encontrar
     * @param RegistroDeViagem $registroDeViagem
     * @param Data|null $data
     * @return ViagemBuilder
     */
    // TESTED
    private function findRequiredViagemBuilder(RegistroDeViagem $registroDeViagem, ?Data $data = null): ViagemBuilder
    {
        $vb = $this->findViagemBuilder($registroDeViagem, $data);
        if (is_null($vb)) {
            log::getInstance()->logThrow(new Exception("Viagem builder não encontrado"));
        }
        return log::getInstance()->logCall($vb);
    }

    /** Encontra um viagem builder pelo RegistroDeViagem e opcionalmente Data e retona null se não encontrar
     * @param RegistroDeViagem $registroDeViagem
     * @param Data|null $data
     * @return ViagemBuilder|null
     */
    // TESTED
    private function findViagemBuilder(RegistroDeViagem $registroDeViagem, ?Data $data = null): ?ViagemBuilder
    {
        if (!is_null($data)) {
            if (!$this->voos_em_venda->containsKey($data)) {
                return log::getInstance()->logCall(null);
            }
            /**
             * @var HashMap<RegistroDeViagem, ViagemBuilder> $registroViagemBuilder
             */
            $registroViagemBuilder = $this->voos_em_venda->get($data);
            if (!$registroViagemBuilder->containsKey($registroDeViagem)) {
                return log::getInstance()->logCall(null);
            }
            /**
             * @var ViagemBuilder $viagemBuilder
             */
            $viagemBuilder = $registroViagemBuilder->get($registroDeViagem);
            return log::getInstance()->logCall($viagemBuilder);
        }
        /**
         * @var HashMap<RegistroDeViagem, ViagemBuilder> $registroViagemBuilder
         */
        foreach ($this->voos_em_venda->values() as $registroViagemBuilder) {
            if ($registroViagemBuilder->containsKey($registroDeViagem)) {
                return log::getInstance()->logCall($registroViagemBuilder->get($registroDeViagem));
            }
        }
        return log::getInstance()->logCall(null);
    }

    /** Encontra um viagem builder pela Data e CodigoVoo e arremessa Exception se não encontrar
     * @param Data $data
     * @param CodigoVoo $codigoVoo
     * @return ViagemBuilder
     */
    // TESTED
    private function findRequiredViagemBuilderByDataECodigoVoo(Data $data, CodigoVoo $codigoVoo): ViagemBuilder
    {
        $vb = $this->findViagemBuilderByDataECodigoVoo($data, $codigoVoo);
        if (is_null($vb)) {
            log::getInstance()->logThrow(new Exception("Viagem builder não encontrado"));
        }
        return log::getInstance()->logCall($vb);
    }

    /** Encontra um viagem builder pela Data e CodigoVoo e retorna null se não encontrar
     * @param Data $data
     * @param CodigoVoo $codigoVoo
     * @return ViagemBuilder|null
     */
    // TESTED
    private function findViagemBuilderByDataECodigoVoo(Data $data, CodigoVoo $codigoVoo): ?ViagemBuilder
    {
        if (!$this->voos_em_venda->containsKey($data)) {
            return log::getInstance()->logCall(null);
        }
        $voos_em_venda_na_data = $this->voos_em_venda->get($data);
        /**
         * @var ViagemBuilder $viagemBuilder
         */
        foreach ($voos_em_venda_na_data->values() as $viagemBuilder) {
            if ($viagemBuilder->getCodigoDoVoo()->eq($codigoVoo)) {
                return log::getInstance()->logCall($viagemBuilder);
            }
        }
        return log::getInstance()->logCall(null);
    }

    /**
     * CRUD
     */

    // TESTED
    public function registrarVoo(
        int                $numero,
        SiglaAeroporto     $aeroporto_de_saida,
        SiglaAeroporto     $aeroporto_de_chegada,
        Tempo              $hora_de_partida,
        Duracao            $duracao_estimada,
        array              $dias_da_semana,
        RegistroDeAeronave $aeronave_padrao,
        float              $tarifa,
        int                $pontuacaoMilhagem,
    ): Voo
    {
        if (empty(Aeroporto::getRecordsBySigla($aeroporto_de_saida))) {
            throw new InvalidArgumentException("Aeroporto de saida não registrado");
        }
        if (empty(Aeroporto::getRecordsBySigla($aeroporto_de_chegada))) {
            throw new InvalidArgumentException("Aeroporto de chegada não registrado");
        }
        if (!$this->aeronaves->containsKey($aeronave_padrao)) {
            throw new InvalidArgumentException("Aeronave não registrada");
        }
        /**
         * @var Aeronave $aeronave
         */
        $aeronave = $this->aeronaves->get($aeronave_padrao);
        $codigoVoo = new CodigoVoo($this->sigla, $numero);
        if ($this->voos_planejados->containsKey($codigoVoo)) {
            throw new InvalidArgumentException("Voo já presente");
        }
        $voo = new Voo(
            $codigoVoo,
            $aeroporto_de_saida,
            $aeroporto_de_chegada,
            $hora_de_partida,
            $duracao_estimada,
            $dias_da_semana,
            $aeronave_padrao,
            $aeronave->getCapacidadePassageiros(),
            $aeronave->getCapacidadeCarga(),
            $tarifa,
            $pontuacaoMilhagem
        );
        # Adicionar aos voos planejados
        $this->voos_planejados->put($voo->getCodigo(), $voo);
        # Adicionar aos voos em venda que já estão presentes
        foreach ($this->voos_em_venda->entries() as $entry) {
            $data = $entry->key;
            $viagem_builders = $entry->value;
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
                continue;
            }
            $viagem_builder = (new ViagemBuilder())
                ->addTarifaFranquia($this->tarifa_franquia)
                ->adicionarGeradorDeRegistro($this->gerador_de_registro_de_viagem)
                ->gerarRegistro()
                ->addData($data)
                ->addVoo($voo);

            $registro_da_viagem = $viagem_builder->getRegistro();
            $viagem_builders->put($registro_da_viagem, $viagem_builder);
        }
        return log::getInstance()->logCall($voo);
    }

    // TESTED
    public function encontrarVoo(CodigoVoo $voo): ?Voo
    {
        return log::getInstance()->logCall($this->voos_planejados->get($voo));
    }

    // TESTED
    public function adicionarPassageiro(Passageiro $passageiro): void
    {
        if ($this->passageiros->containsKey($passageiro->getDocumento())) {
            log::getInstance()->logThrow(new Exception("Passageiro já presente"));
        }
        $passageiroClonado = clone $passageiro;
        $this->passageiros->put($passageiro->getDocumento(), $passageiroClonado);
        log::getInstance()->logCall(null);
    }

    // TESTED
    public function encontrarPassageiro(DocumentoPessoa $passageiro): ?Passageiro
    {
        return log::getInstance()->logCall($this->passageiros->get($passageiro));
    }

    // TESTED
    public function registrarTripulante(
        string          $nome,
        string          $sobrenome,
        DocumentoPessoa $documento,
        Nacionalidade   $nacionalidade,
        ?CPF            $cpf,
        Data            $data_de_nascimento,
        Email           $email,
        string          $cht,
        Endereco        $endereco,
        SiglaAeroporto  $aeroporto_base,
        Cargo           $cargo,
    ): Tripulante
    {
        // TODO: Check for CHT and DocumentoPessoa
        $registro = $this->gerador_de_registro_de_tripulante->gerar();
        $tripulante = new Tripulante(
            $nome,
            $sobrenome,
            $documento,
            $nacionalidade,
            $cpf,
            $data_de_nascimento,
            $email,
            $cht,
            $endereco,
            $this->sigla,
            $aeroporto_base,
            $cargo,
            $registro,
        );
        $this->tripulantes->put($registro, $tripulante);
        return log::getInstance()->logCall($tripulante);
    }

    // TESTED
    public function encontrarTripulante(RegistroDeTripulante $tripulante): ?Tripulante
    {
        return log::getInstance()->logCall($this->tripulantes->get($tripulante));
    }


    // TESTED EXCEPTION
    public function encontrarViagem(RegistroDeViagem $registroDeViagem): ?Viagem
    {
        $vb = $this->findViagemBuilder($registroDeViagem);
        if (!is_null($vb)) {
            log::getInstance()->logThrow(new Exception("A viagem ainda não foi executada"));
        }
        return log::getInstance()->logCall($this->voos_executados->get($registroDeViagem));
    }

    // TESTED
    public function registrarAeronave(
        string             $fabricante,
        string             $modelo,
        int                $capacidade_passageiros,
        float              $capacidade_carga,
        RegistroDeAeronave $registro
    ): Aeronave
    {
        if ($this->aeronaves->containsKey($registro)) {
            log::getInstance()->logThrow(new InvalidArgumentException("Aeronave já presente"));
        }
        $aeronave = new Aeronave(
            $this->sigla,
            $fabricante,
            $modelo,
            $capacidade_passageiros,
            $capacidade_carga, $registro
        );
        $this->aeronaves->put($registro, $aeronave);
        return log::getInstance()->logCall($aeronave);
    }

    // TESTED
    public function encontrarAeronave(RegistroDeAeronave $registro): ?Aeronave
    {
        return log::getInstance()->logCall($this->aeronaves->get($registro));
    }

    // TESTED
    public function encontrarPassagem(RegistroDePassagem $registro): ?Passagem
    {
        return log::getInstance()->logCall($this->passagens->get($registro));
    }

    public function getOnibus(RegistroDeViagem $registroDeViagem): Onibus {
        return log::getInstance()->logCall($this->findRequiredViagemBuilder($registroDeViagem)->getOnibus());
    }

    /**
     * @param SiglaCompanhiaAerea $sigla
     * @return CompanhiaAerea[]
     */
    static public function getRecordsBySigla(SiglaCompanhiaAerea $sigla): array
    {
        return parent::getRecordsByField("sigla", $sigla);
    }
}

?>