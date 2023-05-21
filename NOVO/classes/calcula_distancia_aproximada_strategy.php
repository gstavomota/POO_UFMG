<?php
    class CalculaDistanciaAproximadaStrategy{
        public function calculaDistancia(float $x1, float $y1, float $x2, float $y2){
            return 110.57*sqrt(pow($x2-$x1, 2) + pow($y2-$y1, 2));
        }
    }
?>