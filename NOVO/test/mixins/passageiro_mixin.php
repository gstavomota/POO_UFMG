<?php
require_once "../../classes/passageiro.php";
require_once "../../classes/passageiroVip.php";
require_once "../../classes/companhia_aerea.php";

trait PassageiroMixin {
    private int $passaporteCount = 0;

    protected function passageiro(): Passageiro {
        $nomes = [
            "John",
            "Emma",
            "Liam",
            "Olivia",
            "Noah",
            "Ava",
            "William",
            "Sophia",
            "James",
            "Isabella",
            "Oliver",
            "Mia",
            "Benjamin",
            "Charlotte",
            "Elijah",
            "Amelia",
            "Lucas",
            "Harper",
            "Henry",
            "Evelyn"
        ];

        $sobrenomes = [
            "Smith",
            "Johnson",
            "Brown",
            "Taylor",
            "Miller",
            "Anderson",
            "Wilson",
            "Davis",
            "Martinez",
            "Anderson",
            "Thomas",
            "Rodriguez",
            "Jackson",
            "Thompson",
            "White",
            "Harris",
            "Martin",
            "Garcia",
            "Robinson",
            "Clark"
        ];

        $randomNomeIndex = array_rand($nomes);
        $randomSobrenomeIndex = array_rand($sobrenomes);

        $nome = $nomes[$randomNomeIndex];
        $sobrenome = $sobrenomes[$randomSobrenomeIndex];

        $passaporteNumero = "A" . str_pad(++$this->passaporteCount, 8, "0", STR_PAD_LEFT);

        $email = strtolower($nome) . "." . strtolower($sobrenome) . "@gmail.com";

        return new Passageiro(
            $nome,
            $sobrenome,
            new DocumentoPessoa(new Passaporte($passaporteNumero)),
            Nacionalidade::BRASIL,
            new CPF("111.111.111-11"),
            new Data(2003, 6, 3),
            new Email($email)
        );
    }
    private int $numeroDeRegistroProgramaDeMilhagem;
    protected function passageiroVip(ProgramaDeMilhagem $programaDeMilhagem): PassageiroVip {
        $passageiro = $this->passageiro();
        $numeroDeRegistro = "$this->numeroDeRegistroProgramaDeMilhagem";
        $this->numeroDeRegistroProgramaDeMilhagem++;
        return new PassageiroVip(
            $passageiro->getNome(),
            $passageiro->getSobrenome(),
            $passageiro->getDocumento(),
            $passageiro->getNacionalidade(),
            $passageiro->getCpf(),
            $passageiro->getDataDeNascimento(),
            $passageiro->getEmail(),
            $numeroDeRegistro,
            $programaDeMilhagem
        );
    }

    /**
     * @throws Exception
     */
    protected function adicionarPassageiroNaCompanhiaAerea(CompanhiaAerea & $companhiaAerea, Passageiro $passageiro): void {
        $companhiaAerea->adicionarPassageiro($passageiro);
    }

}