<?php
class Onibus {
    /**
     * @var TripulanteComCoordenada[]
     */
    private array $tripulantes = [];
    private ICoordenada $coordenadaAeroporto;
    private Tempo $horaDeChegada;
    static private float $velocidadeKmH = 18;
    public function __construct(ICoordenada $coordenadaAeroporto, Tempo $horaDeChegada) {
        $this->coordenadaAeroporto = $coordenadaAeroporto;
        $this->horaDeChegada = $horaDeChegada;
    }

    public function adicionarTripulante(TripulanteComCoordenada $tripulanteComCoordenada): void {
        if (in_array($tripulanteComCoordenada, $this->tripulantes)) {
            throw new InvalidArgumentException("O tripulante ja está no onibus");
        }
        $this->tripulantes[] = $tripulanteComCoordenada;
    }

    public function distancia(CalculoRotaStrategy $calculoRotaStrategy): float {
        return $calculoRotaStrategy->calculaDistanciaTotal($this->tripulantes, $this->coordenadaAeroporto);
    }

    public function horaDeSaida(CalculoRotaStrategy $calculoRotaStrategy): HashMap {
        $distanciaTotal = $calculoRotaStrategy->calculaDistanciaTotal($this->tripulantes, $this->coordenadaAeroporto);
        $tempoTotalEmH = $distanciaTotal/static::$velocidadeKmH;
        $horaDeSaida = $this->horaDeChegada->sub(Duracao::umaHora()->mul($tempoTotalEmH));
        /**
         * @var HashMap<RegistroDeTripulante, Tempo> $saidas
         */
        $saidas = new HashMap();
        $parciais = $calculoRotaStrategy->calculaDistanciaParciais($this->tripulantes, $this->coordenadaAeroporto);
        $ultimaHoraDeSaida = $horaDeSaida;
        for ($i = 0; $i < count($this->tripulantes); $i++) {
            $tripulante = $this->tripulantes[$i];
            $saidas->put($tripulante->getTripulante(), $ultimaHoraDeSaida);
            $parcial = $parciais[$i];
            $tempoParcialEmH = $parcial/static::$velocidadeKmH;
            $duracaoParcial = Duracao::umaHora()->mul($tempoParcialEmH);
            $ultimaHoraDeSaida = $ultimaHoraDeSaida->add($duracaoParcial);
        }
        return $saidas;
    }
}