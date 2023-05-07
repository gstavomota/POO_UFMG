<?php

require_once ('identificadores.php');

use Estado;
use Enum;

class Tipo extends Enum {
    const CANCELADA = "cancelada";
    const CHECK_IN_NAO_ABERTO = "check_in_nao_aberto";
    const AGUARDANDO_CHECK_IN = "aguardando_check_in";
    const NAO_APARECEU = "nao_apareceu";
    const CHECKED_IN = "checked_in";
    const EMBARCADO = "embarcado";
    const CONCLUIDA_COM_SUCESSO = "concluida_com_sucesso";
}

class Evento extends Enum {
    const CANCELAR = "cancelar";
    const ABRIR_CHECK_IN = "abrir_check_in";
    const FAZER_CHECK_IN = "fazer_check_in";
    const EMBARCAR = "embarcar";
    const CONCLUIR = "concluir";
}

class StatusDaPassagem {

    public Tipo $tipo;

    public function __construct(Tipo $tipo) {
        $this->tipo = $tipo;
    }

    public function cancelar(): StatusDaPassagem {
        return $this;
    }

    public function abrir_check_in(): StatusDaPassagem {
        return $this;
    }

    public function fazer_check_in(): StatusDaPassagem {
        return $this;
    }

    public function embarcar(): StatusDaPassagem {
        return $this;
    }

    public function concluir(): StatusDaPassagem {
        return $this;
    }

    public function dispatch_event(Evento $evento): StatusDaPassagem {
        switch ($evento) {
            case StatusDaPassagem::Evento()->CANCELAR: return $this->cancelar();
            case StatusDaPassagem::Evento()->ABRIR_CHECK_IN: return $this->abrir_check_in();
            case StatusDaPassagem::Evento()->FAZER_CHECK_IN: return $this->fazer_check_in();
            case StatusDaPassagem::Evento()->EMBARCAR: return $this->embarcar();
            case StatusDaPassagem::Evento()->CONCLUIR: return $this->concluir();
        }
    }
}

class PassagemCancelada extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()->CANCELADA);
    }
}

class PassagemCheckInNaoAberto extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()["CHECK_IN_NAO_ABERTO"]);
    }
    // Transition to cancelled or to checkin aberto
    public function cancelar(): StatusDaPassagem {
        return new PassagemCancelada();
    }
    public function abrir_check_in(): StatusDaPassagem {
        return new PassagemAguardandoCheckIn();
    }
}

class PassagemAguardandoCheckIn extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()["AGUARDANDO_CHECK_IN"]);
    }
    // Transition to cancelled, checked in or did not show up
    public function cancelar(): StatusDaPassagem {
        return new PassagemCancelada();
    }
    public function fazer_check_in(): StatusDaPassagem {
        return new PassagemCheckedIn();
    }
    public function concluir(): StatusDaPassagem {
        return new PassagemNaoApareceu();
    }
}

class PassagemNaoApareceu extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()["NAO_APARECEU"]);
    }
    // Dont transition
}

class PassagemCheckedIn extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()["CHECKED_IN"]);
    }
    // Transition to no show, cancelled or embarcated
    public function embarcar(): StatusDaPassagem {
        return new PassagemEmbarcado();
    }
    public function cancelar(): StatusDaPassagem {
        return new PassagemCancelada();
    }
    public function concluir(): StatusDaPassagem {
        return new PassagemNaoApareceu();
    }
}

class PassagemEmbarcado extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()["EMBARCADO"]);
    }
    // Transition to concluded
    public function concluir(): StatusDaPassagem {
        return new PassagemConcluidaComSucesso();
    }
}

class PassagemConcluidaComSucesso extends StatusDaPassagem {
    public function __construct() {
        parent::__construct(StatusDaPassagem::Tipo()["CONCLUIDA_COM_SUCESSO"]);
    }
    // Dont transition
}

class Passagem {
    public RegistroDePassagem $registro;
    public SiglaAeroporto $aeroporto_de_saida;
    public SiglaAeroporto $aeroporto_de_chegada;
    public SiglaCompanhiaAerea $companhia_aerea;
    public DocumentoPassageiro $documento_cliente;
    public DateTime $data;
    public float $valor;
    public float $valor_pago;
    public array $assentos;
    public DataTempo $data_tempo_de_compra;
    public StatusDaPassagem $status;

    public function __construct(
        RegistroDePassagem $registro,
        SiglaAeroporto $aeroporto_de_saida,
        SiglaAeroporto $aeroporto_de_chegada,
        SiglaCompanhiaAerea $companhia_aerea,
        DocumentoPassageiro $documento_cliente,
        DateTime $data,
        float $valor,
        float $valor_pago,
        array $assentos,
        DataTempo $data_tempo_de_compra,
        StatusDaPassagem $status
    ) {
        $this->registro = $registro;
        $this->aeroporto_de_saida = $aeroporto_de_saida;
        $this->aeroporto_de_chegada = $aeroporto_de_chegada;
        $this->companhia_aerea = $companhia_aerea;
        $this->documento_cliente = $documento_cliente;
        $this->data = $data;
        $this->valor = $valor;
        $this->valor_pago = $valor_pago;
        $this->assentos = $assentos;
        $this->data_tempo_de_compra = $data_tempo_de_compra;
        $this->status = $status;
    }

    public function tipo_de_status(): StatusDaPassagemTipo {
        return $this->status->tipo;
    }

    public function valor_devendo(): float {
        return $this->valor - $this->valor_pago;
    }

    public function pagar(float $valor): float {
        if ($this->valor_devendo() < $valor) {
            throw new Exception("Você está pagando muito, a companhia não pode ficar te devendo");
        }
        $this->valor_pago += $valor;
        return $this->valor_devendo();
    }

    public function acionar_evento(StatusDaPassagemEvento $evento): bool {
        $old_status = $this->status;
        $new_status = $this->status->dispatch_event($evento);
        $this->status = $new_status;
        return $old_status == $new_status;
    }
}
