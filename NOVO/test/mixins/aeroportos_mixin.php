<?php

require_once __DIR__ . "/../../classes/aeroporto.php";
require_once __DIR__ . "/../../classes/coordenada.php";
require_once __DIR__ . "/../../classes/identificadores.php";

trait AeroportosMixin {
    protected SiglaAeroporto $siglaConfins;
    protected SiglaAeroporto $siglaGuarulhos;
    protected SiglaAeroporto $siglaCongonhas;
    protected SiglaAeroporto $siglaGaleao;
    protected SiglaAeroporto $siglaAfonsoPena;
    protected Aeroporto $aeroportoConfins;
    protected Aeroporto $aeroportoGuarulhos;
    protected Aeroporto $aeroportoCongonhas;
    protected Aeroporto $aeroportoGaleao;
    protected Aeroporto $aeroportoAfonsoPena;
    /**
     * @var HashMap<SiglaAeroporto, Aeroporto>
     */
    private HashMap $aeroportos;
    public function initAeroportos() {
        $this->siglaConfins = new SiglaAeroporto("CNF");
        $this->siglaGuarulhos = new SiglaAeroporto("GRU");
        $this->siglaCongonhas = new SiglaAeroporto("CGH");
        $this->siglaGaleao = new SiglaAeroporto("GIG");
        $this->siglaAfonsoPena = new SiglaAeroporto("CWB");
        $this->aeroportoConfins = new Aeroporto($this->siglaConfins, "Confins", "Belo Horizonte", Estado::MG, new Coordenada(-19.6354195,-43.9648464));
        $this->aeroportoGuarulhos = new Aeroporto($this->siglaGuarulhos, "Guarulhos", "São Paulo", Estado::SP, new Coordenada(-23.4256370,-46.4797861));
        $this->aeroportoCongonhas = new Aeroporto($this->siglaCongonhas, "Congonhas", "São Paulo", Estado::SP, new Coordenada(-23.6261187,-46.6592500));
        $this->aeroportoGaleao = new Aeroporto($this->siglaGaleao, "Galeão", "Rio de Janeiro", Estado::RJ, new Coordenada(-22.8083726,-43.2347722));
        $this->aeroportoAfonsoPena = new Aeroporto($this->siglaAfonsoPena, "Afonso Pena", "São José dos Pinhais", Estado::PR, new Coordenada(-25.5320692,-491730854));
        $aeroportos = new HashMap();
        $aeroportos->put($this->aeroportoConfins->getSigla(), $this->aeroportoConfins);
        $aeroportos->put($this->aeroportoGuarulhos->getSigla(), $this->aeroportoGuarulhos);
        $aeroportos->put($this->aeroportoCongonhas->getSigla(), $this->aeroportoCongonhas);
        $aeroportos->put($this->aeroportoGaleao->getSigla(), $this->aeroportoGaleao);
        $aeroportos->put($this->aeroportoAfonsoPena->getSigla(), $this->aeroportoAfonsoPena);
        $this->aeroportos = $aeroportos;
    }
    protected function getAeroporto(SiglaAeroporto $sigla): Aeroporto {
        if (!$this->aeroportos->containsKey($sigla)) {
            throw new InvalidArgumentException("Aeroporto não presente");
        }
        return $this->aeroportos->get($sigla);
    }
    protected function limparAeroportos() {
        Aeroporto::deleteAllRecords();
    }
    protected function registrarAeroportos() {
        foreach ($this->aeroportos->values() as $aeroporto) {
            $aeroporto->save();
        }
    }
    protected function registrarAeroporto(SiglaAeroporto $siglaAeroporto): void {
        $aeroporto = $this->getAeroporto($siglaAeroporto);
        $aeroporto->save();
    }
}