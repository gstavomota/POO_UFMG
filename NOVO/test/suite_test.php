<?php
require_once "suite.php";
require_once "../classes/Comparable.php";
class StringTestOutputLogger implements  TestOutputLogger {
    private string $contents = "";
    public function echo(string $string): void
    {
        $this->contents = $this->contents.$string;
    }

    /** Gets the contents
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /** Clears the contents
     * @return void
     */
    public function clear(): void {
        $this->contents = "";
    }
}
class SectionNormalizationTestCase extends TestCase {
    protected function getName(): string
    {
        return "SectionNormalization";
    }

    public function run()
    {
        $this->startSection("foo");
        $this->startSection("bar");
        $this->checkReached();
        $this->startSection("baz");
    }
}

class SectionDisablingTestCase extends TestCase {
    protected function getName(): string
    {
        return "SectionDisabling";
    }

    public function run()
    {
        $this->startSection("foo");
        $this->checkReached();
    }
}


class CheckPolicyTestCase extends TestCase {
    protected function getName(): string
    {
        return "CheckPolicy";
    }

    public function run()
    {
        $this->checkReached();
        $this->checkNotReached();
    }
}

class ComparissionTestCase extends TestCase {
    protected function getName(): string
    {
        return "CheckPolicy";
    }

    public function run()
    {
        $this->startSection("Raw successful int comparissions");
        $this->checkEq(-1, -1);
        $this->checkEq(0, 0);
        $this->checkEq(1, 1);
        $this->checkNeq(-1, 0);
        $this->checkNeq(0, 1);
        $this->checkNeq(-1, 1);
        $this->checkGt(1, 0);
        $this->checkGte(1, 0);
        $this->checkGte(0, 0);
        $this->checkSt(0, 1);
        $this->checkSte(0, 1);
        $this->checkSte(0, 0);
        $this->startSection("Raw successful string comparissions");
        $this->checkEq("a", "a");
        $this->checkEq("b", "b");
        $this->checkEq("A", "A");
        $this->checkNeq("a", "b");
        $this->checkNeq("b", "A");
        $this->checkNeq("A", "a");
        $this->checkGt("b", "a");
        $this->checkGte("b", "a");
        $this->checkGte("a", "a");
        $this->checkSt("a", "b");
        $this->checkSte("a", "b");
        $this->checkSte("a", "a");
        $this->startSection("Raw unsuccessful int comparissions");
        $this->checkNeq(-1, -1);
        $this->checkNeq(0, 0);
        $this->checkNeq(1, 1);
        $this->checkEq(-1, 0);
        $this->checkEq(0, 1);
        $this->checkEq(-1, 1);
        $this->checkSt(1, 0);
        $this->checkSte(1, 0);
        $this->checkGt(0, 1);
        $this->checkGte(0, 1);
        $this->startSection("Raw unsuccessful string comparissions");
        $this->checkNeq("a", "a");
        $this->checkNeq("b", "b");
        $this->checkNeq("A", "A");
        $this->checkEq("a", "b");
        $this->checkEq("b", "A");
        $this->checkEq("A", "a");
        $this->checkSt("b", "a");
        $this->checkSte("b", "a");
        $this->checkGt("a", "b");
        $this->checkGte("a", "b");
        $this->startSection("Wrapped successful int comparissions");
        $this->checkEq(new ComparableWrapper(-1),new ComparableWrapper( -1));
        $this->checkEq(new ComparableWrapper(0), new ComparableWrapper(0));
        $this->checkEq(new ComparableWrapper(1), new ComparableWrapper(1));
        $this->checkNeq(new ComparableWrapper(-1),new ComparableWrapper( 0));
        $this->checkNeq(new ComparableWrapper(0), new ComparableWrapper(1));
        $this->checkNeq(new ComparableWrapper(-1),new ComparableWrapper( 1));
        $this->checkGt(new ComparableWrapper(1), new ComparableWrapper(0));
        $this->checkGte(new ComparableWrapper(1), new ComparableWrapper(0));
        $this->checkGte(new ComparableWrapper(0), new ComparableWrapper(0));
        $this->checkSt(new ComparableWrapper(0), new ComparableWrapper(1));
        $this->checkSte(new ComparableWrapper(0), new ComparableWrapper(1));
        $this->checkSte(new ComparableWrapper(0), new ComparableWrapper(0));
        $this->startSection("Wrapped successful string comparissions");
        $this->checkEq(new ComparableWrapper("a"), new ComparableWrapper("a"));
        $this->checkEq(new ComparableWrapper("b"), new ComparableWrapper("b"));
        $this->checkEq(new ComparableWrapper("A"), new ComparableWrapper("A"));
        $this->checkNeq(new ComparableWrapper("a"), new ComparableWrapper("b"));
        $this->checkNeq(new ComparableWrapper("b"), new ComparableWrapper("A"));
        $this->checkNeq(new ComparableWrapper("A"), new ComparableWrapper("a"));
        $this->checkGt(new ComparableWrapper("b"), new ComparableWrapper("a"));
        $this->checkGte(new ComparableWrapper("b"), new ComparableWrapper("a"));
        $this->checkGte(new ComparableWrapper("a"), new ComparableWrapper("a"));
        $this->checkSt(new ComparableWrapper("a"), new ComparableWrapper("b"));
        $this->checkSte(new ComparableWrapper("a"), new ComparableWrapper("b"));
        $this->checkSte(new ComparableWrapper("a"), new ComparableWrapper("a"));
        $this->startSection("Wrapped unsuccessful int comparissions");
        $this->checkNeq(new ComparableWrapper(-1), new ComparableWrapper(-1));
        $this->checkNeq(new ComparableWrapper(0), new ComparableWrapper(0));
        $this->checkNeq(new ComparableWrapper(1), new ComparableWrapper(1));
        $this->checkEq(new ComparableWrapper(-1), new ComparableWrapper(0));
        $this->checkEq(new ComparableWrapper(0), new ComparableWrapper(1));
        $this->checkEq(new ComparableWrapper(-1), new ComparableWrapper(1));
        $this->checkSt(new ComparableWrapper(1), new ComparableWrapper(0));
        $this->checkSte(new ComparableWrapper(1), new ComparableWrapper(0));
        $this->checkGt(new ComparableWrapper(0), new ComparableWrapper(1));
        $this->checkGte(new ComparableWrapper(0), new ComparableWrapper(1));
        $this->startSection("Wrapped unsuccessful string comparissions");
        $this->checkNeq(new ComparableWrapper("a"), new ComparableWrapper("a"));
        $this->checkNeq(new ComparableWrapper("b"), new ComparableWrapper("b"));
        $this->checkNeq(new ComparableWrapper("A"), new ComparableWrapper("A"));
        $this->checkEq(new ComparableWrapper("a"), new ComparableWrapper("b"));
        $this->checkEq(new ComparableWrapper("b"), new ComparableWrapper("A"));
        $this->checkEq(new ComparableWrapper("A"), new ComparableWrapper("a"));
        $this->checkSt(new ComparableWrapper("b"), new ComparableWrapper("a"));
        $this->checkSte(new ComparableWrapper("b"), new ComparableWrapper("a"));
        $this->checkGt(new ComparableWrapper("a"), new ComparableWrapper("b"));
        $this->checkGte(new ComparableWrapper("a"), new ComparableWrapper("b"));
    }
}

