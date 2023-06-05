<?php

require_once 'suite.php';
require_once '../classes/passagem.php';

class PassagemCanceladaTestCase extends TestCase {
    public function getName(): string {
        return "PassagemCancelada";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemCancelada();
        $this->checkEq($status->abrir_check_in(), $status);
        $this->checkEq($status->fazer_check_in(), $status);
        $this->checkEq($status->embarcar(), $status);
        $this->checkEq($status->concluir(), $status);
        $this->checkEq($status->cancelar(), $status);
        
    }
}
class PassagemCheckInNaoAbertoTestCase extends TestCase {
    public function getName(): string {
        return "PassagemCheckInNaoAberto";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemCheckInNaoAberto();
        $this->checkEq($status->abrir_check_in(), new PassagemAguardandoCheckIn());
        $this->checkEq($status->fazer_check_in(), $status);
        $this->checkEq($status->embarcar(), $status);
        $this->checkEq($status->concluir(), $status);
        $this->checkEq($status->cancelar(), new PassagemCancelada());
        
    }
}
class PassagemAguardandoCheckInTestCase extends TestCase {
    public function getName(): string {
        return "PassagemAguardandoCheckIn";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemAguardandoCheckIn();
        $this->checkEq($status->abrir_check_in(), $status);
        $this->checkEq($status->fazer_check_in(), new PassagemCheckedIn());
        $this->checkEq($status->embarcar(), $status);
        $this->checkEq($status->concluir(), new PassagemNaoApareceu());
        $this->checkEq($status->cancelar(), new PassagemCancelada());
        
    }
}
class PassagemNaoApareceuTestCase extends TestCase {
    public function getName(): string {
        return "PassagemNaoApareceu";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemNaoApareceu();
        $this->checkEq($status->abrir_check_in(), $status);
        $this->checkEq($status->fazer_check_in(), $status);
        $this->checkEq($status->embarcar(), $status);
        $this->checkEq($status->concluir(), $status);
        $this->checkEq($status->cancelar(), $status);
        
    }
}
class PassagemCheckedInTestCase extends TestCase {
    public function getName(): string {
        return "PassagemCheckedIn";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemCheckedIn();
        $this->checkEq($status->abrir_check_in(), $status);
        $this->checkEq($status->fazer_check_in(), $status);
        $this->checkEq($status->embarcar(), new PassagemEmbarcado());
        $this->checkEq($status->concluir(), new PassagemNaoApareceu());
        $this->checkEq($status->cancelar(), new PassagemCancelada());
        
    }
}
class PassagemEmbarcadoTestCase extends TestCase {
    public function getName(): string {
        return "PassagemEmbarcado";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemEmbarcado();
        $this->checkEq($status->abrir_check_in(), $status);
        $this->checkEq($status->fazer_check_in(), $status);
        $this->checkEq($status->embarcar(), $status);
        $this->checkEq($status->concluir(), new PassagemConcluidaComSucesso());
        $this->checkEq($status->cancelar(), $status);
        
    }
}
class PassagemConcluidaComSucessoTestCase extends TestCase {
    public function getName(): string {
        return "PassagemConcluidaComSucesso";
    }
    
    public function run() {
        # Transitions
        $status = new PassagemConcluidaComSucesso();
        $this->checkEq($status->abrir_check_in(), $status);
        $this->checkEq($status->fazer_check_in(), $status);
        $this->checkEq($status->embarcar(), $status);
        $this->checkEq($status->concluir(), $status);
        $this->checkEq($status->cancelar(), $status);
        
    }
}


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
        $registro = new RegistroDePassagem(1);
        $aeroportoSaida = new SiglaAeroporto('GRU');
        $aeroportoChegada = new SiglaAeroporto('CDG');
        $companhiaAerea = new SiglaCompanhiaAerea('GL');
        $passaporte = new Passaporte('A12345678');
        $documentoCliente = new DocumentoPassageiro($passaporte);
        $data = new Data(2023, 9, 23);
        $valor = 100.0;
        $valorPago = 50.0;
        $assentos = new HashMap();
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

        $this->checkEq($status, $this->getNonPublicProperty($passagem, "status"));

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

        $this->checkEq(Tipo::CHECK_IN_NAO_ABERTO, $passagem->tipoDeStatus());
        $this->checkTrue($passagem->acionarEvento(Evento::ABRIR_CHECK_IN));
        $this->checkEq(Tipo::AGUARDANDO_CHECK_IN, $passagem->tipoDeStatus());
        $this->checkTrue($passagem->acionarEvento(Evento::FAZER_CHECK_IN));
        $this->checkEq(Tipo::CHECKED_IN, $passagem->tipoDeStatus());
        $this->checkTrue($passagem->acionarEvento(Evento::EMBARCAR));
        $this->checkEq(Tipo::EMBARCADO, $passagem->tipoDeStatus());
        $this->checkTrue($passagem->acionarEvento(Evento::CONCLUIR));
        $this->checkEq(Tipo::CONCLUIDA_COM_SUCESSO, $passagem->tipoDeStatus());
    }
}