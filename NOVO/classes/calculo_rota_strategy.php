<?php

require_once 'coordenada.php';

interface CalculoRotaStrategy
{
    /** Calcula a distancia de uma rota
     * @param ICoordenada[] $coordenadas
     * @param ICoordenada $pontoFinal
     * @return float
     */
    function calculaDistanciaTotal(array $coordenadas, ICoordenada $pontoFinal): float;
}

class CalculoRotaAproximadaStrategy implements CalculoRotaStrategy
{
    public function calculaDistanciaTotal(array $coordenadas, ICoordenada $pontoFinal): float
    {
        if (empty($coordenadas)) {
            return 0.0;
        }
        if (count($coordenadas) == 1) {
            return $this->calculaDistanciaDeDoisPontos($coordenadas[0], $pontoFinal);
        }
        $distancia = 0.0;
        $primeiraCoordenada = $coordenadas[0];
        $restoDeCoordenadas = [...array_splice($coordenadas, 1), $pontoFinal];
        $ultimaCoordenada = $primeiraCoordenada;
        foreach ($restoDeCoordenadas as $coordenadaSeguinte) {
            $distancia += $this->calculaDistanciaDeDoisPontos($ultimaCoordenada, $coordenadaSeguinte);
            $ultimaCoordenada = $coordenadaSeguinte;
        }
        return $distancia;
    }

    /** Calcula a distancia aproximada de dois pontos
     * @param ICoordenada $a
     * @param ICoordenada $b
     * @return float
     */
    private function calculaDistanciaDeDoisPontos(ICoordenada $a, ICoordenada $b): float
    {
        return 110.57 * sqrt(pow($b->getX() - $a->getX(), 2) + pow($b->getY() - $a->getY(), 2));
    }
}

?>
