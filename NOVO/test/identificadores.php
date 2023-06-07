<?php
include_once "suite.php";
include_once "../classes/identificadores.php";

class GeradorDeRegistroInteiro extends GeradorDeRegistroNumerico {
    public function __construct(int $ultimo_id = null)
    {
        parent::__construct($ultimo_id);
    }
    public function gerar(): int {
        return $this->gerarNumero();
    }
}

class GeradorDeRegistroNumericoTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeRegistroNumerico";
    }

    public function run()
    {
        $this->startSection("Constructor");
        $geradorComInicioZero = new GeradorDeRegistroInteiro();
        $geradorComInicioUm = new GeradorDeRegistroInteiro(0);
        $this->checkEq($geradorComInicioZero->getUltimoId(), -1);
        $this->checkEq($geradorComInicioUm->getUltimoId(), 0);
        $this->startSection("Geracao");
        $this->checkEq($geradorComInicioZero->gerar(), 0);
        $this->checkEq($geradorComInicioZero->gerar(), 1);
        $this->checkEq($geradorComInicioZero->gerar(), 2);
        $this->checkEq($geradorComInicioUm->gerar(), 1);
        $this->checkEq($geradorComInicioUm->gerar(), 2);
        $this->checkEq($geradorComInicioUm->gerar(), 3);
        $this->startSection("Ultimo id");
        $this->checkEq($geradorComInicioZero->getUltimoId(), 2);
        $this->checkEq($geradorComInicioUm->getUltimoId(), 3);
    }
}

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
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($siglaAA->hashCode(), $siglaBB->hashCode());
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
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($codigo0->hashCode(), $codigo9999->hashCode());
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
            new RegistroDeAeronave($prefixo, "aaa");
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
            new RegistroDeAeronave($prefixo, "AAA");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $registroaaa = new RegistroDeAeronave($prefixo, "AAA");
        $registrobbb = new RegistroDeAeronave($prefixo, "BBB");
        $this->startSection("Stringfication");
        $this->checkEq("{$registroaaa}", "PP-AAA");
        $this->checkEq("{$registrobbb}", "PP-BBB");
        # Equality
        $registroaaa_2 = new RegistroDeAeronave($prefixo, "AAA");
        $registrobbb_2 = new RegistroDeAeronave($prefixo, "BBB");
        $this->startSection("Equality");
        $this->checkEq($registroaaa, $registroaaa_2);
        $this->checkEq($registrobbb, $registrobbb_2);
        $this->checkNeq($registroaaa, $registrobbb);
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($registrobbb->hashCode(), $registroaaa->hashCode());
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
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($registro0->hashCode(), $registro1->hashCode());
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
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($registroAA0->hashCode(), $registroBB9999->hashCode());
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
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($registro0->hashCode(), $registro1->hashCode());
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
        $invalidState = "AA";
        $validState = "MG";
        $validState_2 = "SP";
        $validDigits = "11.111.111";
        $validDigits_no_sep = "11111111";
        try {
            new RG($invalidState.$validDigits);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RG($invalidState.$validDigits_no_sep);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RG($validState."aaaaaaaa");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RG($validState."111111111");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new RG($validState.$validDigits);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new RG($validState_2.$validDigits_no_sep);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $rgValido = new RG($validState.$validDigits);
        $rgValidoSp = new RG($validState_2.$validDigits);
        $this->startSection("Stringfication");
        $this->checkEq("{$rgValido}", "MG/11.111.111");
        $this->checkEq("{$rgValidoSp}", "SP/11.111.111");
        # Equality
        $rgValido_2 = new RG($validState.$validDigits_no_sep);
        $rgValidoSp_2 = new RG($validState_2.$validDigits);
        $this->startSection("Equality");
        $this->checkEq($rgValido, $rgValido_2);
        $this->checkEq($rgValidoSp, $rgValidoSp_2);
        $this->checkNeq($rgValido, $rgValidoSp);
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($rgValido->hashCode(), $rgValidoSp->hashCode());
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
            new Passaporte("---------");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Passaporte("aaaaaaaaaa");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new Passaporte("Aaaaaaaaa");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new Passaporte("A99999999");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $passaporte0 = new Passaporte("A00000000");
        $passaporte1 = new Passaporte("A00000001");
        $this->startSection("Stringfication");
        $this->checkEq("{$passaporte0}", "A00000000");
        $this->checkEq("{$passaporte1}", "A00000001");
        # Equality
        $passaporte0_2 = new Passaporte("A00000000");
        $passaporte1_2 = new Passaporte("A00000001");
        $this->startSection("Equality");
        $this->checkEq($passaporte0, $passaporte0_2);
        $this->checkEq($passaporte1, $passaporte1_2);
        $this->checkNeq($passaporte0, $passaporte1);
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($passaporte0->hashCode(), $passaporte1->hashCode());
    }
}
class DocumentoPessoaTestCase extends TestCase {

    protected function getName(): string
    {
        return "DocumentoPessoa";
    }

