<?php
require_once "suite.php";
require_once "temporal.php";

(new TestRunner())
    ->addCase(new DuracaoTestCase())
    ->addCase(new TempoTestCase())
    ->addCase(new DataTestCase())
    ->addCase(new DataTempoTestCase())
    ->addCase(new IntervaloDeTempoTestCase())
    ->run();
