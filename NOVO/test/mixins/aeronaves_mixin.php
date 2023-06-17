<?php
include_once "../../classes/aeronave.php";
include_once "../../classes/identificadores.php";
include_once "../../classes/companhia_aerea.php";

trait AeronavesMixin {
    /**
     * @var HashMap<RegistroDeAeronave, Aeronave>
     */
    private HashMap $aeronaves;
    /**
     * @var string[]
     */
    private array $sufixos;
    private int $sufixo = 0;

    public function initAeronaves()
    {
        $this->aeronaves = new HashMap();
        $this->sufixos = ["AAA", "AAB", "AAC", "AAD", "AAE", "AAF", "AAG", "AAH", "AAI", "AAJ", "AAK", "AAL", "AAM", "AAN", "AAO", "AAP", "AAQ"];
    }

    protected function aeronaveDoisPassageiros(SiglaCompanhiaAerea $sigla) {
        $sufixo = $this->sufixos[$this->sufixo];
        $this->sufixo++;
        $aeronave = new Aeronave(
            $sigla,
            "Fabricante",
            "02",
            2,
            30.0,
            new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, $sufixo),
        );
        $this->aeronaves->put($aeronave->getRegistro(), $aeronave);
        return $aeronave;
    }
    protected function aeronaveEmbraer175(SiglaCompanhiaAerea $sigla, PrefixoRegistroDeAeronave $prefixo = PrefixoRegistroDeAeronave::PP, ?string $sufixo) {
        if (is_null($sufixo)) {
            $sufixo = $this->sufixos[$this->sufixo];
            $this->sufixo++;
        }
        $aeronave = new Aeronave(
            $sigla,
            "Embraer",
            "175",
            180,
            600.0,
            new RegistroDeAeronave($prefixo, $sufixo),
        );
        $this->aeronaves->put($aeronave->getRegistro(), $aeronave);
        return $aeronave;
    }
    protected function registrarAeronaveNaCompanhia(CompanhiaAerea& $companhia, RegistroDeAeronave $registroDeAeronave): void {
        $aeronave = $this->getAeronave($registroDeAeronave);
        if (!$companhia->getSigla()->eq($aeronave->getSigla())) {
            throw new InvalidArgumentException("Sigla não correspondente");
        }
        $companhia->registrarAeronave($aeronave->getFabricante(), $aeronave->getModelo(),$aeronave->getCapacidadePassageiros(), $aeronave->getCapacidadeCarga(), $aeronave->getRegistro());
    }
    protected function getAeronave(RegistroDeAeronave $registroDeAeronave): Aeronave {
        $aeronave = $this->aeronaves->get($registroDeAeronave);
        if (is_null($aeronave)) {
            throw new InvalidArgumentException("Aeronave não presente");
        }
        return $aeronave;
    }
}