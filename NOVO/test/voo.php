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
        # Calcula Tarifa
        $siglaCA = new SiglaCompanhiaAerea("TM");
        $siglaAeroSaida = new SiglaAeroporto("CON");
        $siglaAeroChegada = new SiglaAeroporto("GUA");
        $horaPartida = new Tempo(12, 40, 25);
        $codigo = new CodigoVoo($siglaCA, 1);
        $duracaoVoo = new Duracao(25, 30);
        $diasDaSemana = [DiaDaSemana::SEGUNDA];
        $aeroPadrao = new RegistroDeAeronave(PrefixoRegistroDeAeronave::PP, "AAA");
        $tarifa_voo = 300.0;
        $voo = new Voo($codigo,
            $siglaCA,
            $siglaAeroSaida,
            $siglaAeroChegada,
            $horaPartida,
            $duracaoVoo,
            $diasDaSemana,
            $aeroPadrao,
            2,
            758.7,
            $tarifa_voo,
        10
        );

        $franquia1 = new FranquiaDeBagagem(19.0);
        $franquia2 = new FranquiaDeBagagem(21.0);
        $franquias = new FranquiasDeBagagem([$franquia1, $franquia2]);
        $tarifa_franquia = 10.0;
        $this->startSection("calculaTarifa");
        $this->checkEq(2*$tarifa_franquia+$tarifa_voo, $voo->calculaTarifa(false, $franquias, $tarifa_franquia ));
        $this->checkEq($tarifa_franquia/2+$tarifa_voo, $voo->calculaTarifa(true, $franquias, $tarifa_franquia ));

        # Construir assentos
        $this->startSection("construirAssentos");
        $gerador = new GeradorDeCodigoDoAssento($voo->getCapacidadeDePassageiros());
        $assentos = $gerador->gerar_todos();
        $voo_assentos = $voo->construirAssentos();
        $this->checkEq($assentos, $voo_assentos->keys());
    }
}