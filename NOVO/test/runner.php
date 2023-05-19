<?php
require_once "suite.php";
require_once "temporal.php";

(new TestRunner())
    ->addCase(new DuracaoTestCase())
    ->run();
