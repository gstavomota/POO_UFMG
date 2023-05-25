<?php
include_once "suite.php";
include_once "../classes/identificadores.php";

class SiglaCompanhiaAereaTestCase extends TestCase {
    protected function getName(): string
    {
        return "SiglaCompanhiaAerea";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        try {
            new SiglaCompanhiaAerea("");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaCompanhiaAerea("AAA");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaCompanhiaAerea("aa");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaCompanhiaAerea("11");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaCompanhiaAerea("AA");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $siglaAA = new SiglaCompanhiaAerea("AA");
        $siglaBB = new SiglaCompanhiaAerea("BB");
        $this->startSection("Stringfication");
        $this->checkEq("{$siglaAA}", "AA");
        $this->checkEq("{$siglaBB}", "BB");
        # Comparission
        $siglaAA_2 = new SiglaCompanhiaAerea("AA");
        $this->startSection("Comparission");
        $this->checkEq($siglaAA, $siglaAA_2);
        $this->checkNeq($siglaAA, $siglaBB);
    }
}
class CodigoVooTestCase extends TestCase {
    protected function getName(): string
    {
        return "CodigoVoo";
    }

    public function run()
    {
        # Constructor
        $sigla = new SiglaCompanhiaAerea("AA");
        $this->startSection("Constructor");
        try {
            new CodigoVoo($sigla, -1);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CodigoVoo($sigla, 10000);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CodigoVoo($sigla, 0);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new CodigoVoo($sigla, 1);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new CodigoVoo($sigla, 9999);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $codigo0 = new CodigoVoo($sigla, 0);
        $codigo9999 = new CodigoVoo($sigla, 9999);
        $this->startSection("Stringfication");
        $this->checkEq("{$codigo0}", "AA0000");
        $this->checkEq("{$codigo9999}", "AA9999");
        # Equality
        $codigo0_2 = new CodigoVoo($sigla, 0);
        $codigo9999_2 = new CodigoVoo($sigla, 9999);
        $this->startSection("Equality");
        $this->checkEq($codigo0, $codigo0_2);
        $this->checkEq($codigo9999, $codigo9999_2);
        $this->checkNeq($codigo0, $codigo9999);
    }
}
class RegistroDeAeronaveTestCase extends TestCase {
    protected function getName(): string
    {
        return "RegistroDeAeronave";
    }

    public function run()
    {
        # Constructor
        $prefixo = PrefixoRegistroDeAeronave::PP;
        $this->startSection("Constructor");
        try {
            new RegistroDeAeronave($prefixo, "");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeAeronave($prefixo, "AAA");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeAeronave($prefixo, "111");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeAeronave($prefixo, "a11");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeAeronave($prefixo, "aaa");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $registroaaa = new RegistroDeAeronave($prefixo, "aaa");
        $registrobbb = new RegistroDeAeronave($prefixo, "bbb");
        $this->startSection("Stringfication");
        $this->checkEq("{$registroaaa}", "PP-aaa");
        $this->checkEq("{$registrobbb}", "PP-bbb");
        # Equality
        $registroaaa_2 = new RegistroDeAeronave($prefixo, "aaa");
        $registrobbb_2 = new RegistroDeAeronave($prefixo, "bbb");
        $this->startSection("Equality");
        $this->checkEq($registroaaa, $registroaaa_2);
        $this->checkEq($registrobbb, $registrobbb_2);
        $this->checkNeq($registroaaa, $registrobbb);
    }
}

class RegistroDePassagemTestCase extends TestCase {

    protected function getName(): string
    {
        return "RegistroDePassagem";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        try {
            new RegistroDePassagem(-1);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDePassagem(0);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new RegistroDePassagem(1);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $registro0 = new RegistroDePassagem(0);
        $registro1 = new RegistroDePassagem(1);
        $this->startSection("Stringfication");
        $this->checkEq("{$registro0}", "0");
        $this->checkEq("{$registro1}", "1");
        # Equality
        $registro0_2 = new RegistroDePassagem(0);
        $registro1_2 = new RegistroDePassagem(1);
        $this->startSection("Equality");
        $this->checkEq($registro0, $registro0_2);
        $this->checkEq($registro1, $registro1_2);
        $this->checkNeq($registro0, $registro1);
    }
}
class RegistroDeViagemTestCase extends TestCase {

