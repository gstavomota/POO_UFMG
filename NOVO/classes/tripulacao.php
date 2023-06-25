<?php
require_once("identificadores.php");
require_once "log.php";

class Tripulacao
{
    private ?RegistroDeTripulante $piloto;
    private ?RegistroDeTripulante $copiloto;
    private array $comissarios;
    private bool $trancado;

    public function __construct(RegistroDeTripulante $piloto = null,
                                RegistroDeTripulante $copiloto = null,
                                array                $comissarios = null)
    {
        $this->piloto = $piloto;
        $this->copiloto = $copiloto;
        $this->comissarios = $comissarios ?? [];
        $this->trancado = false;
    }

    public function getTrancado() 
    {
        return log::getInstance()->logRead($this->trancado);
    }
    
    public function getPiloto()
    {
        return log::getInstance()->logRead($this->piloto);
    }

    public function getCopiloto()
    {
        return log::getInstance()->logRead($this->copiloto);
    }

    public function getComissarios()
    {
        return log::getInstance()->logRead($this->comissarios);
    }

    public function setPiloto(RegistroDeTripulante $piloto)
    {
        $pre = clone $this;
        if ($this->trancado) {
            log::getInstance()->logThrow(new Exception("A tripulação está trancada"));
        }
        if ($this->piloto != null) {
            log::getInstance()->logThrow(new Exception("Piloto só pode ser setado uma vez."));
        }
        $this->piloto = $piloto;
        log::getInstance()->logWrite($pre, $this);
    }

    public function setCopiloto(RegistroDeTripulante $copiloto)
    {
        $pre = clone $this;
        if ($this->trancado) {
            log::getInstance()->logThrow(new Exception("A tripulação está trancada"));
        }
        if ($this->copiloto != null) {
            log::getInstance()->logThrow(new Exception("Copiloto só pode ser setado uma vez."));
        }
        $this->copiloto = $copiloto;
        log::getInstance()->logWrite($pre, $this);
    }

    public function addComissario(RegistroDeTripulante $comissario_novo)
    {
        $pre = clone $this;
        if ($this->trancado) {
            log::getInstance()->logThrow(new Exception("A tripulação está trancada"));
        }
        foreach ($this->comissarios as $comissario) {
            if ($comissario->eq($comissario_novo)) {
                log::getInstance()->logThrow(new InvalidArgumentException("Comissario já presente"));
            }
        }
        $this->comissarios[] = $comissario_novo;
        log::getInstance()->logWrite($pre, $this);
    }


    public function validar(): void
    {
        if ($this->piloto == null) {
            log::getInstance()->logThrow(new Exception("Não há piloto na tripulação."));
        }

        if ($this->copiloto == null) {
            log::getInstance()->logThrow(new Exception("Não há copiloto na tripulação."));
        }

        if (count($this->comissarios) < 2) {
            log::getInstance()->logThrow(new Exception("Quantidade mínima de comissários não foi atingida."));
        }
        log::getInstance()->logCall(null);
    }

    public function trancar(): void
    {
        if ($this->trancado) {
            log::getInstance()->logThrow(new Exception ("Não é possível trancar duas vezes."));
        }

        $this->validar();
        $this->trancado = true;
        log::getInstance()->logCall(null);
    }
}

?>