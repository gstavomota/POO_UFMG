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

use \Temporal\Data;
use \Temporal\Duracao;
use \Temporal\DataTempo;
use \Temporal\Tempo;

use \Identificadores\SiglaCompanhiaAerea;
use \Identificadores\RegistroDeAeronave;
use \Identificadores\CodigoVoo;
use \Identificadores\RegistroDeViagem;
use \Identificadores\GeradorDeRegistroDeViagem;
use \Identificadores\SiglaAeroporto;
use \Identificadores\DocumentoPassageiro;
use \Identificadores\GeradorDeRegistroDePassagem;
use \Identificadores\CodigoDoAssento;
use \Identificadores\RegistroDePassagem;

use \Passagem\Passagem;
use \Passagem\StatusDaPassagem;
use \Passagem\PassagemCheckInNaoAberto;

use \Persist\Persist;

use \Aeronave\Aeronave;

use \Viagem\Viagem;

use \ViagemBuilder\ViagemBuilder;

use \Voo\Voo;

class CompanhiaAerea extends Persist {
    public $nome;
    public $codigo;
    public $razao_social;
    public $sigla;
    public $aeronaves;
    public $voos_planejados;
    public $voos_em_venda;
    public $voos_executados;
    public $gerador_de_registro_de_viagem;
    public $gerador_de_registro_de_passagem;
    public $tarifa_franquia;
    public $passagens;
    public $passageiros;
    public $tripulantes;
    private static $local_filename = "companhia_aerea.txt";

    public function __construct($nome, $codigo, $razao_social, $sigla,
        $aeronaves, $voos_planejados, $voos_em_venda, $voos_executados,
        $gerador_de_registro_de_viagem, $gerador_de_registro_de_passagem, $tarifa_franquia, 
        $passagens, $passageiros, $tripulantes, ...$args) {
            
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

    public static function get_filename() {
        return self::$local_filename;
    }

    public function registrar_que_viagem_aconteceu($hora_de_partida, $hora_de_chegada, $registro_de_viagem) {
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
            $passagem->acionar_evento(StatusDaPassagem::Evento_CONCLUIR);
        }
    }

