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

/** Um Enum com os dias da semana. Ele possui a trait EnumToArray pra ter acesso a metodos estaticos uteis.
 *
 */
enum DiaDaSemana: string {
    use EnumToArray;
    case DOMINGO = "domingo";
    case SEGUNDA = "segunda";
    case TERCA = "terca";
    case QUARTA = "quarta";
    case QUINTA = "quinta";
    case SEXTA = "sexta";
    case SABADO = "sabado";
}

/** Uma classe normalizada que determina uma data.
 *
 */
class Data {
    private int $ano;
    private int $mes;
    private int $dia;

    /**
     * @throws Exception se a data for invalida
     */
    public function __construct(int $ano, int $mes, int $dia)
    {
        [$ano, $mes, $dia] = Data::validateData($ano, $mes, $dia);
        $this->ano = $ano;
        $this->mes = $mes;
        $this->dia = $dia;
    }

    /** Valida uma data
     * @throws Exception se a data for invalida
     */
    private static function validateData(int $ano, int $mes, int $dia) {
        if (!checkdate($mes, $dia, $ano)) {
            throw new Exception("Data invalida");
        }
        return [$ano, $mes, $dia];
    }

    /**
     * @return int
     */
    public function getAno(): int
    {
        return $this->ano;
    }
    /**
     * @return int
     */
    public function getMes(): int
    {
        return $this->mes;
    }
    /**
     * @return int
     */
    public function getDia(): int
    {
        return $this->dia;
    }

    public function toDateTime(): DateTime {
        $dateTime = new DateTime();
        $dateTime->setDate($this->getAno(), $this->getMes(), $this->getDia());
        return $dateTime;
    }

    /**
     * @throws Exception se a data for invalida
     */
    public static function fromDateTime(DateTime $dateTime): Data {
        $ano = (int) $dateTime->format('Y'); // Extract the year as an integer
        $mes = (int) $dateTime->format('m'); // Extract the month as an integer
        $dia = (int) $dateTime->format('d'); // Extract the day as an integer
        return new Data($ano, $mes, $dia);
    }

    private static array $dayOfWeekToDiaDaSemana = [
        "Sunday" => DiaDaSemana::DOMINGO,
        "Monday" => DiaDaSemana::SEGUNDA,
        "Tuesday" => DiaDaSemana::TERCA,
        "Wednesday" => DiaDaSemana::QUARTA,
        "Thursday" => DiaDaSemana::QUINTA,
        "Friday" => DiaDaSemana::SEXTA,
        "Saturday" => DiaDaSemana::SABADO,
    ];

    public function getDiaDaSemana(): DiaDaSemana {
        $dateTime = $this->toDateTime();
        $dayOfWeek = $dateTime->format('l');
        return Data::$dayOfWeekToDiaDaSemana[$dayOfWeek];
    }

