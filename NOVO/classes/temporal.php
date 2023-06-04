<?php

require_once "enum_to_array.php";
require_once "Comparable.php";
require_once "Equatable.php";

/** Uma classe normalizada que determina um intervalo de tempo.
 *
 */
class Duracao implements HashableAndComparable {
    private float $segundo;
    private int $dia;
    private bool $negativo;
    public function __construct(float $dia, float $segundo)
    {
        $segundosNoDia = 60*60*24;
        $segundos = $segundo + $segundosNoDia * $dia;
        $negativo = $segundos < 0;
        $segundos = abs($segundos);
        $this->segundo = $segundos % $segundosNoDia;
        $this->dia = floor($segundos / $segundosNoDia);
        $this->negativo = $negativo;
    }

    /** Retorna o valor em segundos
     * @return float
     */
    private function emSegundos(): float {
        $segundosNoDia = 60*60*24;
        return $this->getSinal() * ($this->dia*$segundosNoDia + $this->segundo);
    }

    /** Retorna o sinal (-1 ou 1)
     * @return int
     */
    public function getSinal(): int {
        return $this->negativo ? -1 : 1;
    }

    /** Retorna o numero de segundos
     * @return float
     */
    public function getSegundo(): float
    {
        return $this->segundo * $this->getSinal();
    }

    /** Retorna o numero de dias
     * @return int
     */
    public function getDia(): int
    {
        return $this->dia * $this->getSinal();
    }

    /** Retorna a soma da Duracao provida com $this
     * @param Duracao $outra
     * @return Duracao
     */
    public function add(Duracao $outra): Duracao {
        return new Duracao(0, $this->emSegundos() + $outra->emSegundos());
    }

    /** Retorna a subtracao da Duracao provida com $this
     * @param Duracao $outra
     * @return Duracao
     */
    public function sub(Duracao $outra): Duracao {
        return new Duracao(0, $this->emSegundos() - $outra->emSegundos());
    }

    /** Retorna a multiplicacao de $this pelo numero provido
     * @param float $num
     * @return Duracao
     */
    public function mul(float $num): Duracao {
        return new Duracao(0, $this->emSegundos() * $num);
    }

    /** Retorna a divisao de $this pelo numero provido
     * @param float $num
     * @return Duracao
     */
    public function div(float $num): Duracao {
        return new Duracao(0, $this->emSegundos() / $num);
    }

    /** Operador de comparação >
     * @param Duracao $outra
     * @return bool
     */
    public function gt(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->emSegundos() > $outra->emSegundos();
    }