    public function run()
    {
        # Constructor
        $this->startSection("Constructor");
        $passaporte = new Passaporte("A11111111");
        $rg = new RG("MG11.111.111");
        try {
            new DocumentoPessoa($passaporte, $rg);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new DocumentoPessoa();
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new DocumentoPessoa($passaporte);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new DocumentoPessoa(null, $rg);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $documentoPassaporte = new DocumentoPessoa($passaporte);
        $documentoRg = new DocumentoPessoa(null, $rg);
        $this->startSection("Stringfication");
        $this->checkEq("{$documentoPassaporte}", "{$passaporte}");
        $this->checkEq("{$documentoRg}", "{$rg}");
        # Equality
        $this->startSection("Equality");
        $documentoPassaporte_2 = new DocumentoPessoa($passaporte);
        $documentoRg_2 = new DocumentoPessoa(null, $rg);
        $this->checkEq($documentoPassaporte, $documentoPassaporte_2);
        $this->checkEq($documentoRg, $documentoRg_2);
        $this->checkNeq($documentoRg, $documentoPassaporte);
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($documentoRg->hashCode(), $documentoPassaporte->hashCode());
    }
}

class GeradorDeRegistroDeViagemTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeRegistroDeViagem";
    }

    public function run()
    {
        $gerador = new GeradorDeRegistroDeViagem();
        $codigo0 =  $gerador->gerar();
        $codigo1 =  $gerador->gerar();
        $this->startSection("Gerador");
        $this->checkEq($codigo0, new RegistroDeViagem("AA", 0));
        $this->checkEq($codigo1, new RegistroDeViagem("AA", 1));
        for ($i = 0; $i < 10000 - 2; $i++) {
            $gerador->gerar();
        }
        $codigoAB0 = $gerador->gerar();
        $this->checkEq($codigoAB0, new RegistroDeViagem("AB", 0));
        for ($i = 0; $i < 10000 * 25 - 1; $i++) {
            $gerador->gerar();
        }
        $codigoBA0 = $gerador->gerar();
        $this->checkEq($codigoBA0, new RegistroDeViagem("BA", 0));
    }
}
class GeradorDeRegistroDePassagemTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeRegistroDePassagem";
    }

    public function run()
    {
        # Gerador
        $gerador = new GeradorDeRegistroDePassagem();
        $registro0 = $gerador->gerar();
        $registro1 = $gerador->gerar();
        $registro0_2 = new RegistroDePassagem(0);
        $registro1_2 = new RegistroDePassagem(1);
        $this->startSection("Gerador");
        $this->checkEq($registro0, $registro0_2);
        $this->checkEq($registro1, $registro1_2);
    }
}
class ClasseTestCase extends TestCase {

    protected function getName(): string
    {
        return "Classe";
    }

    public function run()
    {
        # Prefixo
        $this->startSection("Prefixo");
        $this->checkEq(Classe::prefixo(Classe::EXECUTIVA), "E");
        $this->checkEq(Classe::prefixo(Classe::STANDARD), "S");
        try {
            Classe::prefixo("outra");
            $this->checkNotReached();
        } catch (TypeError $e) {
            $this->checkReached();
        }

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
            new CodigoDoAssento(Classe::STANDARD, "A", 0);
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CodigoDoAssento(Classe::STANDARD, "A", 1);
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $codigo1 = new CodigoDoAssento(Classe::STANDARD, "A", 1);
        $codigo2 = new CodigoDoAssento(Classe::STANDARD, "B", 1);
        $this->startSection("Stringfication");
        $this->checkEq("{$codigo1}", "SA01");
        $this->checkEq("{$codigo2}", "SB01");
        # Equality
        $codigo1_2 = new CodigoDoAssento(Classe::STANDARD, "A", 1);
        $codigo2_2 = new CodigoDoAssento(Classe::STANDARD, "B", 1);
        $this->startSection("Equality");
        $this->checkEq($codigo1, $codigo1_2);
        $this->checkEq($codigo2, $codigo2_2);
        $this->checkNeq($codigo1, $codigo2);
        # Hash
        $this->startSection("Hash");
        $this->checkNeq($codigo1->hashCode(), $codigo2->hashCode());
    }
}
class GeradorDeCodigoDoAssentoTestCase extends TestCase {

    protected function getName(): string
    {
        return "GeradorDeCodigoDoAssento";
    }