class ComparableWrapper implements Comparable {
    private mixed $object;
    public function __construct(mixed $object) {
        $this->object = $object;
    }
    public function __toString(): string {
        return "{$this->object}";
    }
    public function eq(Equatable $other): bool
    {
        if (!$other instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->object == $other->object;
    }

    public function gt(Comparable $other)
    {
        if (!$other instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->object > $other->object;
    }

    public function gte(Comparable $other)
    {
        if (!$other instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->object >= $other->object;
    }

    public function st(Comparable $other)
    {
        if (!$other instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->object < $other->object;
    }

    public function ste(Comparable $other)
    {
        if (!$other instanceof self) {
            throw new ComparableTypeException();
        }
        return $this->object <= $other->object;
    }
}

class ClassWithPrivateStuff {
    private string $fieldWithDefault = "default";
    private string $privateField;
    function __construct(string $privateField) {
        $this->privateField = $privateField;
    }
    static private function staticPrivateFunction(int $arg) {
        return $arg + 42;
    }
    private function privateFunction(int $arg): string {
        return "{$this->privateField}$arg";
    }
}

class TestSuiteTestCase extends TestCase {

    protected function getName(): string
    {
        return "TestSuite";
    }

    public function run()
    {
        $testOutputLogger = new StringTestOutputLogger();
        $runner = (new TestRunner())
                    ->setTestOutputLogger($testOutputLogger)
                    ->addCase(new SectionNormalizationTestCase());
        $this->startSection("Section normalization");
        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
"BEGIN TESTS:
  SectionNormalization Checks:
      foo
    suite_test.php:36[✅] should be reached
    SUMMARY:
      SUCCESS[✅] = 1
      FAILURE[❌] = 0
END TESTS;
");
        $testOutputLogger->clear();
        $runner = (new TestRunner())
            ->setTestOutputLogger($testOutputLogger)
            ->addCase(new SectionDisablingTestCase())
            ->setShowSections(false);
        $this->startSection("Section disabling");
        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
"BEGIN TESTS:
  SectionDisabling Checks:
    suite_test.php:50[✅] should be reached
    SUMMARY:
      SUCCESS[✅] = 1
      FAILURE[❌] = 0
END TESTS;
");
        $testOutputLogger->clear();
        $runner = (new TestRunner())
            ->setTestOutputLogger($testOutputLogger)
            ->setCheckShowPolicy(CheckShowPolicy::ALL)
            ->addCase(new CheckPolicyTestCase());
        $this->startSection("CheckPolicy");
        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
"BEGIN TESTS:
  CheckPolicy Checks:
    suite_test.php:63[✅] should be reached
    suite_test.php:64[❌] should not be reached
    SUMMARY:
      SUCCESS[✅] = 1
      FAILURE[❌] = 1
END TESTS;
");
        $testOutputLogger->clear();
        $runner = (new TestRunner())
            ->setTestOutputLogger($testOutputLogger)
            ->setCheckShowPolicy(CheckShowPolicy::SUCCESS)
            ->addCase(new CheckPolicyTestCase());
        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
"BEGIN TESTS:
  CheckPolicy Checks:
    suite_test.php:63[✅] should be reached
    SUMMARY:
      SUCCESS[✅] = 1
      FAILURE[❌] = 1
END TESTS;
");
        $testOutputLogger->clear();
        $runner = (new TestRunner())
            ->setTestOutputLogger($testOutputLogger)
            ->setCheckShowPolicy(CheckShowPolicy::FAILURE)
            ->addCase(new CheckPolicyTestCase());
        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
"BEGIN TESTS:
  CheckPolicy Checks:
    suite_test.php:64[❌] should not be reached
    SUMMARY:
      SUCCESS[✅] = 1
      FAILURE[❌] = 1
END TESTS;
");
        $testOutputLogger->clear();
        $runner = (new TestRunner())
            ->setTestOutputLogger($testOutputLogger)
            ->setCheckShowPolicy(CheckShowPolicy::NONE)
            ->addCase(new CheckPolicyTestCase());
        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
            "BEGIN TESTS:
  CheckPolicy Checks:
    SUMMARY:
      SUCCESS[✅] = 1
      FAILURE[❌] = 1
END TESTS;
");
        $this->startSection("Comparissions");

        $testOutputLogger->clear();
        $runner = (new TestRunner())
            ->setTestOutputLogger($testOutputLogger)
            ->setCheckShowPolicy(CheckShowPolicy::ALL)
            ->addCase(new ComparissionTestCase());

        $runner->run();
        $this->checkEq($testOutputLogger->getContents(),
            "BEGIN TESTS:
  CheckPolicy Checks:
      Raw successful int comparissions
    suite_test.php:77[✅] -1 === -1
    suite_test.php:78[✅] 0 === 0
    suite_test.php:79[✅] 1 === 1
    suite_test.php:80[✅] -1 !== 0
    suite_test.php:81[✅] 0 !== 1
    suite_test.php:82[✅] -1 !== 1
    suite_test.php:83[✅] 1 > 0
    suite_test.php:84[✅] 1 >= 0
    suite_test.php:85[✅] 0 >= 0
    suite_test.php:86[✅] 0 < 1
    suite_test.php:87[✅] 0 <= 1
    suite_test.php:88[✅] 0 <= 0
      Raw successful string comparissions
    suite_test.php:90[✅] a === a
    suite_test.php:91[✅] b === b
    suite_test.php:92[✅] A === A
    suite_test.php:93[✅] a !== b
    suite_test.php:94[✅] b !== A
    suite_test.php:95[✅] A !== a
    suite_test.php:96[✅] b > a
    suite_test.php:97[✅] b >= a
    suite_test.php:98[✅] a >= a
    suite_test.php:99[✅] a < b
    suite_test.php:100[✅] a <= b
    suite_test.php:101[✅] a <= a
      Raw unsuccessful int comparissions
    suite_test.php:103[❌] -1 !== -1
    suite_test.php:104[❌] 0 !== 0
    suite_test.php:105[❌] 1 !== 1
    suite_test.php:106[❌] -1 === 0
    suite_test.php:107[❌] 0 === 1
    suite_test.php:108[❌] -1 === 1
    suite_test.php:109[❌] 1 < 0
    suite_test.php:110[❌] 1 <= 0
    suite_test.php:111[❌] 0 > 1
    suite_test.php:112[❌] 0 >= 1
      Raw unsuccessful string comparissions
    suite_test.php:114[❌] a !== a
    suite_test.php:115[❌] b !== b
    suite_test.php:116[❌] A !== A
    suite_test.php:117[❌] a === b
    suite_test.php:118[❌] b === A
    suite_test.php:119[❌] A === a
    suite_test.php:120[❌] b < a
    suite_test.php:121[❌] b <= a
    suite_test.php:122[❌] a > b
    suite_test.php:123[❌] a >= b
      Wrapped successful int comparissions
    suite_test.php:125[✅] -1 === -1
    suite_test.php:126[✅] 0 === 0
    suite_test.php:127[✅] 1 === 1
    suite_test.php:128[✅] -1 !== 0
    suite_test.php:129[✅] 0 !== 1
    suite_test.php:130[✅] -1 !== 1
    suite_test.php:131[✅] 1 > 0
    suite_test.php:132[✅] 1 >= 0
    suite_test.php:133[✅] 0 >= 0
    suite_test.php:134[✅] 0 < 1
    suite_test.php:135[✅] 0 <= 1
    suite_test.php:136[✅] 0 <= 0
      Wrapped successful string comparissions
    suite_test.php:138[✅] a === a
    suite_test.php:139[✅] b === b
    suite_test.php:140[✅] A === A
    suite_test.php:141[✅] a !== b
    suite_test.php:142[✅] b !== A
    suite_test.php:143[✅] A !== a
    suite_test.php:144[✅] b > a
    suite_test.php:145[✅] b >= a
    suite_test.php:146[✅] a >= a
    suite_test.php:147[✅] a < b
    suite_test.php:148[✅] a <= b
    suite_test.php:149[✅] a <= a
      Wrapped unsuccessful int comparissions
    suite_test.php:151[❌] -1 !== -1
    suite_test.php:152[❌] 0 !== 0
    suite_test.php:153[❌] 1 !== 1
    suite_test.php:154[❌] -1 === 0
    suite_test.php:155[❌] 0 === 1
    suite_test.php:156[❌] -1 === 1
    suite_test.php:157[❌] 1 < 0
    suite_test.php:158[❌] 1 <= 0
    suite_test.php:159[❌] 0 > 1
    suite_test.php:160[❌] 0 >= 1
      Wrapped unsuccessful string comparissions
    suite_test.php:162[❌] a !== a
    suite_test.php:163[❌] b !== b
    suite_test.php:164[❌] A !== A
    suite_test.php:165[❌] a === b
    suite_test.php:166[❌] b === A
    suite_test.php:167[❌] A === a
    suite_test.php:168[❌] b < a
    suite_test.php:169[❌] b <= a
    suite_test.php:170[❌] a > b
    suite_test.php:171[❌] a >= b
    SUMMARY:
      SUCCESS[✅] = 48
      FAILURE[❌] = 40
END TESTS;
");
        $this->startSection("Reflection");
        $this->checkEq($this->getPropertyDefault(ClassWithPrivateStuff::class, "fieldWithDefault"), "default");
        $this->checkEq($this->runNonPublicStaticMethod(ClassWithPrivateStuff::class, "staticPrivateFunction", 1), 43);
        $this->checkEq($this->runNonPublicMethod(new ClassWithPrivateStuff("foo"), "privateFunction", 42), "foo42");
        $this->checkEq($this->getNonPublicProperty     (new ClassWithPrivateStuff("private"), "privateField"), "private");
    }
}