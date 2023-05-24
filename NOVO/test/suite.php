<?php
function absolute_to_relative_path(string $absolute_path, string $base_path)
{
    // Get the real, canonicalized paths
    $absolute_path = realpath($absolute_path);
    $base_path = realpath($base_path);

    // Split the paths into arrays of directories
    $absolute_parts = explode(DIRECTORY_SEPARATOR, $absolute_path);
    $base_parts = explode(DIRECTORY_SEPARATOR, $base_path);

    // Find the common path length
    $common_length = 0;
    $length = min(count($absolute_parts), count($base_parts));
    for ($i = 0; $i < $length; $i++) {
        if ($absolute_parts[$i] !== $base_parts[$i]) {
            break;
        }
        $common_length++;
    }

    // Build the relative path
    $relative_parts = array_fill(0, count($base_parts) - $common_length, '..');
    $relative_parts = array_merge($relative_parts, array_slice($absolute_parts, $common_length));

    // Join the relative parts into a single string
    $relative_path = implode(DIRECTORY_SEPARATOR, $relative_parts);

    return $relative_path;
}

class CheckResult
{
    private bool $success;
    private string $stringRepresentation;
    private int $line;
    private string $file;

    public function __construct(bool $success, string $stringRepresentation, int $line, string $file)
    {
        $this->success = $success;
        $this->stringRepresentation = $stringRepresentation;
        $this->line = $line;
        $this->file = $file;
    }

    public function __toString(): string
    {
        $check = $this->success ? "✅" : "❌";
        return "{$this->file}:{$this->line}[{$check}] {$this->stringRepresentation}";
    }

}

class TestRunner
{
    private array $testCases = [];

    public function addCase(TestCase $case): self
    {
        $this->testCases[] = $case;
        return $this;
    }

    public function run()
    {
        echo "BEGIN TESTS:\n";
        foreach ($this->testCases as $case) {
            $case->run();
            $case->printResults();
        }
        echo "END TESTS;\n";
    }
}

abstract class TestCase
{
    private array $checkResults = [];

    private function objOrEnumToString(mixed $obj)
    {
        # Enum
        if (is_object($obj) && property_exists($obj, "value")) {
            return $obj->value;
        }
        return "{$obj}";
    }

    protected function checkGt(mixed $a, mixed $b): void
    {
        $symbol = ">";
        $success = null;
        if ($a instanceof Comparable && $b instanceof Comparable) {
            $success = $a->gt($b);
        } else {
            $success = $a > $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkGte(mixed $a, mixed $b): void
    {
        $symbol = ">=";
        $success = null;
        if ($a instanceof Comparable && $b instanceof Comparable) {
            $success = $a->gte($b);
        } else {
            $success = $a >= $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkSt(mixed $a, mixed $b): void
    {
        $symbol = "<";
        $success = null;
        if ($a instanceof Comparable && $b instanceof Comparable) {
            $success = $a->st($b);
        } else {
            $success = $a < $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkSte(mixed $a, mixed $b): void
    {
        $symbol = "<=";
        $success = null;
        if ($a instanceof Comparable && $b instanceof Comparable) {
            $success = $a->ste($b);
        } else {
            $success = $a <= $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkEq(mixed $a, mixed $b, bool $strict = true): void
    {
        $symbol = $strict ? "===" : "==";
        $success = null;
        if ($a instanceof Equatable && $b instanceof Equatable) {
            $success = $a->eq($b);
        } else if (is_string($a) && is_string($b)) {
            $success = strcmp($a, $b) === 0;
        } else {
            $success = $strict ? $a === $b : $a == $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkNeq(mixed $a, mixed $b, bool $strict = true): void
    {
        $symbol = $strict ? "!==" : "!=";
        $success = null;
        if ($a instanceof Equatable && $b instanceof Equatable) {
            $success = !$a->eq($b);
        } else if (is_string($a) && is_string($b)) {
            $success = strcmp($a, $b) !== 0;
        } else {
            $success = $strict ? $a === $b : $a == $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkApproximate(float $a, float $b, float $epsilon = 0.001): void
    {
        $success = abs($a - $b) < $epsilon;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "|{$this->objOrEnumToString($a)} - {$this->objOrEnumToString($b)}| < {$epsilon}", $line, $file);
    }

    protected function checkTrue(bool $bool)
    {
        $success = $bool;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "should be true", $line, $file);
    }

    protected function checkFalse(bool $bool)
    {
        $success = !$bool;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "should be false", $line, $file);
    }

    protected function checkNotReached()
    {
        $success = false;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "should not be reached", $line, $file);
    }

    protected function checkReached()
    {
        $success = true;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "should be reached", $line, $file);
    }

    private static function getTestFile(): string
    {
        $bt = debug_backtrace();
        return $bt[0]['file'];
    }

    private static function getTestFolder(): string
    {
        return dirname(TestCase::getTestFile());
    }

    private function getLineAndFileForPreviousFunction(): array
    {
        $bt = debug_backtrace();
        $caller = $bt[1];
        return [$caller['line'], absolute_to_relative_path($caller['file'], TestCase::getTestFolder())];
    }

    abstract protected function getName(): string;

    abstract public function run();

    public function printResults()
    {
        echo "  {$this->getName()} Checks:\n";
        foreach ($this->checkResults as $checkResult) {
            echo "    {$checkResult}\n";
        }
    }
}