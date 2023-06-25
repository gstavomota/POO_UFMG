<?php
require_once __DIR__ . "/../../classes/tripulante.php";
require_once __DIR__ . "/../../classes/companhia_aerea.php";
require_once "endereco_mixin.php";
require_once "passageiro_mixin.php";
trait TripulanteMixin {
    use EnderecoMixin;
    use PassageiroMixin;

    private function coordenadaTripulante(SiglaAeroporto $aeroporto): ICoordenada {
        $coordAeroporto = Aeroporto::getRecordsBySigla($aeroporto)[0]->getCoordenada();
        $dt = 0.001;
        $dx = $dt * rand(-10, 10);
        $dy = $dt * rand(-10, 10);
        return new Coordenada($dx + $coordAeroporto->getX(), $dy + $coordAeroporto->getY());
    }
    protected function initTripulante() {
        $this->initEndereco();
    }
    private int $numeroCht = 0;
    protected function buildTripulante(Cargo $cargo, SiglaCompanhiaAerea $companhiaAerea, SiglaAeroporto $aeroportoBase = new SiglaAeroporto("GRU")): Tripulante {
        $passageiro = $this->passageiro();
        $ncht = $this->numeroCht;
        $cht = "CHT".$ncht;
        $this->numeroCht++;
        return new Tripulante(
            $passageiro->getNome(),
            $passageiro->getSobrenome(),
            $passageiro->getDocumento(),
            $passageiro->getNacionalidade(),
            $passageiro->getCpf(),
            $passageiro->getDataDeNascimento(),
            $passageiro->getEmail(),
            $cht,
            $this->endereco,
            $companhiaAerea,
            $aeroportoBase,
            $cargo,
            new RegistroDeTripulante($ncht)
        );
    }
    protected function registrarTripulante(CompanhiaAerea &$companhiaAerea, Cargo $cargo, SiglaAeroporto $aeroportoBase = new SiglaAeroporto("GRU")): Tripulante
    {
        $passageiro = $this->passageiro();
        $cht = "CHT".$this->numeroCht;
        $this->numeroCht++;
        return $companhiaAerea->registrarTripulante(
            $passageiro->getNome(),
            $passageiro->getSobrenome(),
            $passageiro->getDocumento(),
            $passageiro->getNacionalidade(),
            $passageiro->getCpf(),
            $passageiro->getDataDeNascimento(),
            $passageiro->getEmail(),
            $cht,
            $this->endereco,
            $aeroportoBase,
            $cargo
        );
    }
}