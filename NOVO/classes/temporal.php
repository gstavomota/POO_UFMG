<?php
/** Uma classe normalizada que determina um intervalo de tempo.
 *
 */
class Duracao {
    private float $segundo;
    private int $dia;
    public function __construct(float $segundo,float $dia)
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
        return new Duracao($this->getSegundo()+$outra->getSegundo(), $this->getDia()+$outra->getDia());
    }

    /** Retorna a subtracao da Duracao provida com $this
     * @return Duracao
     */
    public function sub(Duracao $outra): Duracao {
        return new Duracao($this->getSegundo()-$outra->getSegundo(), $this->getDia()-$outra->getDia());
    }

    /** Retorna a multiplicacao de $this pelo numero provido
     * @return Duracao
     */
    public function mul(float $num): Duracao {
        return new Duracao($this->getSegundo()*$num, $this->getDia()*$num);
    }

    /** Retorna a divisao de $this pelo numero provido
     * @return Duracao
     */
    public function div(float $num): Duracao {
        return new Duracao($this->getSegundo()/$num, $this->getDia()/$num);
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
?>