<?php
/** Uma classe normalizada que determina um intervalo de tempo.
 *
 */
class Duracao {
    private float $segundo;
    private int $dia;
    public function __construct(float $dia, float $segundo)
    {
        $segundosNoDia = 60*60*24;
        $segundos = $segundo + $segundosNoDia * $dia;
        $this->segundo = $segundo % $segundosNoDia;
        $this->dia = floor($segundos / $segundosNoDia);
    }


    /** Retorna o numero de segundos
     * @return float
     */
    public function getSegundo(): float
    {
        return $this->segundo;
    }

    /** Retorna o numero de dias
     * @return int
     */
    public function getDia(): int
    {
        return $this->dia;
    }

    /** Retorna a soma da Duracao provida com $this
     * @return Duracao
     */
    public function add(Duracao $outra): Duracao {
        return new Duracao($this->getDia()+$outra->getDia(), $this->getSegundo()+$outra->getSegundo());
    }

    /** Retorna a subtracao da Duracao provida com $this
     * @return Duracao
     */
    public function sub(Duracao $outra): Duracao {
        return new Duracao($this->getDia()-$outra->getDia(), $this->getSegundo()-$outra->getSegundo());
    }

    /** Retorna a multiplicacao de $this pelo numero provido
     * @return Duracao
     */
    public function mul(float $num): Duracao {
        return new Duracao($this->getDia()*$num, $this->getSegundo()*$num);
    }

    /** Retorna a divisao de $this pelo numero provido
     * @return Duracao
     */
    public function div(float $num): Duracao {
        return new Duracao($this->getDia()/$num, $this->getSegundo()/$num,);
    }

    /** Operador de comparação >
     * @return bool
     */
    public function gt(Duracao $outra): bool {
        return $outra->getDia() > $this->getDia() || $outra->getDia() == $this->getDia() && $outra->getSegundo() > $this->getSegundo();
    }

    /** Operador de comparação >=
     * @return bool
     */
    public function gte(Duracao $outra): bool {
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @return bool
     */
    public function st(Duracao $outra): bool {
        return $outra->getDia() < $this->getDia() || $outra->getDia() == $this->getDia() && $outra->getSegundo() < $this->getSegundo();
    }

    /** Operador de comparação <=
     * @return bool
     */
    public function ste(Duracao $outra): bool {
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @return bool
     */
    public function eq(Duracao $outra): bool {
        return $outra->getDia() == $this->getDia() && $outra->getSegundo() == $this->getSegundo();
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getDia()}d{$this->getSegundo()}s";
    }
}

/** Uma classe normalizada que determina um tempo em um dia.
 *
 */
class Tempo {
    private int $hora;
    private int $minuto;
    private float $segundo;

    /**
     * @throws Exception when hora is more than 24
     */
    public function __construct(float $hora, float $minuto, float $segundo)
    {
        $segundosInMinuto = 60;
        $segundosInHora = $segundosInMinuto * 60;
        $timeInSegundos = $segundo + $minuto * $segundosInMinuto + $hora * $segundosInHora;
        $this->hora = Tempo::validateHora(floor($timeInSegundos / $segundosInHora));
        $this->minuto = floor($timeInSegundos / $segundosInMinuto - $this->hora * $segundosInHora);
        $this->segundo = $timeInSegundos - $this->hora * $segundosInHora - $this->minuto * $segundosInMinuto;
    }

    /** Valida uma hora
     * @throws Exception when hora is more than 24
     */
    private static function validateHora(int $hora): int {
        if ($hora >= 24) {
            throw new Exception("Hora is more than 24 on time");
        }
        return $hora;
    }
    /** Retorna o numero de horas
     * @return int
     */
    public function getHora(): int
    {
        return $this->hora;
    }

    /** Retorna o numero de minutos
     * @return int
     */
    public function getMinuto(): int
    {
        return $this->minuto;
    }

    /** Retorna o numero de segundos
     * @return float
     */
    public function getSegundo(): float
    {
        return $this->segundo;
    }

    /** Retorna a soma da Duracao provido com $this
     * @param Duracao $outra
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function add(Duracao $outra): Tempo {
        return new Tempo($this->getHora() + $outra->getDia()*24, $this->getMinuto(), $this->getSegundo()+$outra->getSegundo());
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function sub(Duracao $outra): Tempo {
        return new Tempo($this->getHora() - $outra->getDia()*24, $this->getMinuto(), $this->getSegundo()-$outra->getSegundo());
    }

    /** Retorna a multiplicacao de $this pelo numero provido
     * @param float $num
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function mul(float $num): Tempo {
        return new Tempo($this->getHora()*$num, $this->getMinuto()*$num, $this->getSegundo()*$num);
    }

    /** Retorna a divisao de $this pelo numero provido
     * @param float $num
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function div(float $num): Tempo {
        return new Tempo($this->getHora()/$num, $this->getMinuto()/$num, $this->getSegundo()/$num);
    }

    /** Operador de comparação >
     * @param Tempo $outra
     * @return bool
     */
    public function gt(Tempo $outra): bool {
        return $outra->getHora() > $this->getHora() || $outra->getHora() == $this->getHora() && $outra->getMinuto() > $this->getMinuto() || $outra->getHora() == $this->getHora() && $outra->getMinuto() == $this->getMinuto() && $outra->getSegundo() > $this->getSegundo();
    }

    /** Operador de comparação >=
     * @param Tempo $outra
     * @return bool
     */
    public function gte(Tempo $outra): bool {
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param Tempo $outra
     * @return bool
     */
    public function st(Tempo $outra): bool {
        return $outra->getHora() < $this->getHora() || $outra->getHora() == $this->getHora() && $outra->getMinuto() < $this->getMinuto() || $outra->getHora() == $this->getHora() && $outra->getMinuto() == $this->getMinuto() && $outra->getSegundo() < $this->getSegundo();
    }

    /** Operador de comparação <=
     * @param Tempo $outra
     * @return bool
     */
    public function ste(Tempo $outra): bool {
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param Tempo $outra
     * @return bool
     */
    public function eq(Tempo $outra): bool {
        return $outra->getHora() == $this->getHora() && $outra->getMinuto() == $this->getMinuto() && $outra->getSegundo() == $this->getSegundo();
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getHora()}h{$this->getMinuto()}m{$this->getSegundo()}s";
    }
}
?>