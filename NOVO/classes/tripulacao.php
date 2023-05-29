<?php
require_once("identificadores.php");

class Tripulacao
{
    private RegistroDeTripulante $piloto;
    private RegistroDeTripulante $copiloto;
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
        return $this->trancado;
    }
    
    public function getPiloto()
    {
        return $this->piloto;
    }

    public function getCopiloto()
    {
        return $this->copiloto;
    }

    public function getComissarios()
    {
        return $this->comissarios;
    }

    public function setPiloto(RegistroDeTripulante $piloto)
    {
        if ($this->trancado) {
            throw new Exception("A tripulação está trancada");
        }
        if ($this->piloto != null) {
            throw new Exception("Piloto só pode ser setado uma vez.");
        }
        $this->piloto = $piloto;
    }

    public function setCopiloto(RegistroDeTripulante $copiloto)
    {
        if ($this->trancado) {
            throw new Exception("A tripulação está trancada");
        }
        if ($this->copiloto != null) {
            throw new Exception("Copiloto só pode ser setado uma vez.");
        }
        $this->copiloto = $copiloto;
    }

    public function addComissario(RegistroDeTripulante $comissario_novo)
    {
        if ($this->trancado) {
            throw new Exception("A tripulação está trancada");
        }
        foreach ($this->comissarios as $comissario) {
            if ($comissario->eq($comissario_novo)) {
                throw new InvalidArgumentException("Comissario já presente");
            }
        }
        $this->comissarios[] = $comissario;
    }


    public function validar(): void
    {
        if ($this->piloto == null) {
            throw new Exception("Não há piloto na tripulação.");
        }

        if ($this->copiloto == null) {
            throw new Exception("Não há copiloto na tripulação.");
        }

        if (count($this->comissarios) < 2) {
            throw new Exception("Quantidade mínima de comissários não foi atingida.");
        }
    }

    public function trancar(): void
    {
        if ($this->trancado) {
            throw new Exception ("Não é possível trancar duas vezes.");
        }

        $this->validar();
        $this->trancado = true;
    }
}

?>