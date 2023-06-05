<?php
include_once "suite.php";
include_once "../classes/enum_to_array.php";
enum Enum: string {
    use EnumToArray;
    case A = "1";
    case B = "2";
}

class EnumToArrayTestCase extends TestCase {
    protected function getName(): string
    {
        return "EnumToArray";
    }

    public function run()
    {
        # Array
        $array = ["A" => '1', "B" => "2"];
        $this->checkEq(Enum::array(), $array);
        # Names
        $keys = array_keys($array);
        $this->checkEq(Enum::names(), $keys);
        # Values
        $values = array_values($array);
        $this->checkEq(Enum::values(), $values);
    }
}