    public function run()
    {
        # Gerador
        $gerador = new GeradorDeCodigoDoAssento(8);
        $codigos = [
            new CodigoDoAssento(Classe::STANDARD, "A", 1),
            new CodigoDoAssento(Classe::STANDARD, "B", 1),
            new CodigoDoAssento(Classe::STANDARD, "C", 1),
            new CodigoDoAssento(Classe::STANDARD, "D", 1),
            new CodigoDoAssento(Classe::STANDARD, "E", 1),
            new CodigoDoAssento(Classe::STANDARD, "F", 1),
            new CodigoDoAssento(Classe::STANDARD, "G", 1),
            new CodigoDoAssento(Classe::STANDARD, "H", 1),
        ];
        $this->startSection("Gerador");
        $this->checkEq($gerador->gerar(), $codigos[0]);
        $this->checkEq($gerador->gerar(), $codigos[1]);
        $this->checkEq($gerador->gerar(), $codigos[2]);
        $this->checkEq($gerador->gerar(), $codigos[3]);
        $this->checkEq($gerador->gerar(), $codigos[4]);
        $this->checkEq($gerador->gerar(), $codigos[5]);
        $this->checkEq($gerador->gerar(), $codigos[6]);
        $this->checkEq($gerador->gerar(), $codigos[7]);
        # Gerar todos
        $this->startSection("Gerar Todos");
        $gerador = new GeradorDeCodigoDoAssento(8);
        $this->checkEq($gerador->gerar_todos(), $codigos);
    }
}
class EmailTestCase extends TestCase {

    protected function getName(): string
    {
        return "Email";
    }

    public function run()
    {
        $invalidEmails = [
            # One @
            "user@example",
            "user@.com",
            "@example.com",
            "user@example..com",
            "user@-example.com",
            "user@example_com",
            "user#example.com",
            "user@example..com",
            "user@example_com",
            "user@example#com",
            "user@example..com",
            "user@[example].com",
            "user@example_com",
            "user@example_",
            "user@example,com",
            "user@example..com",
            "user@example.com-",
            "user@example_com",
            "user@example!",
            # Two @
            "user@@example.com",
            "user@example@@com",
            "@example.com@",
            "user@example@com",
            "user@@example@@com",
            "user@example@.com",
            "user@example.com@",
            "user@example@com@",
            "user@example@.com@",
            "user@@example.com@",
        ];
        $validEmails = [
            "user@example.com",
            "john.doe@example.com",
            "jane.smith123@example.com",
            "info@company.com",
            "first.last@example.com",
            "sales@company.co.uk",
            "john+smith@example.com",
            "jane.doe1234@example.com",
            "support@website.com",
            "johndoe1980@example.com",
            "admin@domain.com",
            "mary-ann@example.com",
            "jsmith@example.net",
            "test.email@example.com",
            "user123@example.com",
            "john.doe@subdomain.example.com",
            "info1234@example.com",
            "marketing@example.org",
            "jdoe@example.us",
            "contact@business.io",
        ];
        # Constructor
        $this->startSection("Constructor");
        foreach ($invalidEmails as $invalidEmail) {
            try {
                new Email($invalidEmail);
                $this->checkNotReached();
            } catch (InvalidArgumentException $e) {
                $this->checkReached();
            }
        }
        foreach ($validEmails as $validEmail) {
            try {
                new Email($validEmail);
                $this->checkReached();
            } catch (InvalidArgumentException $e) {
                $this->checkNotReached();
            }
        }
        $validEmailStr1 = $validEmails[0];
        $validEmailStr2 = $validEmails[1];
        $email1 = new Email($validEmailStr1);
        $email2 = new Email($validEmailStr2);
        $this->startSection("Stringfication");
        $this->checkEq("{$email1}", $validEmailStr1);
        $this->checkEq("{$email2}", $validEmailStr2);
        # Equality
        $email1_2 = new Email($validEmailStr1);
        $email2_2 = new Email($validEmailStr2);
        $this->startSection("Equality");
        $this->checkEq($email1, $email1_2);
        $this->checkEq($email2, $email2_2);
        $this->checkNeq($email1, $email2);
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
            new CPF("");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CPF("aaaaaaaaaaa");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CPF("11111111110");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CPF("11111111111");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $cpf1 = new CPF("11111111111");
        $cpf2 = new CPF("22222222222");
        $this->startSection("Stringfication");
        $this->checkEq("{$cpf1}", "111.111.111-11");
        $this->checkEq("{$cpf2}", "222.222.222-22");
        # Equality
        $this->startSection("Equality");
        $cpf1_2 = new CPF("111.111.111-11");
        $cpf2_2 = new CPF("222.222.222-22");
        $this->checkEq($cpf1, $cpf1_2);
        $this->checkEq($cpf2, $cpf2_2);
        $this->checkNeq($cpf1, $cpf2);
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
            new CEP("");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CEP("--------");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CEP("aaaaaaaa");
            $this->checkNotReached();
        } catch (InvalidArgumentException $e) {
            $this->checkReached();
        }
        try {
            new CEP("11111111");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        try {
            new CEP("11111-111");
            $this->checkReached();
        } catch (InvalidArgumentException $e) {
            $this->checkNotReached();
        }
        # Stringfication
        $cep1 = new CEP("11111-111");
        $cep2 = new CEP("22222-222");
        $this->startSection("Stringfication");
        $this->checkEq("{$cep1}", "11111-111");
        $this->checkEq("{$cep2}", "22222-222");
        # Equality
        $cep1_2 = new CEP("11111-111");
        $cep2_2 = new CEP("22222-222");
        $this->startSection("Equality");
        $this->checkEq($cep1, $cep1_2);
        $this->checkEq($cep2, $cep2_2);
        $this->checkNeq($cep1, $cep2);
    }
}