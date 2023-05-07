
<?php

require_once "aeroporto.php";
require_once "assento.php";
require_once "calculo_tarifa_strategy.php";
require_once "companhia_aerea.php";
require_once "franquia_de_bagagem.php";
require_once "identificadores.php";
require_once "temporal.php";

class Voo {
    private $codigo;
    private $companhia_aerea;
    private $aeroporto_de_saida;
    private $aeroporto_de_chegada;
    private $hora_de_partida;
    private $duracao_estimada;
    private $dias_da_semana;
    private $aeronave_padrao;
    private $capacidade_passageiros;
    private $capacidade_carga;
    private $tarifa;

    public function __construct($codigo, $companhia_aerea, $aeroporto_de_saida, $aeroporto_de_chegada, $hora_de_partida, $duracao_estimada, $dias_da_semana, $aeronave_padrao, $capacidade_passageiros, $capacidade_carga, $tarifa) {
        $this->codigo = $codigo;
        $this->companhia_aerea = $companhia_aerea;
        $this->aeroporto_de_saida = $aeroporto_de_saida;
        $this->aeroporto_de_chegada = $aeroporto_de_chegada;
        $this->hora_de_partida = $hora_de_partida;
        $this->duracao_estimada = $duracao_estimada;
        $this->dias_da_semana = $dias_da_semana;
        $this->aeronave_padrao = $aeronave_padrao;
        $this->capacidade_passageiros = $capacidade_passageiros;
        $this->capacidade_carga = $capacidade_carga;
        $this->tarifa = $tarifa;
    }

    public function calcula_tarifa($cliente_vip, $franquias, $tarifa_franquia) {
        return calculo_tarifa_strategy_for($cliente_vip, $this->tarifa, $tarifa_franquia)->calcula($franquias);
    }

    public function construir_assentos() {
        $gerador = new GeradorDeCodigoDoAssento($this->capacidade_passageiros, 0.0);
        $assentos = $gerador->gerar_todos();
        $dict_assentos = array();
        foreach ($assentos as $codigo_assento) {
            $dict_assentos[$codigo_assento] = new Assento($codigo_assento);
        }
        return $dict_assentos;
    }
}
?>