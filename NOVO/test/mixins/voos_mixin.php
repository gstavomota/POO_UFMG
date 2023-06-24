<?php
require_once "aeroportos_mixin.php";
require_once "aeronaves_mixin.php";
require_once __DIR__ . "/../../classes/companhia_aerea.php";

trait VoosMixin {
    use AeroportosMixin;
    use AeronavesMixin;
    protected function initVoos() {
        $this->initAeroportos();
        $this->initAeronaves();
    }
    protected function vooConfinsGuarulhos(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 0),
            $this->siglaConfins,
            $this->siglaGuarulhos,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooConfinsCongonhas(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 1),
            $this->siglaConfins,
            $this->siglaCongonhas,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooConfinsGaleao(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 2),
            $this->siglaConfins,
            $this->siglaGaleao,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooConfinsAfonsoPena(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 3),
            $this->siglaConfins,
            $this->siglaAfonsoPena,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGuarulhosConfins(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 4),
            $this->siglaGuarulhos,
            $this->siglaConfins,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGuarulhosCongonhas(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 5),
            $this->siglaGuarulhos,
            $this->siglaCongonhas,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGuarulhosGaleao(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 6),
            $this->siglaGuarulhos,
            $this->siglaGaleao,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGuarulhosAfonsoPena(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 7),
            $this->siglaGuarulhos,
            $this->siglaAfonsoPena,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooCongonhasConfins(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 8),
            $this->siglaCongonhas,
            $this->siglaConfins,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooCongonhasGuarulhos(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 9),
            $this->siglaCongonhas,
            $this->siglaGuarulhos,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooCongonhasGaleao(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 10),
            $this->siglaCongonhas,
            $this->siglaGaleao,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooCongonhasAfonsoPena(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 11),
            $this->siglaCongonhas,
            $this->siglaAfonsoPena,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGaleaoConfins(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 12),
            $this->siglaGaleao,
            $this->siglaConfins,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGaleaoGuarulhos(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 13),
            $this->siglaGaleao,
            $this->siglaGuarulhos,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGaleaoCongonhas(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 14),
            $this->siglaGaleao,
            $this->siglaCongonhas,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooGaleaoAfonsoPena(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 15),
            $this->siglaGaleao,
            $this->siglaAfonsoPena,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooAfonsoPenaConfins(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 16),
            $this->siglaAfonsoPena,
            $this->siglaConfins,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooAfonsoPenaGuarulhos(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 17),
            $this->siglaAfonsoPena,
            $this->siglaGuarulhos,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooAfonsoPenaCongonhas(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 18),
            $this->siglaAfonsoPena,
            $this->siglaCongonhas,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }

    protected function vooAfonsoPenaGaleao(SiglaCompanhiaAerea $sigla, Tempo $partida, RegistroDeAeronave $registroAeronave, array $diasDaSemana, float $tarifa): Voo {
        return new Voo(
            new CodigoVoo($sigla, 19),
            $this->siglaAfonsoPena,
            $this->siglaGaleao,
            $partida,
            new Duracao(0, 60*60*2),
            $diasDaSemana,
            $registroAeronave,
            $this->getAeronave($registroAeronave)->getCapacidadePassageiros(),
            $this->getAeronave($registroAeronave)->getCapacidadeCarga(),
            $tarifa,
            (int)$tarifa
        );
    }
    protected function registrarVooNaCompanhia(CompanhiaAerea& $companhia, Voo $voo): void {
        if (!$companhia->getSigla()->eq($voo->getSiglaCompanhiaAerea())) {
            throw new InvalidArgumentException("Sigla não correspondente");
        }
        $companhia->registrarVoo(
            $voo->getCodigo()->getNumero(),
            $voo->getAeroportoSaida(),
            $voo->getAeroportoChegada(),
            $voo->getHoraDePartida(),
            $voo->getDuracaoEstimada(),
            $voo->getDiasDaSemana(),
            $voo->getAeronavePadrao(),
            $voo->getTarifa(),
            $voo->getPontuacaoMilhagem()
        );
    }

}
