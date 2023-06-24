<?php

require_once "log.php";
class Pontos
{
    private int $pontos_ganhos;
    private DataTempo $data_de_obtencao;

    public function __construct(int $pontos_ganhos, DataTempo $data_de_obtencao)
    {
        $this->pontos_ganhos = $pontos_ganhos;
        $this->data_de_obtencao = $data_de_obtencao;
    }

    public function getPontosGanhos(): int
    {
        return $this->pontos_ganhos;
    }

    public function getDataDeObtencao(): DataTempo
    {
        return $this->data_de_obtencao;
    }
}

?>