    private function _encontrar_voos_sem_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada) {
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

    private function _encontrar_voos_com_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada) {
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
                $voo_final_desejado =  [$voo_final, $voo_intermediario->hora_de_partida + $voo_intermediario->duracao_estimada];
                return $voo_final_desejado;
            }) as $voo_final) {
                $voos[] = [$voo_intermediario->codigo, $voo_final->codigo];
            }
        }

        return $voos;
    }
    function _encontrar_melhor_voo($cliente_vip, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias) {
        $voos_sem_conexao = $this->_encontrar_voos_sem_conexao($data, $aeroporto_de_saida, $aeroporto_de_chegada);
        $infinity = 1 / 0;
        $melhor_tarifa = $infinity;
    
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
    
        $infinity = 1 / 0;
        $melhor_tarifa = $infinity;
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

    function adicionar_viagens_em_venda() {
        $datas_atuais = array_keys($this->voos_em_venda);
        $hoje = Data::now();
        $datas_alvo = array_map(function ($i) use ($hoje) {
            return $hoje->add(Duracao::um_dia()->multiply($i));
        }, range(30));
        $datas_nao_preenchidas = array_diff($datas_alvo, $datas_atuais);
    
        foreach ($datas_nao_preenchidas as $data) {
            $voos_nesse_dia_da_semana = array_filter($this->voos_planejados, function ($voo) use ($data) {
                return in_array($data->dia_da_semana, $voo->dias_da_semana);
            });
            $viagens_nesse_dia_da_semana = $this->voos_em_venda[$data] = [];
    
            foreach ($voos_nesse_dia_da_semana as $voo_que_ira_acontecer) {
                $viagem_factory = (new ViagemBuilder())
                    ->add_tarifa_franquia($this->tarifa_franquia)
                    ->adicionar_gerador_de_registro($this->gerador_de_registro_de_viagem)
                    ->gerar_registro()
                    ->add_data($data)
                    ->add_voo($voo_que_ira_acontecer);
    
                $registro_da_viagem = $viagem_factory->registro;
                $viagens_nesse_dia_da_semana[$registro_da_viagem] = $viagem_factory;
            }
        }
    }
    
    function cancelar_passagem($passagem) {
        if (!isset($this->passagens[$passagem])) {
            throw new Exception("Passagem não está na companhia");
        }
    
        $passagem = $this->passagens[$passagem];
    
        if (!$passagem->acionar_evento(StatusDaPassagem::Evento_CANCELAR)) {
            throw new Exception("A passagem não pode ser cancelada agora");
        }
    
        $data = $passagem->data;
    
        foreach ($passagem->assentos as $viagem => $assento) {
            $voos_em_venda_na_data = $this->voos_em_venda[$data];
    
            if (!isset($voos_em_venda_na_data[$viagem])) {
                throw new Exception("Não é possível cancelar uma viagem que já ocorreu");
            }
    
            $viagem_factory = $voos_em_venda_na_data[$viagem];
            $viagem_factory->liberar_assento($passagem->registro, $assento);
        }
    }
    function acessar_historico_de_viagens($passageiro) {
        if (!isset($this->passageiros[$passageiro])) {
            throw new Exception("Passageiro nao cadastrado");
        }
    
        $passageiro = $this->passageiros[$passageiro];
        $registros_de_passagens = $passageiro->passagens;
        $passagens = array_map(function ($passagem) {
            return $this->passagens[$passagem];
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
    
        sort($viagens);
        return $viagens;
    }
    
    function abrir_check_in_para_passagens(...$args) {
        if (!empty($args)) {
            foreach ($args as $registro_passagem) {
                if (!isset($this->passagens[$registro_passagem])) 
                    throw new Exception("Passagem não está na companhia");
    
                $passagem = $this->passagens[$registro_passagem];
    
                if ($passagem->data->diff(Data::now())->days < 2) 
                    continue;
    
                throw new Exception("Passagem está a mais de 48h de distância");
            }
    
            foreach ($args as $registro_passagem) {
                $passagem = $this->passagens[$registro_passagem];
                $passagem->acionar_evento(StatusDaPassagem::Evento_ABRIR_CHECK_IN);
            }
    
            return;
        }
    
        foreach ($this->passagens as $passagem) {
            if ($passagem->data->diff(Data::now())->days < 2) 
                $passagem->acionar_evento(StatusDaPassagem::Evento_ABRIR_CHECK_IN);
        }
    }
    
    function fazer_check_in($passagem) {
        if (!isset($this->passagens[$passagem])) 
            throw new Exception("Passagem não está na companhia");
    
        $passagem = $this->passagens[$passagem];
    
        if ($passagem->data->diff(Data::now())->days < 2) 
            $passagem->acionar_evento(StatusDaPassagem::Evento_ABRIR_CHECK_IN);
    
        if (!$passagem->acionar_evento(StatusDaPassagem::Evento_FAZER_CHECK_IN)) 
            throw new Exception("Não é possível fazer check-in agora");
    }
    function comprar_passagem($id_cliente, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias, $assento = null) {
        if (!isset($this->passageiros[$id_cliente])) {
            throw new Exception("Cliente nao cadastrado");
        }
    
        $cliente = $this->passageiros[$id_cliente];
        $voos = $this->_encontrar_melhor_voo($cliente->vip, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias);
    
        if (count($voos) == 0) {
            return null;
        }
    
        $viagem_factories = array_map(function ($codigo_voo) use ($data) {
            $viagem_factories = $this->voos_em_venda[$data];
    
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
            $viagens_assentos[$viagem_factory->registro] = $assento_desejado;
        }
    
        $status = new PassagemCheckInNaoAberto();
    
        if ($data->diff(Data::now())->days < 2) {
            $status = $status->abrir_check_in();
        }
    
        $passagem = new Passagem(
            $registro_passagem,
            $this->voos_planejados[$voos[0]]->aeroporto_de_saida,
            $this->voos_planejados[end($voos)]->aeroporto_de_chegada,
            $this->sigla,
            $id_cliente,
            $data,
            $valor_total,
            0,
            $viagens_assentos,
            DataTempo::now(),
            $status
        );
    
        $cliente->passagens[] = $passagem->registro;
        $this->passagens[$registro_passagem] = $passagem;
        return $passagem->registro;
    }  
}  
?>