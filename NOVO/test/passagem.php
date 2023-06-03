<?php

require_once 'suite.php';
require_once '../classes/passagem.php';

class PassagemTestCase extends TestCase
{
    protected function getName(): string
    {
        return "Passagem";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        $registro = new RegistroDePassagem('ABC123');
        $aeroportoSaida = new SiglaAeroporto('GRU');
        $aeroportoChegada = new SiglaAeroporto('CDG');
        $companhiaAerea = new SiglaCompanhiaAerea('GOL');
        $passaporte = new Passaporte('123456789');
        $documentoCliente = new DocumentoPassageiro($passaporte);
        $data = new Data(2023, 9, 23);
        $valor = 100.0;
        $valorPago = 50.0;
        $assentos = ['A1', 'A2'];
        $data_compra = new Data(2023, 07, 21);
        $tempo_compra = new Tempo(10, 35, 22);
        $dataTempoCompra = new DataTempo($data_compra, $tempo_compra);
        $status = new PassagemCheckInNaoAberto();

        $passagem = new Passagem(
            $registro,
            $aeroportoSaida,
            $aeroportoChegada,
            $companhiaAerea,
            $documentoCliente,
            $data,
            $valor,
            $valorPago,
            $assentos,
            $dataTempoCompra,
            $status
        );

        # getters
        $this->startSection("getters");

        $this->checkEq($registro, $passagem->getRegistro());

        $this->checkEq($aeroportoSaida, $passagem->getAeroportoDeSaida());

        $this->checkEq($aeroportoChegada, $passagem->getAeroportoDeChegada());

        $this->checkEq($companhiaAerea, $passagem->getCompanhiaAerea());

        $this->checkEq($documentoCliente, $passagem->getDocumentoCliente());

        $this->checkEq($data, $passagem->getData());

        $this->checkEq($valor, $passagem->getValor());

        $this->checkEq($valorPago, $passagem->getValorPago());

        $this->checkEq($assentos, $passagem->getAssentos());

        $this->checkEq($dataTempoCompra, $passagem->getDataTempoDeCompra());

        $this->checkEq($status, $passagem->status);

        # valorDevendo
        $this->startSection("valorDevendo");

        $this->checkEq(50.0, $passagem->valorDevendo());

        # pagar
        $this->startSection("pagar");

        $this->checkEq(0.0, $passagem->pagar(50.0));
        $this->checkEq(0.0, $passagem->valorDevendo());

        try {
            $passagem->pagar(50.0);
            $this->checkNotReached();
        } catch (Exception $e) {
            $this->checkReached();
        }

        # acionarEvento
        $this->startSection("acionarEvento");

        $this->checkEq(true, $passagem->acionarEvento(Evento::CANCELAR));

        $this->checkEq(true, $passagem->acionarEvento(Evento::ABRIR_CHECK_IN));

        $this->checkEq(true, $passagem->acionarEvento(Evento::FAZER_CHECK_IN));

        $this->checkEq(true, $passagem->acionarEvento(Evento::EMBARCAR));

        $this->checkEq(true, $passagem->acionarEvento(Evento::CONCLUIR));

        # tipoDeStatus
        $this->startSection("tipoDeStatus");

        $this->checkEq(Tipo::CANCELADA, $passagem->tipoDeStatus());

        $this->checkEq(Tipo::CHECK_IN_NAO_ABERTO, $passagem->tipoDeStatus());

        $this->checkEq(Tipo::AGUARDANDO_CHECK_IN, $passagem->tipoDeStatus());

        $this->checkEq(Tipo::NAO_APARECEU, $passagem->tipoDeStatus());

        $this->checkEq(Tipo::CHECKED_IN, $passagem->tipoDeStatus());

        $this->checkEq(Tipo::EMBARCADO, $passagem->tipoDeStatus());

        $this->checkEq(Tipo::CONCLUIDA_COM_SUCESSO, $passagem->tipoDeStatus());

    }
}