    protected function getName(): string
    {
        return "RegistroDeViagem";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        try {
            new RegistroDeViagem("",0);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeViagem("aa",0);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeViagem("11",0);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeViagem("AA",0);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new RegistroDeViagem("AA",-1);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeViagem("AA",10000);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeViagem("AA",9999);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $registroAA0 = new RegistroDeViagem("AA", 0);
        $registroBB9999 = new RegistroDeViagem("BB", 9999);
        $this->startSection("Stringfication");
        $this->checkEq("{$registroAA0}", "AA0000");
        $this->checkEq("{$registroBB9999}", "BB9999");
        # Equality
        $registroAA0_2 = new RegistroDeViagem("AA", 0);
        $registroBB9999_2 = new RegistroDeViagem("BB", 9999);
        $this->startSection("Equality");
        $this->checkEq($registroAA0, $registroAA0_2);
        $this->checkEq($registroBB9999, $registroBB9999_2);
        $this->checkNeq($registroAA0, $registroBB9999);
    }
}
class SiglaAeroportoTestCase extends TestCase {

    protected function getName(): string
    {
        return "SiglaAeroporto";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        try {
            new SiglaAeroporto("");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaAeroporto("aaa");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaAeroporto("111");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new SiglaAeroporto("AAA");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $siglaAAA = new SiglaAeroporto("AAA");
        $siglaBBB = new SiglaAeroporto("BBB");
        $this->startSection("Stringfication");
        $this->checkEq("{$siglaAAA}", "AAA");
        $this->checkEq("{$siglaBBB}", "BBB");
        # Equality
        $this->startSection("Equality");
        $siglaAAA_2 = new SiglaAeroporto("AAA");
        $siglaBBB_2 = new SiglaAeroporto("BBB");
        $this->checkEq($siglaAAA, $siglaAAA_2);
        $this->checkEq($siglaBBB, $siglaBBB_2);
        $this->checkNeq($siglaAAA, $siglaBBB);
    }
}
class RegistroDeTripulanteTestCase extends TestCase {

    protected function getName(): string
    {
        return "RegistroDeTripulante";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        try {
            new RegistroDeTripulante(-1);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RegistroDeTripulante(0);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $registro0 = new RegistroDeTripulante(0);
        $registro1 = new RegistroDeTripulante(1);
        $this->startSection("Stringfication");
        $this->checkEq("{$registro0}", "0");
        $this->checkEq("{$registro1}", "1");
        # Equality
        $this->startSection("Equality");
        $registro0_2 = new RegistroDeTripulante(0);
        $registro1_2 = new RegistroDeTripulante(1);
        $this->checkEq($registro0, $registro0_2);
        $this->checkEq($registro1, $registro1_2);
        $this->checkNeq($registro0, $registro1);
    }
}
class GeradorDeRegistroDeTripulanteTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeRegistroDeTripulante";
    }

    public function run()
    {
        # Gerador
        $gerador = new GeradorDeRegistroDeTripulante();
        $registro0 = $gerador->gerar();
        $registro1 = $gerador->gerar();
        $registro0_2 = new RegistroDeTripulante(0);
        $registro1_2 = new RegistroDeTripulante(1);
        $this->startSection("Gerador");
        $this->checkEq($registro0, $registro0_2);
        $this->checkEq($registro1, $registro1_2);
    }
}
class RGTestCase extends TestCase {

    protected function getName(): string
    {
        return "RG";
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
class PassaporteTestCase extends TestCase {

    protected function getName(): string
    {
        return "Passaporte";
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
class DocumentoPassageiroTestCase extends TestCase {

    protected function getName(): string
    {
        return "DocumentoPassageiro";
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

class GeradorDeRegistroDeViagemTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeRegistroDeViagem";
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
class GeradorDeRegistroDePassagemTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeRegistroDePassagem";
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
class ClasseTestCase extends TestCase {

    protected function getName(): string
    {
        return "Classe";
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
class CodigoDoAssentoTestCase extends TestCase {

    protected function getName(): string
    {
        return "CodigoDoAssento";
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
class GeradorDeCodigoDoAssentoTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeCodigoDoAssento";
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
class EmailTestCase extends TestCase {

    protected function getName(): string
    {
        return "Email";
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
class CPFTestCase extends TestCase {

    protected function getName(): string
    {
        return "CPF";
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
class CEPTestCase extends TestCase {

    protected function getName(): string
    {
        return "CEP";
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
