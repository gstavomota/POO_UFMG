<?php
function absolute_to_relative_path(string $absolute_path, string $base_path) {
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
class CheckResult {
    private bool $success;
    private string $stringRepresentation;
    private int $line;
    private string $file;

    public function __construct(bool $success, string $stringRepresentation, int $line, string $file) {
        $this->success = $success;
        $this->stringRepresentation = $stringRepresentation;
        $this->line = $line;
        $this->file = $file;
    }

    public function __toString(): string {
        $check = $this->success ? "✅" : "❌";
        return "{$this->file}:{$this->line}[{$check}] {$this->stringRepresentation}";
    }

}
class TestRunner {
    private array $testCases = [];
    public function addCase(TestCase $case): self {
        $this->testCases[] = $case;
        return $this;
    }

    public function run() {
        echo "BEGIN TESTS:\n";
        foreach ($this->testCases as $case) {
            $case->run();
            $case->printResults();
        }
        echo "END TESTS;\n";
    }
}
abstract class TestCase {
    private array $checkResults = [];
    protected function checkGt(Comparable $a, Comparable $b): void
    {
        $symbol = ">";
        $success = $a->gt($b);
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkGte(Comparable $a, Comparable $b): void
    {
        $symbol = ">=";
        $success = $a->gte($b);
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkSt(Comparable $a, Comparable $b): void
    {
        $symbol = "<";
        $success = $a->st($b);
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkSte(Comparable $a, Comparable $b): void
    {
        $symbol = "<=";
        $success = $a->ste($b);
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkEq(Comparable $a, Comparable $b): void
    {
        $symbol = "==";
        $success = $a->eq($b);
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkNSEq(mixed $a, mixed $b): void
    {
        $symbol = "~=";
        $success = $a == $b;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkNSNeq(mixed $a, mixed $b): void
    {
        $symbol = "!~=";
        $success = $a != $b;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkSEq(mixed $a, mixed $b): void
    {
        $symbol = "===";
        $success = $a === $b;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkSNeq(mixed $a, mixed $b): void
    {
        $symbol = "!==";
        $success = $a !== $b;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "{$a} {$symbol} {$b}", $line, $file);
    }
    protected function checkApproximate(float $a, float $b, float $epsilon = 0.001): void
    {
        $success = abs($a - $b) < $epsilon;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "|{$a} - {$b}| < {$epsilon}", $line, $file);
    }
    protected function checkTrue(bool $bool) {
        $success = $bool;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "should be true", $line, $file);
    }
    protected function checkFalse(bool $bool) {
        $success = !$bool;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResults[] = new CheckResult($success, "should be false", $line, $file);
    }
    private static function getTestFile(): string {
        $bt = debug_backtrace();
        return $bt[0]['file'];
    }
    private static function getTestFolder(): string {
        return dirname(TestCase::getTestFile());
    }
    private function getLineAndFileForPreviousFunction(): array {
        $bt = debug_backtrace();
        $caller = $bt[1];
        return [$caller['line'], absolute_to_relative_path($caller['file'], TestCase::getTestFolder())];
    }
    abstract protected  function getName(): string;
    abstract public function run();
    public function printResults() {
        echo "  {$this->getName()} Checks:\n";
        foreach ($this->checkResults as $checkResult) {
            echo "    {$checkResult}\n";
        }
    }
}