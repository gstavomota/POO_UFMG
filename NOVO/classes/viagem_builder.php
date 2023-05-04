<?php

include_once ('gerador_de_registro.php');

class ViagemBuilder {
    # ViagemBuilder
    private GeradorDeRegistroDeViagem $gerador_de_registro;
    private RegistroDeviagem $registro;
    private DataTime $data; //verificar se não vamos usar a classe data

    # Aeronave
    private RegistraAeronave $aeronave;

    # Voo
    private float $carga;
    private int $passageiros;
    private CodigoVoo $codigo_do_voo;
    private float $tarifa;
    private float $tarifa_franquia;
    private siglaAeroporto $aeroporto_de_saida;
    private siglaAeroporto $aeroporto_de_chegada;

    # Extra
    private DataTime $hora_de_partida;
    private DataTime $hora_de_chegada;

    # Passagens
    $assentos = array(); // era pra ser um map/dict

    public function add_tarifa_franquia(float $tarifa_franquia): ViagemBuilder{
        $this->tarifa_franquia = $tarifa_franquia;
        return $this;
    }

    public function adicionar_gerador_de_registro(GeradorDeRegistro $gerador_de_registro): ViagemBuilder {
        $this->gerador_de_registro = $gerador_de_registro;
        return $this;        
    }

    # Revisar essa função aqui de baixo
    public function gerar_registro(): ViagemBuilder {
        $this->registro = $this->gerador_de_registro.gerar();
        return $this;
    }

    public function add_data(DataTime $data): ViagemBuilder {
        
    }


}
