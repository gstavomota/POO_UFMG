<?php
include_once("class.assento.php");
include_once("identificadores.php");

class Viagem {
    public RegistroDeViagem $registro;
    public CodigoVoo $codigo_do_voo;
    public SiglaAeroporto $aeroporto_de_saida;
    public SiglaAeroporto $aeroporto_de_chegada;
    public DataTempo $hora_de_partida;
    public DataTempo $hora_de_chegada;
    public RegistroDeAeronave $aeronave;
    public float $tarifa;
    public float $tarifa_franquia;
    public array $assentos;

    public function __construct(
        RegistroDeViagem $registro,
        CodigoVoo $codigo_do_voo,
        SiglaAeroporto $aeroporto_de_saida,
        SiglaAeroporto $aeroporto_de_chegada,
        DataTempo $hora_de_partida,
        DataTempo $hora_de_chegada,
        RegistroDeAeronave $aeronave,
        float $tarifa,
        float $tarifa_franquia,
        array $assentos
    ) {
        $this->registro = $registro;
        $this->codigo_do_voo = $codigo_do_voo;
        $this->aeroporto_de_saida = $aeroporto_de_saida;
        $this->aeroporto_de_chegada = $aeroporto_de_chegada;
        $this->hora_de_partida = $hora_de_partida;
        $this->hora_de_chegada = $hora_de_chegada;
        $this->aeronave = $aeronave;
        $this->tarifa = $tarifa;
        $this->tarifa_franquia = $tarifa_franquia;
        $this->assentos = $assentos;
    }

    public function __gt__(Viagem $other): bool {
        return $this->hora_de_partida > $other->hora_de_partida;
    }

    public function __lt__(Viagem $other): bool {
        return $this->hora_de_partida < $other->hora_de_partida;
    }
}
?>