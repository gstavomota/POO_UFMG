<?php
require_once ('assento.php');
require_once ('calculo_tarifa_strategy.php');
require_once ('franquia_de_bagagem.php');
require_once ('identificadores.php');
require_once ('temporal.php');
require_once ('viagem.php');

use function assento\{construir_assentos};
use function franquia_de_bagagem\{carga};
use function identificadores\{gerar, registro_de_aeronave, registro_de_viagem};
use function calculo_tarifa_strategy_for as tarifa_strategy;
use temporal\{Data, DataTempo};
use assento\Assento;
use identificadores\{CodigoDoAssento, CodigoVoo, GeradorDeRegistroDeViagem, RegistroDeAeronave, RegistroDePassagem, RegistroDeViagem, SiglaAeroporto};
use franquia_de_bagagem\FranquiasDeBagagem;
use viagem\{Voo, Viagem};

class ViagemBuilder {
    private GeradorDeRegistroDeViagem $gerador_de_registro;
    private RegistroDeViagem $registro;
    private Data $data;
    private RegistroDeAeronave $aeronave;
    private float $carga, $tarifa, $tarifa_franquia;
    private int $passageiros;
    private CodigoVoo $codigo_do_voo;
    private SiglaAeroporto $aeroporto_de_saida, $aeroporto_de_chegada;
    private DataTempo $hora_de_partida, $hora_de_chegada;
    private array $assentos = array();

    public function add_tarifa_franquia(float $tarifa_franquia): self {
        $this->tarifa_franquia = $tarifa_franquia;
        return $this;
    }

    public function adicionar_gerador_de_registro(GeradorDeRegistroDeViagem $gerador_de_registro): self {
        $this->gerador_de_registro = $gerador_de_registro;
        return $this;
    }

    public function gerar_registro(): self {
        $this->registro = $this->gerador_de_registro->gerar();
        return $this;
    }

    public function add_data(Data $data): self {
        $this->data = $data;
        return $this;
    }

    public function add_voo(Voo $voo): self {
        $this->carga = $voo->capacidade_carga;
        $this->passageiros = $voo->capacidade_passageiros;
        $this->codigo_do_voo = $voo->codigo;
        $this->tarifa = $voo->tarifa;
        $this->aeroporto_de_saida = $voo->aeroporto_de_saida;
        $this->aeroporto_de_chegada = $voo->aeroporto_de_chegada;
        $this->assentos = construir_assentos($voo);
        return $this;
    }

    public function add_aeronave(Aeronave $aeronave): self {
        $carga = $aeronave->capacidade_carga;
        $passageiros = $aeronave->capacidade_passageiros;
        if ($carga != $this->carga || $passageiros != $this->passageiros) {
            throw new Exception("Essa aeronave não tem a carga e passageiros necessários");
        }
        $this->aeronave = registro_de_aeronave($aeronave);
        return $this;
    }

    function tem_assentos_liberados(): bool {
        foreach ($this->assentos as $assento) {
            if ($assento->vazio()) {
                return true;
            }
        }
        return false;
    }
    
    function assento_esta_liberado(CodigoDoAssento $assento): bool {
        if (!isset($this->assentos[$assento])) {
            throw new Exception('Assento não encontrado');
        }
        return $this->assentos[$assento]->vazio();
    }
    
    function tem_carga_disponivel_para_franquias(FranquiasDeBagagem $franquias): bool {
        $carga_usada = 0;
        foreach ($this->assentos as $assento) {
            $carga_usada += $assento->franquias()->carga();
        }
        if ($carga_usada + $franquias->carga() > $this->carga) {
            return false;
        }
        return true;
    }
    
    function codigo_assento_liberado(): CodigoDoAssento {
        foreach ($this->assentos as $codigo => $assento) {
            if ($assento->vazio()) {
                return $codigo;
            }
        }
        throw new Exception('Não tem assentos liberados');
    }
    
    function reservar_assento(bool $cliente_vip, RegistroDePassagem $registro_passagem, FranquiasDeBagagem $franquias, CodigoDoAssento $assento_desejado): float {
        if (!$this->tem_carga_disponivel_para_franquias($franquias)) {
            throw new Exception('Não tem carga para franquia disponível');
        }
        if (!isset($this->assentos[$assento_desejado])) {
            throw new Exception('Assento não encontrado');
        }
        $assento = $this->assentos[$assento_desejado];
        if ($assento->preenchido()) {
            throw new Exception('O assento está preenchido');
        }
        $assento->reservar($registro_passagem, $franquias);
        return calculo_tarifa_strategy_for($cliente_vip, $this->tarifa, $this->tarifa_franquia)->calcula($franquias);
    }
    
    function liberar_assento(RegistroDePassagem $registro_passagem, CodigoDoAssento $assento): void {
        if (!isset($this->assentos[$assento])) {
            throw new Exception('Assento não encontrado');
        }
        $this->assentos[$assento]->liberar($registro_passagem);
    }
    
    function add_hora_de_partida_e_hora_de_chegada(DataTempo $hora_de_partida, DataTempo $hora_de_chegada): self {
        $this->hora_de_partida = $hora_de_partida;
        $this->hora_de_chegada = $hora_de_chegada;
        return $this;
    }

    public function build() {
        return new Viagem(
            $this->registro,
            $this->codigo_do_voo,
            $this->aeroporto_de_saida,
            $this->aeroporto_de_chegada,
            $this->hora_de_partida,
            $this->hora_de_chegada,
            $this->aeronave,
            $this->tarifa,
            $this->tarifa_franquia,
            $this->assentos
        );
    }
}