    /** Retorna a soma da Duracao provido com $this
     * @param Duracao $outra
     * @return Data
     * @throws Exception se a data for invalida
     */
    public function add(Duracao $outra): Data {
        $dateTime = $this->toDateTime();
        $dateTime->modify("+{$outra->getDia()} days");
        return Data::fromDateTime($dateTime);
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return Data
     * @throws Exception se a data for invalida
     */
    public function sub(Duracao $outra): Data {
        $dateTime = $this->toDateTime();
        $dateTime->modify("-{$outra->getDia()} days");
        return Data::fromDateTime($dateTime);
    }

    /** Operador de comparação >
     * @param Data $outra
     * @return bool
     */
    public function gt(Data $outra): bool {
        return $outra->getAno() > $this->getAno() || $outra->getAno() == $this->getAno() && $outra->getMes() > $this->getMes() || $outra->getAno() == $this->getAno() && $outra->getMes() == $this->getMes() && $outra->getDia() > $this->getDia();
    }

    /** Operador de comparação >=
     * @param Data $outra
     * @return bool
     */
    public function gte(Data $outra): bool {
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param Data $outra
     * @return bool
     */
    public function st(Data $outra): bool {
        return $outra->getAno() < $this->getAno() || $outra->getAno() == $this->getAno() && $outra->getMes() < $this->getMes() || $outra->getAno() == $this->getAno() && $outra->getMes() == $this->getMes() && $outra->getDia() < $this->getDia();
    }

    /** Operador de comparação <=
     * @param Data $outra
     * @return bool
     */
    public function ste(Data $outra): bool {
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param Data $outra
     * @return bool
     */
    public function eq(Data $outra): bool {
        return $outra->getAno() == $this->getAno() && $outra->getMes() == $this->getMes() && $outra->getDia() == $this->getDia();
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getDia()}/{$this->getMes()}/{$this->getAno()}";
    }
}


/** Uma classe normalizada que determina um dia e um tempo nele.
 *
 */
class DataTempo {
    private Data $data;
    private Tempo $tempo;
    public function __construct(Data $data, Tempo $tempo)
    {
        $this->data = $data;
        $this->tempo = $tempo;
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @return Tempo
     */
    public function getTempo(): Tempo
    {
        return $this->tempo;
    }

    public function toDateTime(): DateTime {
        $dateTime = $this->data->toDateTime();
        $segundoFloat = $this->tempo->getSegundo();
        $segundo = floor($segundoFloat);
        $microsegundo = floor(($segundoFloat - $segundo) * 1000000); // Extract microseconds
        $dateTime->setTime($this->tempo->getHora(), $this->tempo->getMinuto(), $segundo, $microsegundo);
        return $dateTime;
    }

    /**
     * @throws Exception se a data for invalida
     */
    public static function fromDateTime(DateTime $dateTime): DataTempo {
        $dia = (int) $dateTime->format('d');
        $mes = (int) $dateTime->format('m');
        $ano = (int) $dateTime->format('Y');
        $hora = (int) $dateTime->format('H');
        $minuto = (int) $dateTime->format('i');
        $segundo = (float) $dateTime->format('s.u');
        return new DataTempo(new Data($ano, $mes, $dia), new Tempo($hora, $minuto, $segundo));
    }

    public function getDiaDaSemana(): DiaDaSemana {
        return $this->data->getDiaDaSemana();
    }

    /** Retorna a soma da Duracao provido com $this
     * @param Duracao $outra
     * @return DataTempo
     * @throws Exception se a data for invalida
     */
    public function add(Duracao $outra): DataTempo {
        $dateTime = $this->toDateTime();
        $dateTime->modify("+{$outra->getDia()} days");
        $dateTime->modify("+{$outra->getSegundo()} seconds");
        return DataTempo::fromDateTime($dateTime);
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return DataTempo
     * @throws Exception se a data for invalida
     */
    public function sub(Duracao $outra): DataTempo {
        $dateTime = $this->toDateTime();
        $dateTime->modify("-{$outra->getDia()} days");
        $dateTime->modify("-{$outra->getSegundo()} seconds");
        return DataTempo::fromDateTime($dateTime);
    }

    /** Operador de comparação >
     * @param DataTempo $outra
     * @return bool
     */
    public function gt(DataTempo $outra): bool {
        return $outra->getData()->gt($this->getData()) || $outra->getData()->eq($this->getData()) && $outra->getTempo()->gt($this->getTempo());
    }

    /** Operador de comparação >=
     * @param DataTempo $outra
     * @return bool
     */
    public function gte(DataTempo $outra): bool {
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param DataTempo $outra
     * @return bool
     */
    public function st(DataTempo $outra): bool {
        return $outra->getData()->st($this->getData()) || $outra->getData()->eq($this->getData()) && $outra->getTempo()->st($this->getTempo());
    }

    /** Operador de comparação <=
     * @param DataTempo $outra
     * @return bool
     */
    public function ste(DataTempo $outra): bool {
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param DataTempo $outra
     * @return bool
     */
    public function eq(Data $outra): bool {
        return $outra->getData()->eq($this->getData()) && $outra->getTempo()->eq($this->getTempo());
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getData()} {$this->getTempo()}";
    }

    /** Função de formatação
     * @param string $format
     * @return string
     */
    public function format(string $format): string {
        return $this->toDateTime()->format($format);
    }
}
?>