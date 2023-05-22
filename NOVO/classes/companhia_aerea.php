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
    private string $sigla;
    private array $aeronaves;
    private array $voos_planejados;
    private array $voos_em_venda;
    private array $voos_executados;
    private GeradorDeRegistroDeViagem $gerador_de_registro_de_viagem;
    private GeradorDeRegistroDePassagem $gerador_de_registro_de_passagem;
    private float $tarifa_franquia;
    private array $passagens;
    private array $passageiros;
    private array $tripulantes;
    private static $local_filename = "companhia_aerea.txt";

    public function __construct(string                      $nome,
                                string                      $codigo,
                                string                      $razao_social,
                                string                      $sigla,
                                array                       $aeronaves,
                                array                       $voos_planejados,
                                array                       $voos_em_venda,
                                array                       $voos_executados,
                                GeradorDeRegistroDeViagem   $gerador_de_registro_de_viagem,
                                GeradorDeRegistroDePassagem $gerador_de_registro_de_passagem,
                                float                       $tarifa_franquia,
                                array                       $passagens,
                                array                       $passageiros,
                                array                       $tripulantes,
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

    public function getAeronaves(): array
    {
        return $this->aeronaves;
    }

    public function getVoosPlanejados(): array
    {
        return $this->voos_planejados;
    }

    public function getVoosEmVenda(): array
    {
        return $this->voos_em_venda;
    }

    public function getVoosExecutados(): array
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

    public function getPassagens(): array
    {
        return $this->passagens;
    }

    public function getPassageiros(): array
    {
        return $this->passageiros;
    }

    public function getTripulantes(): array
    {
        return $this->tripulantes;
    }

    public static function getFilename()
    {
        return self::$local_filename;
    }

    public function registrar_que_viagem_aconteceu($hora_de_partida, $hora_de_chegada, $registro_de_viagem)
    {
        $fabrica = null;
        foreach ($this->voos_em_venda as $registro_fabrica) {
            if (array_key_exists($registro_de_viagem, $registro_fabrica)) {
                $fabrica = $registro_fabrica[$registro_de_viagem];
                unset($registro_fabrica[$registro_de_viagem]);
                break;
            }
        }
        if ($fabrica === null)
            throw new Exception("Fabrica não encontrada");

        $fabrica->add_hora_de_partida_e_hora_de_chegada($hora_de_partida, $hora_de_chegada);
        $viagem = $fabrica->build();
        $this->voos_executados[$viagem->registro] = $viagem;
        foreach ($viagem->assentos as $assento) {
            if ($assento->vazio())
                continue;

            $registro_passagem = $assento->passagem;
            $passagem = $this->passagens[$registro_passagem];
            $passagem->acionar_evento(Evento::CONCLUIR);
        }
    }

    private function _encontrar_voos_sem_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada)
    {
        $voos = [];

        $voo_desejado = function ($voo) use ($data, $aeroporto_de_saida, $aeroporto_de_chegada) {
            if (!in_array($data->dia_da_semana, $voo->dias_da_semana))
                return false;
            if ($aeroporto_de_saida != $voo->aeroporto_de_saida)
                return false;
            if ($aeroporto_de_chegada != $voo->aeroporto_de_chegada)
                return false;
            return true;
        };

        foreach (array_filter($this->voos_planejados, $voo_desejado) as $voo) {
            $voos[] = $voo->codigo;
        }

        return $voos;
    }

    private function _encontrar_voos_com_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada)
    {
        $voos = [];

        $voo_intermediario_desejado = function ($voo) use ($data, $aeroporto_de_saida) {
            if (!in_array($data->dia_da_semana, $voo->dias_da_semana)) {
                return false;
            }
            if ($aeroporto_de_saida != $voo->aeroporto_de_saida) {
                return false;
            }
            return true;
        };

        $voo_final_desejado = function ($voo, $hora_de_chegada) use ($data, $aeroporto_de_chegada) {
            if (!in_array($data->dia_da_semana, $voo->dias_da_semana)) {
                return false;
            }
            if ($voo->aeroporto_de_chegada != $aeroporto_de_chegada) {
                return false;
            }
            $tempo_da_conexao = $hora_de_chegada + Duracao::meia_hora();
            if ($voo->hora_de_partida > $tempo_da_conexao) {
                return false;
            }
            return true;
        };

        foreach (array_filter($this->voos_planejados, $voo_intermediario_desejado) as $voo_intermediario) {
            foreach (array_filter($this->voos_planejados, function ($voo_final) use ($voo_intermediario) {
                $voo_final_desejado = [$voo_final, $voo_intermediario->hora_de_partida + $voo_intermediario->duracao_estimada];
                return $voo_final_desejado;
            }) as $voo_final) {
                $voos[] = [$voo_intermediario->codigo, $voo_final->codigo];
            }
        }

        return $voos;
    }

    function _encontrar_melhor_voo($cliente_vip, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias)
    {
        $voos_sem_conexao = $this->_encontrar_voos_sem_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada);
        $melhor_tarifa = INF;

        if (count($voos_sem_conexao) != 0) {
            $melhor_voo = null;

            foreach ($voos_sem_conexao as $codigo_voo) {
                $voo = $this->voos_planejados[$codigo_voo];
                $tarifa = $voo->calcula_tarifa($cliente_vip, $franquias, $this->tarifa_franquia);

                if ($tarifa >= $melhor_tarifa)
                    continue;

                $melhor_voo = $voo;
                $melhor_tarifa = $tarifa;
            }

            return [$melhor_voo->codigo];
        }

        $melhor_tarifa = INF;
        $pares_de_voos = $this->_encontrar_voos_com_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada);

        if (count($pares_de_voos) == 0) {
            return [];
        }

        $melhor_voo_intermediario = null;
        $melhor_voo_final = null;

        foreach ($pares_de_voos as $par_de_voos) {
            [$codigo_voo_intermediario, $codigo_voo_final] = $par_de_voos;
            $voo_intermediario = $this->voos_planejados[$codigo_voo_intermediario];
            $voo_final = $this->voos_planejados[$codigo_voo_final];
            $tarifa_intermediario = $voo_intermediario->calcula_tarifa($cliente_vip, $franquias, $this->tarifa_franquia);
            $tarifa_final = $voo_final->calcula_tarifa($cliente_vip, $franquias, $this->tarifa_franquia);
            $tarifa = $tarifa_intermediario + $tarifa_final;

            if ($tarifa >= $melhor_tarifa) {
                continue;
            }

            $melhor_voo_intermediario = $voo_intermediario;
            $melhor_voo_final = $voo_final;
            $melhor_tarifa = $tarifa;
        }

        return [$melhor_voo_intermediario->codigo, $melhor_voo_final->codigo];
    }

    function adicionar_viagens_em_venda(): void
    {
        $datas_atuais = array_keys($this->voos_em_venda);
        $hoje = Data::hoje();
        $datas_alvo = array_map(function ($i) use ($hoje) {
            $data = $hoje->add(Duracao::umDia()->mul($i));
            return "{$data}";
        }, range(0, 30));
        $datas_nao_preenchidas = array_diff($datas_alvo, $datas_atuais);

        foreach ($datas_nao_preenchidas as $dataString) {
            $data = Data::fromString($dataString);
            $voos_nesse_dia_da_semana = array_filter($this->voos_planejados, function ($voo) use ($data) {
                return in_array($data->getDiaDaSemana(), $voo->dias_da_semana);
            });
            $viagens_nesse_dia_da_semana = $this->voos_em_venda["{$data}"] = [];

            foreach ($voos_nesse_dia_da_semana as $voo_que_ira_acontecer) {
                $viagem_factory = (new ViagemBuilder())
                    ->add_tarifa_franquia($this->tarifa_franquia)
                    ->adicionar_gerador_de_registro($this->gerador_de_registro_de_viagem)
                    ->gerar_registro()
                    ->add_data($data)
                    ->add_voo($voo_que_ira_acontecer);

                $registro_da_viagem = $viagem_factory->registro;
                $viagens_nesse_dia_da_semana["{$registro_da_viagem}"] = $viagem_factory;
            }
        }
    }

    function cancelar_passagem(RegistroDePassagem $passagem): void
    {
        if (!isset($this->passagens["{$passagem}"])) {
            throw new Exception("Passagem não está na companhia");
        }

        $passagem = $this->passagens["{$passagem}"];

        if (!$passagem->acionar_evento(Evento::CANCELAR)) {
            throw new Exception("A passagem não pode ser cancelada agora");
        }

        $data = $passagem->getData();

        foreach ($passagem->assentos as $viagem => $assento) {
            $voos_em_venda_na_data = $this->voos_em_venda["{$data}"];

            if (!isset($voos_em_venda_na_data[$viagem])) {
                throw new Exception("Não é possível cancelar uma viagem que já ocorreu");
            }

            $viagem_factory = $voos_em_venda_na_data[$viagem];
            $viagem_factory->liberar_assento($passagem->registro, $assento);
        }
    }

    function acessar_historico_de_viagens(DocumentoPassageiro $passageiro)
    {
        if (!isset($this->passageiros["{$passageiro}"])) {
            throw new Exception("Passageiro nao cadastrado");
        }

        $passageiro = $this->passageiros["{$passageiro}"];
        $registros_de_passagens = $passageiro->passagens;
        $passagens = array_map(function ($passagem) {
            return $this->passagens["{$passagem}"];
        }, $registros_de_passagens);

        $viagens = [];

        foreach ($passagens as $passagem) {
            $registros_de_viagens = array_keys($passagem->assentos);
            $viagens_na_passagem = array_map(function ($registro_de_viagem) {
                return $this->voos_executados[$registro_de_viagem];
            }, $registros_de_viagens);

            foreach ($viagens_na_passagem as $viagem) {
                $viagens[] = $viagem;
            }
        }
        function viagem_cmp(Viagem $a, Viagem $b): int
        {
            if ($a->eq($b)) {
                return 0;
            }
            return $a->st($b) ? -1 : 1;
        }

        usort($viagens, function (Viagem $a, Viagem $b): int
        {
            if ($a->eq($b)) {
                return 0;
            }
            return $a->st($b) ? -1 : 1;
        });
        return $viagens;
    }

    function abrir_check_in_para_passagens(...$args)
    {
        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        if (!empty($args)) {
            foreach ($args as $registro_passagem) {
                if (!isset($this->passagens["{$registro_passagem}"]))
                    throw new Exception("Passagem não está na companhia");

                $passagem = $this->passagens["{$registro_passagem}"];

                $delta = $passagem->getData()->dt($hoje);
                if ($delta->st($twoDays)) {
                    continue;
                }

                throw new Exception("Passagem está a mais de 48h de distância");
            }

            foreach ($args as $registro_passagem) {
                $passagem = $this->passagens["{$registro_passagem}"];
                $passagem->acionar_evento(Evento::ABRIR_CHECK_IN);
            }

            return;
        }

        foreach ($this->passagens as $passagem) {
            $delta = $passagem->getData()->dt($hoje);
            if ($delta->st($twoDays)) {
                $passagem->acionar_evento(Evento::ABRIR_CHECK_IN);
            }
        }
    }

    function fazer_check_in(RegistroDePassagem $passagem)
    {
        if (!isset($this->passagens["{$passagem}"]))
            throw new Exception("Passagem não está na companhia");

        $passagem = $this->passagens["{$passagem}"];

        $twoDays = new Duracao(2, 0);
        $hoje = Data::hoje();
        $delta = $passagem->getData()->dt($hoje);
        if ($delta->st($twoDays)) {
            $passagem->acionar_evento(Evento::ABRIR_CHECK_IN);
        }

        if (!$passagem->acionar_evento(Evento::FAZER_CHECK_IN))
            throw new Exception("Não é possível fazer check-in agora");
    }

    function comprar_passagem(DocumentoPassageiro $id_cliente, Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada, FranquiasDeBagagem $franquias, ?CodigoDoAssento $assento = null)
    {
        if (!isset($this->passageiros["{$id_cliente}"])) {
            throw new Exception("Cliente nao cadastrado");
        }

        $cliente = $this->passageiros["{$id_cliente}"];
        $voos = $this->_encontrar_melhor_voo($cliente->vip, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias);

        if (count($voos) == 0) {
            return null;
        }

        $viagem_factories = array_map(function ($codigo_voo) use ($data) {
            $viagem_factories = $this->voos_em_venda["{$data}"];

            foreach ($viagem_factories as $viagem_factory) {
                if ($viagem_factory->codigo_do_voo == $codigo_voo) {
                    return $viagem_factory;
                }
            }

            return null;
        }, $voos);

        if (count(array_filter($viagem_factories, function ($viagem_factory) {
                return $viagem_factory == null;
            })) != 0) {
            return null;
        }

        foreach ($viagem_factories as $viagem_factory) {
            if ($assento === null) {
                if (!$viagem_factory->tem_assentos_liberados()) {
                    return null;
                }
            } elseif (!$viagem_factory->assento_esta_liberado($assento)) {
                return null;
            }

            if (!$viagem_factory->tem_carga_disponivel_para_franquias($franquias)) {
                return null;
            }
        }

        $registro_passagem = $this->gerador_de_registro_de_passagem->gerar();
        $viagens_assentos = [];
        $valor_total = 0;

        foreach ($viagem_factories as $viagem_factory) {
            $assento_desejado = $assento ?? $viagem_factory->codigo_assento_liberado();
            $valor = $viagem_factory->reservar_assento($cliente->vip, $registro_passagem, $franquias, $assento_desejado);
            $valor_total += $valor;
            $viagens_assentos["{$viagem_factory->registro}"] = $assento_desejado;
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
            $this->voos_planejados["{$primeiro_voo}"]->aeroporto_de_saida,
            $this->voos_planejados["{$ultimo_voo}"]->aeroporto_de_chegada,
            $this->sigla,
            $id_cliente,
            $data,
            $valor_total,
            0,
            $viagens_assentos,
            DataTempo::agora(),
            $status
        );

        $cliente->passagens[] = $passagem->registro;
        $this->passagens[$registro_passagem] = $passagem;
        return $passagem->registro;
    }
}

?>