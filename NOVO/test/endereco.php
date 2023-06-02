<?php

class EnderecoTestCase extends TestCase {

    protected function getName(): string
    {
        return "Endereco";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        try {
            // TODO
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            // TODO
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        // TODO
        $this->startSection("Stringfication");
        // TODO
        # Equality
        $this->startSection("Equality");
        // TODO
    }
}
