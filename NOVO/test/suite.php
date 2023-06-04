<?php

/** An interface for logging the output of the tests
 *
 */
interface TestOutputLogger
{
    /** Add an string to the output
     * @param string $string
     * @return void
     */
    public function echo(string $string): void;
}

/** An TestOutputLogger that logs to the Stdout
 *
 */
class StdoutTestOutputLogger implements TestOutputLogger
{
    public function echo(string $string): void
    {
        echo $string;
    }
}

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

interface CheckResultOrSection
{
    public function getSuccess(): ?bool;
}

class CheckResult implements CheckResultOrSection
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

    public function getSuccess(): ?bool
    {
        return $this->success;
    }
}

class CheckSection implements CheckResultOrSection
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return "  {$this->name}";
    }

    public function getSuccess(): ?bool
    {
        return null;
    }
}

enum CheckShowPolicy: string
{
    case ALL = "all";
    case SUCCESS = "success";
    case FAILURE = "failure";
    case NONE = "none";
}

class TestRunner
{
    /**
     * @var TestCase[]
     */
    private array $testCases = [];
    private bool $showSections = true;
    private CheckShowPolicy $checkShowPolicy = CheckShowPolicy::ALL;

    private TestOutputLogger $testOutputLogger;

    public function __construct()
    {
        $this->testOutputLogger = new StdoutTestOutputLogger();
    }

    /**
     * @param CheckShowPolicy $checkShowPolicy
     */
    public function setCheckShowPolicy(CheckShowPolicy $checkShowPolicy): self
    {
        $this->checkShowPolicy = $checkShowPolicy;
        return $this;
    }

    /**
     * @param bool $showSections
     */
    public function setShowSections(bool $showSections): self
    {
        $this->showSections = $showSections;
        return $this;
    }

    /** Sets the TestOutputLogger to be used
     * @param TestOutputLogger $testOutputLogger
     * @return TestRunner
     */
    public function setTestOutputLogger(TestOutputLogger $testOutputLogger): self
    {
        $this->testOutputLogger = $testOutputLogger;
        return $this;
    }

    public function addCase(TestCase $case): self
    {
        $this->testCases[] = $case;
        return $this;
    }

    public function run()
    {
        $this->testOutputLogger->echo("BEGIN TESTS:\n");
        $success = 0;
        $failure = 0;
        foreach ($this->testCases as $i => $case) {
            $case->run();
            $case->printResults($this->showSections, $this->checkShowPolicy, $this->testOutputLogger);
            if ($i != count($this->testCases) - 1) {
                $this->testOutputLogger->echo("\n");
            }
            [$caseSuccess, $caseFailure] = $case->getSuccessesAndFailures();
            $success += $caseSuccess;
            $failure += $caseFailure;
        }
        $this->testOutputLogger->echo("END TESTS;\n");
        $this->testOutputLogger->echo("SUMMARY:\n" .
            "  SUCCESS[✅] = {$success}\n" .
            "  FAILURE[❌] = {$failure}\n");
    }
}

abstract class TestCase
{
    private array $checkResultsOrSections = [];

    private function objOrEnumToString(mixed $obj)
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
                $obj = $obj . $key . ": " . $this->objOrEnumToString($value) . ", ";
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
        $this->checkResultsOrSections[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
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
        $this->checkResultsOrSections[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
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
        $this->checkResultsOrSections[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
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
        $this->checkResultsOrSections[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    private function equals(mixed $a, mixed $b, bool $strict): bool
    {
        if ($a instanceof Equatable && $b instanceof Equatable) {
            return $a->eq($b);
        }
        if (is_string($a) && is_string($b)) {
            return strcmp($a, $b) === 0;
        }
        if (is_array($a) && is_array($b)) {
            $a_keys = array_keys($a);
            $b_keys = array_keys($b);

            if (count($a_keys) !== count($b_keys)) {
                return false;
            }

            sort($a_keys);
            sort($b_keys);

            for ($i = 0; $i < count($a_keys); $i++) {
                if (!$this->equals($a_keys[$i], $b_keys[$i], $strict)) {
                    return false;
                }
            }

            foreach ($a_keys as $key) {
                if (!$this->equals($a[$key], $b[$key], $strict)) {
                    return false;
                }
            }

            return true;
        }
        return $strict ? $a === $b : $a == $b;
    }

    protected function checkEq(mixed $a, mixed $b, bool $strict = true): void
    {
        $symbol = $strict ? "===" : "==";
        $success = $this->equals($a, $b, $strict);
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
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
            $success = $strict ? $a !== $b : $a != $b;
        }
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "{$this->objOrEnumToString($a)} {$symbol} {$this->objOrEnumToString($b)}", $line, $file);
    }

    protected function checkApproximate(float $a, float $b, float $epsilon = 0.001): void
    {
        $success = abs($a - $b) < $epsilon;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "|{$this->objOrEnumToString($a)} - {$this->objOrEnumToString($b)}| < {$epsilon}", $line, $file);
    }

    protected function checkTrue(bool $bool)
    {
        $success = $bool;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "should be true", $line, $file);
    }

