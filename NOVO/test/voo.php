<?php
include_once 'suite.php';
include_once '../classes/voo.php';

class VooTestCase extends TestCase
{
    protected function getName(): string
    {
        return "Voo";
    }

    public function run()
    {
        # Construtor 
        $this->startSection("Calcula Tarifa");
        try {

            $siglaCA = new SiglaCompanhiaAerea("LATAM");
            $siglaAeroSaida = new SiglaAeroporto("CON");
            $siglaAeroChegada = new SiglaAeroporto("GUA");
            $horaPartida = new Tempo(12, 40, 25);
            $codigo = new CodigoVoo($siglaCA, 1);
            $duracaoVoo = new Duracao(25, 30);
            //$diasDaSemana = DiaDaSemana; Implementar enum php
            //Falta PrefixoREgistroDeAeronave
            $aeroPadrao = new RegistroDeAeronave();

            $voo = new Voo($codigo, 
                $siglaCA, 
                $siglaAeroSaida, 
                $siglaAeroChegada, 
                $horaPartida, 
                $duracaoVoo, 
                [1,2,3], 
                $aeroPadrao,
                35,
                758.7,
                300.0
            );

            $franquia1 = new FranquiaDeBagagem(19.0);
            $franquia2 = new FranquiaDeBagagem(21.0);
            $arr = [$franquia1, $franquia2];
            $franquias = new FranquiasDeBagagem($arr);

            $this->checkEq(40.0, $voo->calculaTarifa(false, $franquias, 13 ));
            $this->checkReached();

        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }

        $this->startSection("construirAssentos");
        try {

            $siglaCA = new SiglaCompanhiaAerea("LATAM");
            $siglaAeroSaida = new SiglaAeroporto("CON");
            $siglaAeroChegada = new SiglaAeroporto("GUA");
            $horaPartida = new Tempo(12, 40, 25);
            $codigo = new CodigoVoo($siglaCA, 1);
            $duracaoVoo = new Duracao(25, 30);
            //$diasDaSemana = DiaDaSemana; Implementar enum php
            //Falta PrefixoREgistroDeAeronave
            $aeroPadrao = new RegistroDeAeronave();

            $voo = new Voo($codigo, 
                $siglaCA, 
                $siglaAeroSaida, 
                $siglaAeroChegada, 
                $horaPartida, 
                $duracaoVoo, 
                [1,2,3], 
                $aeroPadrao,
                1,
                758.7,
                300.0
            );

            $gerador = new GeradorDeCodigoDoAssento($voo->capacidade_passageiros, 0.0);
            $assentos = $gerador->gerar_todos();

            $this->checkEq(["{A1}",$assentos[0]], $voo->construirAssentos());
            $this->checkReached();

        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
    }
}