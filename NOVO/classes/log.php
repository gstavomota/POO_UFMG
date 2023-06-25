<?php
require_once "AuthenticationManager.php";
require_once "temporal.php";
function objOrEnumToString(mixed $obj)
{
    if (is_bool($obj)) {
        return $obj ? "true" : "false";
    }
    # Enum
    if (is_object($obj) && property_exists($obj, "value")) {
        return $obj->value;
    }
    $quotes = is_string($obj) ? "\"" : "";
    if (is_array($obj)) {
        $arr = $obj;
        $obj = "{";
        foreach ($arr as $key => $value) {
            $obj = $obj . $key . ": " . objOrEnumToString($value) . ", ";
        }
        $obj = $obj . "}";
    }
    try {
        return "$quotes{$obj}$quotes";
    } catch (Error $e) {
        $class = get_class($obj);
        $hash = spl_object_hash($obj);
        return "{$class}#{$hash}";
    }
}
enum Operation: string {
    case READ = "read";
    case WRITE = "write";
    case CALL = "call";
}
abstract class LogEntry {
    public function __construct(Operation $operation, string $sujeito, string $metodo) {
        $this->momento = DataTempo::agora();
        $this->operation = $operation;
        $this->usuario = AuthenticationManager::getInstance()->getUsuarioAtual();
        $this->sujeito = $sujeito;
        $this->metodo = $metodo;
    }
    private DataTempo $momento;
    private Operation $operation;
    private ?Usuario $usuario;
    /**
     * @var class-string
     */
    private string $sujeito;
    private string $metodo;
    private function prefix(): string {
        $usuario = $this->usuario ? $this->usuario->getUsuario() : "Usuario anonimo";
        return "[$this->momento][".$usuario."][".$this->operation->name."]"."[$this->sujeito.$this->metodo]";
    }
    abstract protected function suffix(): string;
    public function __toString(): string {
        return $this->prefix()." ".$this->suffix();
    }
}
class LogEntryLeitura extends LogEntry {
    private mixed $valor;
public function __construct(mixed $valor, string $sujeito, string $metodo)
{
    $this->valor = $valor;
    parent::__construct(Operation::READ, $sujeito, $metodo);
}

    protected function suffix(): string
    {
        return "valor ".objOrEnumToString($this->valor);
    }
}

class LogEntryEscrita extends LogEntry {
    private mixed $antes;
    private mixed $depois;
    public function __construct(mixed $antes, mixed $depois, string $sujeito, string $metodo)
    {
        $this->antes = $antes;
        $this->depois = $depois;
        parent::__construct(Operation::WRITE, $sujeito, $metodo);
    }

    protected function suffix(): string
    {
        return "de ".objOrEnumToString($this->antes)." para ".objOrEnumToString($this->depois);
    }
}

class LogEntryCall extends LogEntry {
    private mixed $retorno;
    private bool $throw;
    public function __construct(mixed $retorno, bool $throw, string $sujeito, string $metodo)
    {
        $this->retorno = $retorno;
        $this->throw = $throw;
        parent::__construct(Operation::CALL, $sujeito, $metodo);
    }

    protected function suffix(): string
    {
        $symbol = $this->throw ? "throw " : "-> ";
        return $symbol.objOrEnumToString($this->retorno);
    }
}
interface LogOutputter {
    public function output(LogEntry $entry): void;
}
class StdoutLogOutputter implements LogOutputter {
    public function output(LogEntry $entry): void {
        echo $entry;
        echo PHP_EOL;
    }
}
class log extends persist {
    /**
     * @var LogEntry[]
     */
    private array $entries = [];
    private LogOutputter $logOutputter;
    private static string $filename = "log.txt";
    private function __construct()
    {
        $this->logOutputter = new StdoutLogOutputter();
        parent::__construct(0);
    }
    private static ?log $instance = null;

    static public function getInstance(): log {
        if (!is_null(static::$instance)) {
            return static::$instance;
        }
        if (count(static::getRecords()) > 1) {
            static::deleteAllRecords();
        }
        if (count(static::getRecords()) == 0) {
            return static::$instance = new log();
        }
        return static::getRecords()[0];
    }

    /**
     * @template T
     * @param T $exception
     * @never-return
     */
    public function logThrow(mixed $exception): never {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $className = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '';
        $methodName = isset($backtrace[1]['function']) ? $backtrace[1]['function'] : '';
        $entry = new LogEntryCall($exception, true, $className, $methodName);
        $this->entries[] = $entry;
        $this->logOutputter->output($entry);
        throw $exception;
    }

    /**
     * @template T
     * @param T $retorno
     * @return T
     */
    public function logCall(mixed $retorno): mixed {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $className = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '';
        $methodName = isset($backtrace[1]['function']) ? $backtrace[1]['function'] : '';
        $entry = new LogEntryCall($retorno, false, $className, $methodName);
        $this->entries[] = $entry;
        $this->logOutputter->output($entry);
        return $retorno;
    }

    /**
     * @template T
     * @param T $valor
     * @return T
     */
    public function logRead(mixed $valor): mixed {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $className = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '';
        $methodName = isset($backtrace[1]['function']) ? $backtrace[1]['function'] : '';
        $entry = new LogEntryLeitura($valor, $className, $methodName);
        $this->entries[] = $entry;
        $this->logOutputter->output($entry);
        return $valor;
    }
    /**
     * @template T
     * @param T $antes
     * @param T $depois
     * @return T
     */
    public function logWrite(mixed $antes, mixed $depois): mixed {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $className = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '';
        $methodName = isset($backtrace[1]['function']) ? $backtrace[1]['function'] : '';
        $entry = new LogEntryEscrita($antes, $depois, $className, $methodName);
        $this->entries[] = $entry;
        $this->logOutputter->output($entry);
        return $depois;
    }

    public function setLogOutputter(LogOutputter $logOutputter) {
        $this->logOutputter = $logOutputter;
    }
    /**
     * @return LogEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    static public function getFilename(): string
    {
        return static::$filename;
    }
}