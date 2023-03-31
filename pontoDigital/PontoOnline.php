<?php
enum Tipo {
    case INICIO;
    case FIM;
}

class PontoOnline {
    protected DateTime $dataHora;
    protected Tipo $TipoPonto;

    public function __construct(DateTime $data){
        $this->dataHora = $data;
    }

    public function setTipo( Tipo $tipo){
        $this->TipoPonto = $tipo;
    }
}