    protected function checkFalse(bool $bool)
    {
        $success = !$bool;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "should be false", $line, $file);
    }

    protected function checkNotReached()
    {
        $success = false;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "should not be reached", $line, $file);
    }

    protected function checkReached()
    {
        $success = true;
        [$line, $file] = $this->getLineAndFileForPreviousFunction();
        $this->checkResultsOrSections[] = new CheckResult($success, "should be reached", $line, $file);
    }

    protected function runNonPublicStaticMethod(string $class, string $method, mixed ...$args): mixed
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        if (!$reflectionMethod->isStatic()) {
            throw new ReflectionException("The method is not static");
        }
        if ($reflectionMethod->isPublic()) {
            throw new ReflectionException("The method is public");
        }
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke(null, ...$args);
    }

    protected function runNonPublicMethod(object $object, string $method, mixed ...$args): mixed
    {
        $reflectionMethod = new ReflectionMethod($object, $method);
        if ($reflectionMethod->isStatic()) {
            throw new ReflectionException("The method is static");
        }
        if ($reflectionMethod->isPublic()) {
            throw new ReflectionException("The method is public");
        }
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke($object, ...$args);
    }

    protected function getNonPublicProperty(object $object, string $property, string $class = null): mixed
    {
        $reflectionProperty = new ReflectionProperty($class ?? $object, $property);
        if ($reflectionProperty->isPublic()) {
            throw new ReflectionException("The property is public");
        }
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($object);
    }

    protected function getPropertyDefault(string $class, string $property): mixed
    {
        $reflectionProperty = new ReflectionProperty($class, $property);
        return $reflectionProperty->getDefaultValue();
    }

    protected function startSection(string $name)
    {
        $this->checkResultsOrSections[] = new CheckSection($name);
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

    /** Returns the number of successes and failures
     * @return int[]
     */
    public function getSuccessesAndFailures(): array
    {
        $success = 0;
        $failure = 0;
        foreach ($this->checkResultsOrSections as $checkResultOrSection) {
            $successOrNot = $checkResultOrSection->getSuccess();
            if ($successOrNot === true) {
                $success++;
            }
            if ($successOrNot === false) {
                $failure++;
            }
        }
        return [$success, $failure];
    }

    public function printResults(bool $showSections, CheckShowPolicy $checkShowPolicy, TestOutputLogger $testOutputLogger)
    {

        $testOutputLogger->echo("  {$this->getName()} Checks:\n");

        // Filter out the unwanted checks
        $baseCheckResultsOrSections = [];
        foreach ($this->checkResultsOrSections as $checkResultOrSection) {
            $successOrNull = $checkResultOrSection->getSuccess();

            switch ($checkShowPolicy) {
                case CheckShowPolicy::ALL:
                    $baseCheckResultsOrSections[] = $checkResultOrSection;
                    break;
                case CheckShowPolicy::SUCCESS:
                    if ($successOrNull === null || $successOrNull === true) {
                        $baseCheckResultsOrSections[] = $checkResultOrSection;
                    }
                    break;
                case CheckShowPolicy::FAILURE:
                    if ($successOrNull === null || $successOrNull === false) {
                        $baseCheckResultsOrSections[] = $checkResultOrSection;
                    }
                    break;
            }
        }

        $checkResultsOrSectionsWithNormalizedSections = [];
        $previousIsSection = false;

        foreach ($baseCheckResultsOrSections as $index => $item) {
            if (is_null($item->getSuccess())) {
                // Check if the current item is a section
                if (!$previousIsSection) {
                    $checkResultsOrSectionsWithNormalizedSections[] = $item;
                    $previousIsSection = true;
                }
            } else {
                // Check if the current item is a check result
                $checkResultsOrSectionsWithNormalizedSections[] = $item;
                $previousIsSection = false;
            }

            // Check if the last item is an empty section
            if ($index === count($baseCheckResultsOrSections) - 1 && is_null($item->getSuccess())) {
                array_pop($checkResultsOrSectionsWithNormalizedSections);
            }
        }

        $checkResultsOrSections = [];

        if ($showSections) {
            $checkResultsOrSections = $checkResultsOrSectionsWithNormalizedSections;
        } else {
            foreach ($checkResultsOrSectionsWithNormalizedSections as $checkResultOrSection) {
                if ($checkResultOrSection->getSuccess() === null) {
                    continue;
                }
                $checkResultsOrSections[] = $checkResultOrSection;
            }
        }


        [$success, $failure] = $this->getSuccessesAndFailures();
        foreach ($checkResultsOrSections as $checkResultOrSection) {
            $testOutputLogger->echo("    {$checkResultOrSection}\n");
        }
        $testOutputLogger->echo("    SUMMARY:\n" .
            "      SUCCESS[✅] = {$success}\n" .
            "      FAILURE[❌] = {$failure}\n");
    }
}