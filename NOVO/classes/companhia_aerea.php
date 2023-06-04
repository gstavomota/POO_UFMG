<?php

require_once 'aeronave.php';
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
     * @var HashMap<DocumentoPassageiro, Passageiro>
     */
    private HashMap $passageiros;
    /**
     * @var HashMap<RegistroDeTripulante, Tripulante>
     */
    private HashMap $tripulantes;
    private static $local_filename = "companhia_aerea.txt";

    public function __construct(string                      $nome,
                                string                      $codigo,
                                string                      $razao_social,
                                SiglaCompanhiaAerea         $sigla,
                                HashMap                     $aeronaves,
                                HashMap                     $voos_planejados,
                                HashMap                     $voos_em_venda,
                                HashMap                     $voos_executados,
                                GeradorDeRegistroDeViagem   $gerador_de_registro_de_viagem,
                                GeradorDeRegistroDePassagem $gerador_de_registro_de_passagem,
                                float                       $tarifa_franquia,
                                HashMap                     $passagens,
                                HashMap                     $passageiros,
                                HashMap                     $tripulantes,
                                                            ...$args)
    {

        $this->nome = $nome;
        $this->codigo = $codigo;
        $this->razao_social = $razao_social;
        $this->sigla = $sigla;
        $this->aeronaves = $aeronaves;
        $this->voos_planejados = $voos_planejados;
        $this->voos_em_venda = $voos_em_venda;
        $this->voos_executados = $voos_executados;
        $this->gerador_de_registro_de_viagem = $gerador_de_registro_de_viagem;
        $this->gerador_de_registro_de_passagem = $gerador_de_registro_de_passagem;
        $this->tarifa_franquia = $tarifa_franquia;
        $this->passagens = $passagens;
        $this->passageiros = $passageiros;
        $this->tripulantes = $tripulantes;
        parent::__construct(...$args);
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getRazaoSocial(): string
    {
        return $this->razao_social;
    }

    public function getSigla(): string
    {
        return $this->sigla;
    }

    public function getAeronaves(): HashMap
    {
        return $this->aeronaves;
    }

    public function getVoosPlanejados(): HashMap
    {
        return $this->voos_planejados;
    }

    public function getVoosEmVenda(): HashMap
    {
        return $this->voos_em_venda;
    }

    public function getVoosExecutados(): HashMap
    {
        return $this->voos_executados;
    }

    public function getGeradorDeRegistroDeViagem(): GeradorDeRegistroDeViagem
    {
        return $this->gerador_de_registro_de_viagem;
    }

    public function getGeradorDeRegistroDePassagem(): GeradorDeRegistroDePassagem
    {
        return $this->gerador_de_registro_de_passagem;
    }

    public function getTarifaFranquia(): float
    {
        return $this->tarifa_franquia;
    }

    public function getPassagens(): HashMap
    {
        return $this->passagens;
    }

    public function getPassageiros(): HashMap
    {
        return $this->passageiros;
    }

    public function getTripulantes(): HashMap
    {
        return $this->tripulantes;
    }

    public static function getFilename()
    {
        return self::$local_filename;
    }

    public function registrar_que_viagem_aconteceu(DataTempo $hora_de_partida, DataTempo $hora_de_chegada, RegistroDeViagem $registro_de_viagem)
    {
        /**
         * @var null|ViagemBuilder $fabrica
         */
        $fabrica = null;
        /**
         * @var HashMap<Data, ViagemBuilder> $registro_fabrica
         */
        foreach ($this->voos_em_venda->values() as $registro_fabrica) {
            if ($registro_fabrica->containsKey($registro_de_viagem)) {
                $fabrica = $registro_fabrica->get($registro_de_viagem);
                $registro_fabrica->remove($registro_de_viagem);
                break;
            }
        }
        if (is_null($fabrica)) {
            throw new Exception("Fabrica não encontrada");
        }

        $fabrica->addHoraDePartidaEHoraDeChegada($hora_de_partida, $hora_de_chegada);
        $viagem = $fabrica->build();
        $this->voos_executados->put($viagem->getRegistro(), $viagem);
        /**
         * @var Assento $assento
         */
        foreach ($viagem->getAssentos()->values() as $assento) {
            if ($assento->vazio())
                continue;

            $registro_passagem = $assento->getPassagem();
            $passagem = $this->passagens->get($registro_passagem);
            $passagem->acionar_evento(Evento::CONCLUIR);
        }
    }

    /**
     * @param Data $data
     * @param SiglaAeroporto $aeroporto_de_saida
     * @param SiglaAeroporto $aeroporto_de_chegada
     * @return CodigoVoo[]
     * @throws EquatableTypeException
     */
    private function _encontrar_voos_sem_conexao(Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada): array
    {
        /**
         * @var CodigoVoo[] $voos
         */
        $voos = [];

        $voo_desejado = function (Voo $voo) use ($data, $aeroporto_de_saida, $aeroporto_de_chegada) {
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana()))
                return false;
            if (!$aeroporto_de_saida->eq($voo->getAeroportoSaida()))
                return false;
            if (!$aeroporto_de_chegada->eq($voo->getAeroportoChegada()))
                return false;
            return true;
        };

        /**
         * @var Voo $voo
         */
        foreach (array_filter($this->voos_planejados->values(), $voo_desejado) as $voo) {
            $voos[] = $voo->getCodigo();
        }

        return $voos;
    }

    /**
     * @param Data $data
     * @param SiglaAeroporto $aeroporto_de_saida
     * @param SiglaAeroporto $aeroporto_de_chegada
     * @return CodigoVoo[][]
     * @throws ComparableTypeException
     * @throws EquatableTypeException
     */
    private function _encontrar_voos_com_conexao(Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada): array
    {
        /**
         * @var CodigoVoo[][] $voos
         */
        $voos = [];

        $voo_intermediario_desejado = function (Voo $voo) use ($data, $aeroporto_de_saida) {
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
                return false;
            }
            if (!$aeroporto_de_saida->eq($voo->getAeroportoSaida())) {
                return false;
            }
            return true;
        };

        $voo_final_desejado = function (Voo $voo, Tempo $hora_de_chegada) use ($data, $aeroporto_de_chegada) {
            if (!in_array($data->getDiaDaSemana(), $voo->getDiasDaSemana())) {
                return false;
            }
            if (!$voo->getAeroportoChegada()->eq($aeroporto_de_chegada)) {
                return false;
            }
            $tempo_da_conexao = $hora_de_chegada->add(Duracao::meiaHora());
            if ($voo->getHoraDePartida()->gt($tempo_da_conexao)) {
                return false;
            }
            return true;
        };

        /**
         * @var Voo $voo_intermediario
         */
        foreach (array_filter($this->voos_planejados->values(), $voo_intermediario_desejado) as $voo_intermediario) {
            /**
             * @var Voo $voo_final
             */
            foreach (array_filter($this->voos_planejados->values(), $voo_final_desejado) as $voo_final) {
                $voos[] = [$voo_intermediario->getCodigo(), $voo_final->getCodigo()];
            }
        }

        return $voos;
    }

    /**
     * @param bool $cliente_vip
     * @param Data $data
     * @param SiglaAeroporto $aeroporto_de_saida
     * @param SiglaAeroporto $aeroporto_de_chegada
     * @param FranquiasDeBagagem $franquias
     * @return CodigoVoo[]
     * @throws ComparableTypeException
     * @throws EquatableTypeException
     */
    function _encontrar_melhor_voo(bool $cliente_vip, Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada, FranquiasDeBagagem $franquias)
    {
        /**
         * @var CodigoVoo[] $voos_sem_conexao
         */
        $voos_sem_conexao = $this->_encontrar_voos_sem_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada);
        $melhor_tarifa = INF;

        if (count($voos_sem_conexao) != 0) {
            /**
             * @var Voo $melhor_voo
             */
            $melhor_voo = null;

            /**
             * @var CodigoVoo $codigo_voo
             */
            foreach ($voos_sem_conexao as $codigo_voo) {
                /**
                 * @var Voo $voo
                 */
                $voo = $this->voos_planejados->get($codigo_voo);
                $tarifa = $voo->calculaTarifa($cliente_vip, $franquias, $this->tarifa_franquia);

                if ($tarifa >= $melhor_tarifa)
                    continue;

                $melhor_voo = $voo;
                $melhor_tarifa = $tarifa;
            }

            return [$melhor_voo->getCodigo()];
        }

        /**
         * @var CodigoVoo[][] $pares_de_voos
         */
        $pares_de_voos = $this->_encontrar_voos_com_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada);

        if (count($pares_de_voos) == 0) {
            return [];
        }

        /**
         * @var Voo $melhor_voo_intermediario
         */
        $melhor_voo_intermediario = null;

        /**
         * @var Voo $melhor_voo_final
         */
        $melhor_voo_final = null;

        foreach ($pares_de_voos as $par_de_voos) {
            [$codigo_voo_intermediario, $codigo_voo_final] = $par_de_voos;
            /**
             * @var Voo $voo_intermediario
             */
            $voo_intermediario = $this->voos_planejados->get($codigo_voo_intermediario);
            /**
             * @var Voo $voo_final
             */
            $voo_final = $this->voos_planejados->get($codigo_voo_final);
            $tarifa_intermediario = $voo_intermediario->calculaTarifa($cliente_vip, $franquias, $this->tarifa_franquia);
            $tarifa_final = $voo_final->calculaTarifa($cliente_vip, $franquias, $this->tarifa_franquia);
            $tarifa = $tarifa_intermediario + $tarifa_final;

            if ($tarifa >= $melhor_tarifa) {
                continue;
            }

            $melhor_voo_intermediario = $voo_intermediario;
            $melhor_voo_final = $voo_final;
            $melhor_tarifa = $tarifa;
        }

        return [$melhor_voo_intermediario->getCodigo(), $melhor_voo_final->getCodigo()];
    }

    function adicionar_viagens_em_venda(): void
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
    }

    function cancelar_passagem(RegistroDePassagem $passagem): void
    {
        if (!$this->passagens->containsKey($passagem)) {
            throw new Exception("Passagem não está na companhia");
        }

        $passagem = $this->passagens->get($passagem);

        if (!$passagem->acionar_evento(Evento::CANCELAR)) {
            throw new Exception("A passagem não pode ser cancelada agora");
        }

        $data = $passagem->getData();

        foreach ($passagem->getAssentos()->entries() as $entry) {
            /**
             * @var RegistroDeViagem $registro_viagem
             */
            $registro_viagem = $entry->key;
            /**
             * @var CodigoDoAssento $codigo_assento
             */
            $codigo_assento = $entry->value;
            /**
             * @var HashMap<RegistroDeViagem, ViagemBuilder> $voos_em_venda_na_data
             */
            $voos_em_venda_na_data = $this->voos_em_venda->get($data);

            if (!$voos_em_venda_na_data->containsKey($registro_viagem)) {
                throw new Exception("Não é possível cancelar uma viagem que já ocorreu");
            }

            $viagem_builder = $voos_em_venda_na_data->get($registro_viagem);
            $viagem_builder->liberarAssento($passagem->getRegistro(), $codigo_assento);
        }
    }

    function acessar_historico_de_viagens(DocumentoPassageiro $documentoPassageiro)
    {
        if (!$this->passageiros->containsKey($documentoPassageiro)) {
            throw new Exception("Passageiro nao cadastrado");
        }

        /**
         * @var Passageiro $passageiro
         */
        $passageiro = $this->passageiros->get($documentoPassageiro);
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

    function abrir_check_in_para_passagens(RegistroDePassagem ...$args)
    {
        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        if (!empty($args)) {
            foreach ($args as $registro_passagem) {
                if (!$this->passagens->containsKey($registro_passagem)) {
                    throw new Exception("Passagem não está na companhia");
                }

                /**
                 * @var Passagem $passagem
                 */
                $passagem = $this->passagens->get($registro_passagem);

                $delta = $passagem->getData()->dt($hoje);
                if ($delta->st($twoDays)) {
                    continue;
                }

                throw new Exception("Passagem está a mais de 48h de distância");
            }

            foreach ($args as $registro_passagem) {
                /**
                 * @var Passagem $passagem
                 */
                $passagem = $this->passagens->get($registro_passagem);
                $passagem->acionarEvento(Evento::ABRIR_CHECK_IN);
            }

            return;
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
    }

    function fazer_check_in(RegistroDePassagem $passagem)
    {
        if (!$this->passagens->containsKey($passagem)) {
            throw new Exception("Passagem não está na companhia");
        }

        /**
         * @var Passagem $passagem
         */
        $passagem = $this->passagens->get($passagem);

        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        $delta = $passagem->getData()->dt($hoje);
        if ($delta->st($twoDays)) {
            $passagem->acionarEvento(Evento::ABRIR_CHECK_IN);
        }

        if (!$passagem->acionarEvento(Evento::FAZER_CHECK_IN)) {
            throw new Exception("Não é possível fazer check-in agora");
        }
    }

    private function findViagemBuilderByCodigoVoo(CodigoVoo $codigo_voo, Data $data)
    {
        /** @var HashMap<RegistroDeViagem, ViagemBuilder> $viagem_builders */
        $viagem_builders = $this->voos_em_venda->get($data);

        /**
         * @var ViagemBuilder $viagem_builder
         */
        foreach ($viagem_builders->values() as $viagem_builder) {
            if ($viagem_builder->getCodigoDoVoo()->eq($codigo_voo)) {
                return $viagem_builder;
            }
        }

        throw new Exception("Didnt find the viagem builder");
    }

    function comprar_passagem(DocumentoPassageiro $documentoPassageiro, Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada, FranquiasDeBagagem $franquias, ?CodigoDoAssento $assento = null)
    {
        if (!$this->passageiros->containsKey($documentoPassageiro)) {
            throw new Exception("Cliente nao cadastrado");
        }

        /**
         * @var Passageiro $passageiro
         */
        $passageiro = $this->passageiros->get($documentoPassageiro);
        $voos = $this->_encontrar_melhor_voo($passageiro instanceof PassageiroVip, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias);

        if (count($voos) == 0) {
            return null;
        }

        /**
         * @var ViagemBuilder[] $viagem_builders
         */
        $viagem_builders = array_map(function (CodigoVoo $codigoVoo) use ($data) {
                return $this->findViagemBuilderByCodigoVoo($codigoVoo, $data);
        }, $voos);

        foreach ($viagem_builders as $viagem_builder) {
            if ($assento === null) {
                if (!$viagem_builder->temAssentosLiberados()) {
                    return null;
                }
            } else if (!$viagem_builder->assentoEstaLiberado($assento)) {
                return null;
            }

            if (!$viagem_builder->temCargaDisponivelParaFranquias($franquias)) {
                return null;
            }
        }

        $registro_passagem = $this->gerador_de_registro_de_passagem->gerar();
        /**
         * @var HashMap<RegistroDeViagem, CodigoDoAssento> $viagens_assentos
         */
        $viagens_assentos = new HashMap();
        $valor_total = 0.0;

        foreach ($viagem_builders as $viagem_builder) {
            $assento_desejado = $assento ?? $viagem_builder->codigoAssentoLiberado();
            $valor = $viagem_builder->reservarAssento($passageiro instanceof PassageiroVip, $registro_passagem, $franquias, $assento_desejado);
            $valor_total += $valor;
            $viagens_assentos->put($viagem_builder->getRegistro(), $assento_desejado);
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
            $documentoPassageiro,
            $data,
            $valor_total,
            0,
            $viagens_assentos,
            DataTempo::agora(),
            $status
        );

        $passageiro->addPassagem($registro_passagem);
        $this->passagens->put($registro_passagem, $passagem);
        return $registro_passagem;
    }
}

?>