    /** Operador de comparação >=
     * @param Duracao $outra
     * @return bool
     */
    public function gte(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param Duracao $outra
     * @return bool
     */
    public function st(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->emSegundos() < $outra->emSegundos();
    }

    /** Operador de comparação <=
     * @param Duracao $outra
     * @return bool
     */
    public function ste(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param Duracao $outra
     * @return bool
     */
    public function eq(Equatable $outra): bool {
        if (!$outra instanceof self) {
            throw new EquatableTypeException();
        }
        return $outra->dia == $this->dia && $outra->segundo == $this->segundo && $outra->negativo == $this->negativo;
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        $sinal = $this->negativo ? "-" : "+";
        return "{$sinal}{$this->dia}d{$this->segundo}s";
    }

    /** Um mes de 31 dias em Duracao
     * @return Duracao
     */
    public static function umMes(): Duracao
    {
        return new Duracao(31, 0);
    }

    /** Um dia em Duracao
     * @return Duracao
     */
    public static function umDia(): Duracao
    {
        return new Duracao(1, 0);
    }

    /** Uma hora em Duracao
     * @return Duracao
     */
    public static function umaHora(): Duracao
    {
        return new Duracao(0, 60 * 60);
    }

    /** Meia hora em Duracao
     * @return Duracao
     */
    public static function meiaHora(): Duracao
    {
        return Duracao::umaHora()->div(2);
    }

    /** Uma hora e meia em Duracao
     * @return Duracao
     */
    public static function umaHoraEMeia(): Duracao
    {
        return Duracao::umaHora()->add(Duracao::meiaHora());
    }

    /** Construtor a partir de um DateInterval
     * @param DateInterval $dateInterval
     * @return Duracao
     */
    public static function fromDateInterval(DateInterval $dateInterval): Duracao {
        $segundosEmUmMinuto = 60;
        $segundosEmUmaHora = $segundosEmUmMinuto*60;
        $microssegundosEmUmSegundo = 1000000;

        $sinal = $dateInterval->invert == 0 ? -1 : 1;
        $dias = abs($dateInterval->days);
        $segundos = $dateInterval->s + $dateInterval->i * $segundosEmUmMinuto + $dateInterval->h * $segundosEmUmaHora;
        $microssegundos = $dateInterval->f * $microssegundosEmUmSegundo;
        $segundos += $microssegundos / $microssegundosEmUmSegundo;
        return new Duracao($dias*$sinal, $segundos * $sinal);
    }

    /** Retorna o valor absoluto dessa Duracao
     * @return Duracao
     */
    public function abs(): Duracao {
        return new Duracao(0, abs($this->emSegundos()));
    }

    public function hashCode(): int
    {
        return combineHash([$this->dia, $this->segundo, $this->negativo]);
    }
}

/** Uma classe normalizada que determina um tempo em um dia.
 *
 */
class Tempo implements HashableAndComparable {
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
        $timeInSegundos = Tempo::validaTempoEmSegundos($segundo + $minuto * $segundosInMinuto + $hora * $segundosInHora);

        $this->hora = floor($timeInSegundos / $segundosInHora);
        $remainingSeconds = $timeInSegundos % $segundosInHora;

        $this->minuto = floor($remainingSeconds / $segundosInMinuto);
        $this->segundo = $remainingSeconds % $segundosInMinuto;
    }

    /** Valida o tempo em segundos
     * @throws Exception quando o tempo for negativo
     */
    private static function validaTempoEmSegundos(float $tempoEmSegundos) {
        if ($tempoEmSegundos < 0) {
            throw new Exception("O tempo deve ser positivo");
        }
        return $tempoEmSegundos;
    }

    /** Valida uma hora
     * @throws Exception when hora is more than 24
     */
    private static function validaHora(int $hora): int {
        if ($hora >= 24) {
            throw new Exception("Hora is more than 24 on time");
        }
        return $hora;
    }

    /** Retorna o valor em segundos
     * @return float
     */
    private function emSegundos(): float {
        $segundosNoMinuto = 60;
        $segundosNaHora = $segundosNoMinuto*60;
        return $this->hora * $segundosNaHora + $this->minuto * $segundosNoMinuto + $this->segundo;
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
        if ($outra->getSinal() == 1) {
            return new Tempo($this->hora + $outra->getDia()*24, $this->minuto, $this->segundo+$outra->getSegundo());
        }
        return $this->sub($outra->abs());
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function sub(Duracao $outra): Tempo {
        if ($outra->getSinal() == 1) {
            return new Tempo($this->hora - $outra->getDia()*24, $this->minuto, $this->segundo-$outra->getSegundo());
        }
        return $this->add($outra->abs());
    }

    /** Retorna a multiplicacao de $this pelo numero provido
     * @param float $num
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function mul(float $num): Tempo {
        return new Tempo($this->hora*$num, $this->minuto*$num, $this->segundo*$num);
    }

    /** Retorna a divisao de $this pelo numero provido
     * @param float $num
     * @return Tempo
     * @throws Exception when hora is more than 24
     */
    public function div(float $num): Tempo {
        return new Tempo($this->hora/$num, $this->minuto/$num, $this->segundo/$num);
    }

    /** Operador de comparação >
     * @param Tempo $outra
     * @return bool
     */
    public function gt(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }

        return $this->emSegundos() > $outra->emSegundos();
    }

    /** Operador de comparação >=
     * @param Tempo $outra
     * @return bool
     */
    public function gte(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param Tempo $outra
     * @return bool
     */
    public function st(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }

        return $this->emSegundos() < $outra->emSegundos();
    }

    /** Operador de comparação <=
     * @param Tempo $outra
     * @return bool
     */
    public function ste(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param Tempo $outra
     * @return bool
     */
    public function eq(Equatable $outra): bool {
        if (!$outra instanceof self) {
            throw new EquatableTypeException();
        }
        return $outra->hora == $this->hora && $outra->minuto == $this->minuto && $outra->segundo == $this->segundo;
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->hora}h{$this->minuto}m{$this->segundo}s";
    }

    /** Adiciona uma data a esse tempo
     * @param Data $data
     * @return DataTempo
     */
    public function comData(Data $data): DataTempo {
        return new DataTempo($data, $this);
    }

    /** Retorna agora como um Tempo
     * @return Tempo
     * @throws Exception
     */
    public static function agora(): Tempo {
        return DataTempo::agora()->getTempo();
    }

   /** Diferença entre dois tempos
     * @param Tempo $outra
     * @return Duracao
     */
    public function dt(Tempo $outra): Duracao {
        $segundosNumMinuto = 60;
        $segundosNumaHora = $segundosNumMinuto*60;

        $thisEmSegundos = $segundosNumaHora*$this->hora+$segundosNumMinuto*$this->minuto+$this->segundo;
        $outraEmSegundos = $segundosNumaHora*$outra->hora+$segundosNumMinuto*$outra->minuto+$outra->segundo;
        return (new Duracao(0, $thisEmSegundos))->sub(new Duracao(0, $outraEmSegundos));
    }

    /** Meia noite em Tempo
     * @return Tempo
     */
    public static function meiaNoite(): Tempo {
        return new Tempo(0,0,0);
    }

    public function hashCode(): int
    {
        return combineHash([$this->hora, $this->minuto, $this->segundo]);
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
class Data implements HashableAndComparable {
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


    /** Converte uma data em string numa Data
     * @param string $data
     * @return Data
     */
    static function fromString(string $data): Data {
        // Dividir a string de data nos "/"
        $partesDeData = explode('/', $data);

        // Checa se a data tem tres partes
        if (count($partesDeData) !== 3) {
            throw new InvalidArgumentException('Formato invalido de data');
        }

        // Extrair o dia, o mes e o ano
        $dia = intval($partesDeData[0]);
        $mes = intval($partesDeData[1]);
        $ano = intval($partesDeData[2]);

        // Checa se o dia, o mes e o ano são validos
        if ($dia <= 0 || $mes <= 0 || $ano <= 0) {
            throw new InvalidArgumentException('Formato invalido de data');
        }

        // Return the parsed date as an array
        return new Data($ano, $mes, $dia);
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
        $dateTime->setDate($this->ano, $this->mes, $this->dia);
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
        if ($outra->getSinal() == 1) {
            $dateTime = $this->toDateTime();
            $dateTime->modify("+{$outra->getDia()} days");
            return Data::fromDateTime($dateTime);
        }
        return $this->sub($outra->abs());
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return Data
     * @throws Exception se a data for invalida
     */
    public function sub(Duracao $outra): Data {
        if ($outra->getSinal() == 1) {
            $dateTime = $this->toDateTime();
            $dateTime->modify("-{$outra->getDia()} days");
            return Data::fromDateTime($dateTime);
        }
        return $this->add($outra->abs());
    }

    /** Operador de comparação >
     * @param Data $outra
     * @return bool
     */
    public function gt(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }

        return $this->ano > $outra->ano ||
            ($this->ano == $outra->ano && $this->mes > $outra->mes) ||
            ($this->ano == $outra->ano && $this->mes == $outra->mes && $this->dia > $outra->dia);
    }

    /** Operador de comparação >=
     * @param Data $outra
     * @return bool
     */
    public function gte(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param Data $outra
     * @return bool
     */
    public function st(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }

        return $this->ano < $outra->ano ||
            ($this->ano == $outra->ano && $this->mes < $outra->mes) ||
            ($this->ano == $outra->ano && $this->mes == $outra->mes && $this->dia < $outra->dia);
    }

    /** Operador de comparação <=
     * @param Data $outra
     * @return bool
     */
    public function ste(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param Data $outra
     * @return bool
     */
    public function eq(Equatable $outra): bool {
        if (!$outra instanceof self) {
            throw new EquatableTypeException();
        }
        return $outra->ano == $this->ano && $outra->mes == $this->mes && $outra->dia == $this->dia;
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->dia}/{$this->mes}/{$this->ano}";
    }


    /** Adiciona uma tempo a essa data
     * @param Tempo $tempo
     * @return DataTempo
     */
    public function comTempo(Tempo $tempo): DataTempo {
        return new DataTempo($this, $tempo);
    }

    /** Retorna hoje como uma Data
     * @return Data
     * @throws Exception
     */
    public static function hoje(): Data {
        return DataTempo::agora()->getData();
    }

   /** Diferença entre duas datas
     * @param Data $outra
     * @return Duracao
     */
    public function dt(Data $outra): Duracao {
        $meiaNoite = Tempo::meiaNoite();
        return $this->comTempo($meiaNoite)->dt($outra->comTempo($meiaNoite));
    }

    public function hashCode(): int
    {
        return combineHash([$this->dia, $this->mes, $this->ano]);
    }
}


/** Uma classe normalizada que determina um dia e um tempo nele.
 *
 */
class DataTempo implements HashableAndComparable {
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
        if ($outra->getSinal() == 1) {
            $dateTime = $this->toDateTime();
            $dateTime->modify("+{$outra->getDia()} days");
            $dateTime->modify("+{$outra->getSegundo()} seconds");
            return DataTempo::fromDateTime($dateTime);
        }
        return $this->sub($outra->abs());
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return DataTempo
     * @throws Exception se a data for invalida
     */
    public function sub(Duracao $outra): DataTempo {
        if ($outra->getSinal() == 1) {
            $dateTime = $this->toDateTime();
            $dateTime->modify("-{$outra->getDia()} days");
            $dateTime->modify("-{$outra->getSegundo()} seconds");
            return DataTempo::fromDateTime($dateTime);
        }
        return $this->add($outra->abs());
    }

    /** Operador de comparação >
     * @param DataTempo $outra
     * @return bool
     */
    public function gt(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->data->gt($outra->data) ||
            ($this->data->eq($outra->data) && $this->tempo->gt($outra->tempo));
    }

    /** Operador de comparação >=
     * @param DataTempo $outra
     * @return bool
     */
    public function gte(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param DataTempo $outra
     * @return bool
     */
    public function st(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->data->st($outra->data) ||
            ($this->data->eq($outra->data) && $this->tempo->st($outra->tempo));
    }

    /** Operador de comparação <=
     * @param DataTempo $outra
     * @return bool
     */
    public function ste(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param DataTempo $outra
     * @return bool
     */
    public function eq(Equatable $outra): bool {
        if (!$outra instanceof self) {
            throw new EquatableTypeException();
        }
        return $outra->data->eq($this->data) && $outra->tempo->eq($this->tempo);
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->data} {$this->tempo}";
    }

    /** Função de formatação
     * @param string $format
     * @return string
     */
    public function format(string $format): string {
        return $this->toDateTime()->format($format);
    }


    /** Retorna agora como um DataTempo
     * @return DataTempo
     * @throws Exception
     */
    public static function agora(): DataTempo {
        return DataTempo::fromDateTime(new DateTime());
    }

    /** Diferença entre dois datatempos
     * @param DataTempo $outra
     * @return Duracao
     */
    public function dt(DataTempo $outra): Duracao {
        $dateInterval = $this->toDateTime()->diff($outra->toDateTime());
        return Duracao::fromDateInterval($dateInterval);
    }

    /** Retorna o intervalo de tempo de this até outra
     * @param DataTempo $outra
     * @return IntervaloDeTempo
     * @throws Exception se o inicio for após o fim
     */
    public function ate(DataTempo $outra): IntervaloDeTempo {
        return new IntervaloDeTempo($this, $outra);
    }

    public function hashCode(): int
    {
        return combineHash([$this->data, $this->tempo]);
    }
}

/** Uma classe que representa um intervalo de tempo com inicio e fim.
 *
 */
class IntervaloDeTempo implements HashableAndComparable {
    private DataTempo $inicio;
    private DataTempo $fim;

    /**
     * @throws Exception se o inicio for após o fim
     */
    public function __construct(DataTempo $inicio, DataTempo $fim)
    {
        [$inicio, $fim] = IntervaloDeTempo::validaIntervalo($inicio, $fim);
        $this->inicio = $inicio;
        $this->fim = $fim;
    }

    /** Valida um intervalo. Checa se o inicio <= fim
     * @param DataTempo $inicio
     * @param DataTempo $fim
     * @return DataTempo[]
     * @throws Exception se o inicio for após o fim
     */
    private static function validaIntervalo(DataTempo $inicio, DataTempo $fim): array {
        if ($inicio->gt($fim)) {
            throw new Exception("O inicio é depois do fim");
        }
        return [$inicio, $fim];
    }

    /** Retorna o inicio do intervalo
     * @return DataTempo
     */
    public function getInicio(): DataTempo
    {
        return $this->inicio;
    }

    /** Retorna o fim do intervalo
     * @return DataTempo
     */
    public function getFim(): DataTempo
    {
        return $this->fim;
    }

    /** Retorna a soma da Duracao provido com $this
     * @param Duracao $outra
     * @return IntervaloDeTempo
     * @throws Exception se a data for invalida
     */
    public function add(Duracao $outra): IntervaloDeTempo {
        return new IntervaloDeTempo($this->inicio->add($outra), $this->fim->add($outra));
    }

    /** Retorna a subtracao da Duracao provido com $this
     * @param Duracao $outra
     * @return IntervaloDeTempo
     * @throws Exception se a data for invalida
     */
    public function sub(Duracao $outra): IntervaloDeTempo {
        return new IntervaloDeTempo($this->inicio->sub($outra), $this->fim->sub($outra));
    }

    /** Operador de comparação >
     * @param IntervaloDeTempo $outra
     * @return bool
     */
    public function gt(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->inicio->gt($outra->fim);
    }

    /** Operador de comparação >=
     * @param IntervaloDeTempo $outra
     * @return bool
     */
    public function gte(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->gt($outra) || $this->eq($outra);
    }

    /** Operador de comparação <
     * @param IntervaloDeTempo $outra
     * @return bool
     */
    public function st(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->fim->st($outra->inicio);
    }

    /** Operador de comparação <=
     * @param IntervaloDeTempo $outra
     * @return bool
     */
    public function ste(Comparable $outra): bool {
        if (!$outra instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->st($outra) || $this->eq($outra);
    }

    /** Operador de igualdade ==
     * @param IntervaloDeTempo $outra
     * @return bool
     */
    public function eq(Equatable $outra): bool {
        if (!$outra instanceof self) {
            throw new EquatableTypeException();
        }
        return $outra->inicio->eq($this->inicio) && $outra->fim->eq($this->fim);
    }

    /** Conversão em string
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->inicio} até {$this->fim}";
    }

    /** Retorna true se outra está contida em this
     * @param DataTempo $outra
     * @return bool
     */
    public function contem(DataTempo $outra): bool {
        return $this->inicio->ste($outra) && $this->fim->gte($outra);
    }

    public function hashCode(): int
    {
        return combineHash([$this->inicio, $this->fim]);
    }
}
?>