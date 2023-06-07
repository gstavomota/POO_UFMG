<?php

include_once("estado.php");
include_once("enum_to_array.php");
include_once("Equatable.php");
include_once "HashableAndEquatable.php";

abstract class GeradorDeRegistroNumerico {

    private int $ultimo_id;

    public function __construct(int $ultimo_id = null)
    {
        $this->ultimo_id = $ultimo_id ?? -1;
    }

    protected function gerarNumero(): int
    {
        $this->ultimo_id += 1;
        $id = $this->ultimo_id;
        return $id;
    }

    public function getUltimoId(): int {
        return $this->ultimo_id;
    }
}

class SiglaCompanhiaAerea implements HashableAndEquatable
{
    public string $sigla;

    public function __construct(string $sigla)
    {
        $this->sigla = SiglaCompanhiaAerea::valida_sigla($sigla);
    }

    public function __toString(): string
    {
        return $this->sigla;
    }

    private static function valida_sigla(string $v): string
    {
        if (strlen($v) !== 2) {
            throw new InvalidArgumentException("Sigla invalida");
        }
        if (!ctype_alpha($v)) {
            throw new InvalidArgumentException("Sigla invalida");
        }
        if (!ctype_upper($v)) {
            throw new InvalidArgumentException("Sigla invalida");
        }
        return $v;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->sigla == $outro->sigla;
    }

    public function hashCode(): int
    {
        return hashObject($this->sigla);
    }
}

class CodigoVoo implements HashableAndEquatable
{
    private SiglaCompanhiaAerea $sigla_da_companhia;
    private int $numero;

    public function __construct(
        SiglaCompanhiaAerea $sigla_da_companhia,
        int                 $numero
    )
    {
        $this->sigla_da_companhia = $sigla_da_companhia;
        $this->numero = CodigoVoo::valida_numero($numero);
    }

    public function __toString(): string
    {
        return "{$this->sigla_da_companhia}" .
            sprintf('%04d', $this->numero);
    }

    private static function valida_numero(int $v): int
    {
        if ($v < 0) {
            throw new InvalidArgumentException("O numero é negativo");
        }
        if ($v > 9999) {
            throw new InvalidArgumentException("O numero é muito grande");
        }
        return $v;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->sigla_da_companhia->eq($outro->sigla_da_companhia) &&
            $this->numero == $outro->numero;
    }

    public function hashCode(): int
    {
        return combineHash([$this->sigla_da_companhia, $this->numero]);
    }

    /**
     * @return SiglaCompanhiaAerea
     */
    public function getSiglaDaCompanhia(): SiglaCompanhiaAerea
    {
        return $this->sigla_da_companhia;
    }

    /**
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }
}

enum PrefixoRegistroDeAeronave: string
{
    use EnumToArray;

    case PT = "PT";
    case PR = "PR";
    case PP = "PP";
    case PS = "PS";
}


class RegistroDeAeronave implements HashableAndEquatable
{
    public PrefixoRegistroDeAeronave $prefixo;
    public string $sufixo;

    public function __construct(
        PrefixoRegistroDeAeronave $prefixo,
        string                    $sufixo
    )
    {
        $this->prefixo = $prefixo;
        $this->sufixo = RegistroDeAeronave::valida_sufixo($sufixo);
    }

    public function __toString(): string
    {
        return "{$this->prefixo->value}-{$this->sufixo}";
    }

    private static function valida_sufixo(string $v): string
    {
        if (strlen($v) !== 3) {
            throw new InvalidArgumentException("O sufixo deve ter 3 letras");
        }
        if (!ctype_upper($v)) {
            throw new InvalidArgumentException("O sufixo deve ser uppercase");
        }
        if (!ctype_alpha($v)) {
            throw new InvalidArgumentException("O sufixo deve ser composto somente por letras");
        }
        return $v;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->prefixo == $outro->prefixo &&
            $this->sufixo == $outro->sufixo;
    }

    public function hashCode(): int
    {
        return combineHash([$this->prefixo->value, $this->sufixo]);
    }
}

class RegistroDePassagem implements HashableAndEquatable
{
    public int $number;

    public function __construct(int $number)
    {
        $this->number = RegistroDePassagem::valida_numero($number);
    }

    private static function valida_numero(int $numero): int
    {
        if ($numero < 0) {
            throw new InvalidArgumentException("Numero deve ser não negativo");
        }
        return $numero;
    }

    public function __toString(): string
    {
        return "{$this->number}";
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->number == $outro->number;
    }

    public function hashCode(): int
    {
        return hashObject($this->number);
    }
}

class RegistroDeViagem implements HashableAndEquatable
{
    public string $prefixo;
    public int $numero;

    public function __construct(string $prefixo, int $numero)
    {
        $this->prefixo = RegistroDeViagem::valida_prefixo($prefixo);
        $this->numero = RegistroDeViagem::valida_numero($numero);
    }

    public function __toString(): string
    {
        return "{$this->prefixo}".sprintf('%04d', $this->numero);
    }

    private static function valida_prefixo(string $v): string
    {
        if (strlen($v) != 2) {
            throw new InvalidArgumentException("O prefixo deve ter dois caracteres");
        }
        if (!ctype_upper($v)) {
            throw new InvalidArgumentException("O prefixo deve ser uppercase");
        }
        if (!ctype_alpha($v)) {
            throw new InvalidArgumentException("O prefixo deve ser feito de letras");
        }
        return $v;
    }

    private static function valida_numero(int $v): int
    {
        if ($v < 0) {
            throw new InvalidArgumentException("O numero é negativo");
        }
        if ($v > 9999) {
            throw new InvalidArgumentException("O numero é muito grande");
        }
        return $v;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->prefixo == $outro->prefixo &&
            $this->numero == $outro->numero;
    }

    public function hashCode(): int
    {
        return combineHash([$this->prefixo, $this->numero]);
    }
}

class RegistroDeVeiculo implements Equatable {
    public int $number;

    public function __construct(int $number){
        $this->number = $number;
    }

    public function __toString(): string {
        return "{$this->number}";
    }

    public function eq(Equatable $outro): bool {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }

        return $this->number == $outro->number;
    }
}

class GeradorDeRegistroDeVeiculo extends GeradorDeRegistroNumerico{
    public function __construct(int $ultimo_id = null)
    {
        parent::__construct($ultimo_id);
    }

    public function gerar(): RegistroDeVeiculo
    {
        $id = $this->gerarNumero();
        return new RegistroDeVeiculo($id);
    }
}

class RegistroDeCartaoDeEmbarque implements HashableAndEquatable {
    private SiglaCompanhiaAerea $sigla;
    public int $number;

    public function __construct(SiglaCompanhiaAerea $sigla, int $number) {
        $this->sigla = $sigla;
        $this->number = $number;
    }

    public function __toString(): string {
        return "{$this->sigla}{$this->number}";
    }

    public function eq(Equatable $outro): bool {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }

        return $this->number == $outro->number && $this->sigla->eq($outro->sigla);
    }

    public function hashCode(): int
    {
        return combineHash([$this->number, $this->sigla]);
    }
}

class GeradorDeRegistroDeCartaoDeEmbarque {
    private SiglaCompanhiaAerea $sigla;
    private int $ultimo_id;

    public function __construct(SiglaCompanhiaAerea $sigla, int $ultimo_id = null) {
        $this->sigla = $sigla;
        $this->ultimo_id = $ultimo_id ?? -1;
    }

    public function gerar(): RegistroDeCartaoDeEmbarque {
        $this->ultimo_id += 1;
        $id = $this->ultimo_id;
        return new RegistroDeCartaoDeEmbarque($this->sigla, $id);
    }
}

class SiglaAeroporto implements Equatable
{
    public string $sigla;

    public function __construct(string $sigla)
    {
        $this->sigla = SiglaAeroporto::valida_sigla($sigla);
    }

    public function __toString()
    {
        return $this->sigla;
    }

    private static function valida_sigla($v)
    {
        if (strlen($v) != 3) {
            throw new InvalidArgumentException("A sigla deve ter 3 caracteres");
        }
        if (!ctype_upper($v)) {
            throw new InvalidArgumentException("A sigla deve ser uppercase");
        }
        if (!ctype_alpha($v)) {
            throw new InvalidArgumentException("A sigla deve ser feita de caracteres");
        }
        return $v;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->sigla == $outro->sigla;
    }
}

class RegistroDeTripulante implements HashableAndEquatable
{
    private int $idTripulante;


    /** Valida um id
     * @param int $idTripulante
     * @throws InvalidArgumentException if $idTripulante is negative
     */
    public function __construct(int $idTripulante)
    {
        $this->idTripulante = RegistroDeTripulante::validaIdTripulante($idTripulante);
    }

    /** Valida um id
     * @param int $idTripulante
     * @return int
     * @throws InvalidArgumentException if $idTripulante is negative
     */
    private static function validaIdTripulante(int $idTripulante): int
    {
        if ($idTripulante < 0) {
            throw new InvalidArgumentException("Id de tripulante invalido");
        }
        return $idTripulante;
    }

    public function __toString(): string
    {
        return "{$this->idTripulante}";
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->idTripulante == $outro->idTripulante;
    }

    public function hashCode(): int
    {
        return hashObject($this->idTripulante);
    }
}

class GeradorDeRegistroDeTripulante extends GeradorDeRegistroNumerico
{
    public function __construct(int $ultimo_id = null)
    {
        parent::__construct($ultimo_id);
    }

    public function gerar(): RegistroDeTripulante
    {
        $id = $this->gerarNumero();
        return new RegistroDeTripulante($id);
    }
}

class RG implements HashableAndEquatable
{
    public string $rg;

    public function __construct(string $rg)
    {
        $this->rg = RG::valida_rg($rg);
    }

    private static function valida_rg(string $rg): string
    {
        $rg = str_replace('.', '', $rg);
        $numeros = substr($rg, 2);
        $estado = substr($rg, 0, 2);

        if (!in_array($estado, Estado::names())) {
            throw new InvalidArgumentException("Estado inválido");
        }

        if (!ctype_digit($numeros)) {
            throw new InvalidArgumentException("Resto do RG não é numérico");
        }


        // Check if the RG number has a valid length
        if (strlen($numeros) !== 8) {
            throw new InvalidArgumentException("Resto do RG não tem 8 digitos");
        }

        return $rg;
    }

    public function __toString()
    {
        $rg = $this->rg;
        $state = substr($rg, 0, 2);
        return $state . '/' . substr($rg, 2, 2) . '.' . substr($rg, 4, 3) . '.' . substr($rg, 7);
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->rg == $outro->rg;
    }

    public function hashCode(): int
    {
        return hashObject($this->rg);
    }
}

class Passaporte implements HashableAndEquatable
{
    public string $passaporte;

    public function __construct(string $passaporte)
    {
        $this->passaporte = Passaporte::valida_passaporte($passaporte);
    }

    public function __toString()
    {
        return $this->passaporte;
    }

    private static function valida_passaporte(string $passaporte)
    {
        if (!ctype_alnum($passaporte)) {
            throw new InvalidArgumentException('Um passaporte deve ser alfanumerico');
        }
        if (strlen($passaporte) !== 9) {
            throw new InvalidArgumentException('Um passaporte deve ter 9 caracteres');
        }
        if (strpos($passaporte, 'A') !== 0) {
            throw new InvalidArgumentException('Um passaporte deve começar com um A');
        }
        return $passaporte;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->passaporte == $outro->passaporte;
    }

    public function hashCode(): int
    {
        return hashObject($this->passaporte);
    }
}

class DocumentoPessoa implements HashableAndEquatable
{
    private ?Passaporte $passaporte;
    private ?RG $rg;

    public function __construct(?Passaporte $passaporte = null, ?RG $rg = null)
    {
        if (!$passaporte && !$rg) {
            throw new InvalidArgumentException("Ou um passaporte ou um rg devem ser especificados");
        }
        if ($passaporte && $rg) {
            throw new InvalidArgumentException("somente rg ou passaporte devem ser especificados");
        }
        $this->passaporte = $passaporte;
        $this->rg = $rg;
    }

    public function documento(): Passaporte|RG
    {
        return $this->passaporte ?? $this->rg;
    }

    public function __toString(): string
    {
        return strval($this->documento());
    }


    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        if ($this->passaporte === null && $outro->passaporte === null) {
            return $this->rg->eq($outro->rg);
        }
        if ($this->rg === null && $outro->rg === null) {
            return $this->passaporte->eq($outro->passaporte);
        }
        return false;
    }

    public function hashCode(): int
    {
        return combineHash([$this->passaporte, $this->rg]);
    }
}

class GeradorDeRegistroDeViagem extends GeradorDeRegistroNumerico
{
    public function __construct(int $ultimo_id = null)
    {
        parent::__construct($ultimo_id);
    }

    public function gerar(): RegistroDeViagem
    {
        $id = $this->gerarNumero();

        $prefixIndex = floor($id / 10000);
        $numberPart = $id % 10000;

        $prefix = chr(($prefixIndex / 26) + 65) . chr(($prefixIndex % 26) + 65);

        return new RegistroDeViagem($prefix, $numberPart);
    }
}


class GeradorDeRegistroDePassagem extends GeradorDeRegistroNumerico
{
    public function __construct(int $ultimo_id = null)
    {
        parent::__construct($ultimo_id);
    }

    public function gerar(): RegistroDePassagem
    {
        return new RegistroDePassagem($this->gerarNumero());
    }
}

enum Classe: string
{
    case EXECUTIVA = "executiva";
    case STANDARD = "standard";

    public static function prefixo(Classe $classe): string
    {
        return match ($classe) {
            Classe::EXECUTIVA => 'E',
            Classe::STANDARD => 'S',
        };

    }
}

class CodigoDoAssento implements HashableAndEquatable
{
    private Classe $classe;
    private string $coluna;
    private int $fileira;

    /**
     * @param Classe $classe
     * @param string $coluna
     * @param int $fileira
     * @throws InvalidArgumentException se fileira for menor que 1
     */
    public function __construct(
        Classe $classe,
        string $coluna,
        int    $fileira
    )
    {
        $this->classe = $classe;
        $this->coluna = $coluna;
        $this->fileira = CodigoDoAssento::validaFileira($fileira);
    }

    /** Valida uma fileira
     * @param int $fileira
     * @return int
     * @throws InvalidArgumentException se fileira for menor que 1
     */
    private static function validaFileira(int $fileira): int {
        if ($fileira < 1) {
            throw new InvalidArgumentException("A fileira começa com 1");
        }
        return $fileira;
    }

    /**
     * @return Classe
     */
    public function getClasse(): Classe
    {
        return $this->classe;
    }

    /**
     * @return string
     */
    public function getColuna(): string
    {
        return $this->coluna;
    }

    /**
     * @return int
     */
    public function getFileira(): int
    {
        return $this->fileira;
    }

    public function __toString(): string
    {
        return Classe::prefixo($this->classe) . $this->coluna . str_pad($this->fileira, 2, "0", STR_PAD_LEFT);
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->classe == $outro->classe &&
            $this->coluna == $outro->coluna &&
            $this->fileira == $outro->fileira;
    }

    public function hashCode(): int
    {
        return combineHash([
            $this->classe->value,
            $this->coluna,
            $this->fileira
        ]);
    }
}

class GeradorDeCodigoDoAssento extends GeradorDeRegistroNumerico
{
    private int $passenger_count;
    private int $executive_count;
    private int $standard_count;
    private int $seats_per_row;

    public function __construct(int $passenger_count, float $executive_ratio = 0.0, int $ultimo_id = 0)
    {
        $this->passenger_count = $passenger_count;
        $this->executive_count = (int)($passenger_count * $executive_ratio);
        $this->standard_count = $passenger_count - $this->executive_count;
        $this->seats_per_row = $this->_calculate_seats_per_row();
        parent::__construct($ultimo_id);
    }

    /** Retorna o numero de assentos por fileira
     * @return int
     */
    private function _calculate_seats_per_row(): int
    {
        $standard_seats_per_row = 10;
        $executive_seats_per_row = 4;
        while (true) {
            $standard_rows = ceil($this->standard_count / $standard_seats_per_row);
            $executive_rows = ceil($this->executive_count / $executive_seats_per_row);
            $total_rows = $standard_rows + $executive_rows;
            if ($total_rows <= 80) {
                return $standard_seats_per_row;
            } else {
                $standard_seats_per_row -= 1;
                $executive_seats_per_row -= 1;
            }
        }
    }

    /** Gera um codigo do assento desse gerador
     * @return CodigoDoAssento
     */
    public function gerar(): CodigoDoAssento
    {
        $current_index = $this->gerarNumero();
        if ($current_index <= $this->executive_count) {
            $row = (int)(($current_index - 1) / $this->seats_per_row) + 1;
            $coluna = ($current_index - 1) % $this->seats_per_row + 1;
            $classe = Classe::EXECUTIVA;
        } else {
            $row = (int)(($current_index - $this->executive_count - 1) / $this->seats_per_row) + 1;
            $coluna = ($current_index - $this->executive_count - 1) % $this->seats_per_row + 1;
            $classe = Classe::STANDARD;
        }
        $fileira = chr(ord('A') + $coluna - 1);
        return new CodigoDoAssento($classe, $fileira, $row);
    }

    /** Gera todos os codigos do assento desse gerador
     * @return CodigoDoAssento[]
     */
    public function gerar_todos(): array
    {
        if ($this->getUltimoId() != 0) {
            throw new InvalidArgumentException("O gerador deve iniciar vazio");
        }
        $codigos = [];
        while ($this->passenger_count - $this->getUltimoId() != 0) {
            $codigos[] = $this->gerar();
        }
        return $codigos;
    }
}


class Email implements Equatable
{
    private const EMAIL_REGEX = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i';
    private const AT_REGEX = '/@/';
    private string $email;

    public function __construct(string $email)
    {
        $this->email = Email::validaEmail($email);
    }

    public function __toString(): string
    {
        return $this->email;
    }

    private static function validaEmail(string $email): string
    {
        if (!preg_match(Email::EMAIL_REGEX, $email)) {
            throw new InvalidArgumentException("Email invalido");
        }
        if (preg_match_all(EMAIL::AT_REGEX, $email) != 1) {
            throw new InvalidArgumentException("Email invalido");
        }
        return $email;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->email == $outro->email;
    }
}

class CPF implements Equatable
{
    private string $cpf;

    public function __construct(string $cpf)
    {
        $this->cpf = CPF::validaCPF($cpf);
    }

    public function __toString(): string
    {
        $cpf = $this->cpf;
        // Formata o CPF com separadores
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    private static function validaCPF(string $cpf): string
    {
        // Remover pontos e traços
        $cpf = str_replace('.', '', $cpf);
        $cpf = str_replace('-', '', $cpf);

        if (!ctype_digit($cpf) || strlen($cpf) != 11) {
            throw new InvalidArgumentException("CPF inválido");
        }

        // Verificar o primeiro dígito da validação
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }

        if ($sum % 11 < 2) {
            $digit_1 = 0;
        } else {
            $digit_1 = 11 - ($sum % 11);
        }

        // Verificar o segundo dígito da validação
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }

        if ($sum % 11 < 2) {
            $digit_2 = 0;
        } else {
            $digit_2 = 11 - ($sum % 11);
        }
        // Conferir os dois dígitos
        if (substr($cpf, -2) !== "{$digit_1}{$digit_2}") {
            throw new InvalidArgumentException("CPF inválido");
        }

        return $cpf;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->cpf == $outro->cpf;
    }
}

/** Um CEP normalizado e validado.
 *
 */
class CEP implements Equatable
{
    private string $cep;

    /** Constroi um CEP
     * @param string $cep
     * @throws InvalidArgumentException se o CEP não conter 8 digitos
     */
    public function __construct(string $cep)
    {
        $this->cep = CEP::validaCep($cep);
    }

    /** Valida um CEP
     * @param string $cep
     * @return string
     * @throws InvalidArgumentException se o CEP não conter 8 digitos
     */
    private static function validaCep(string $cep): string
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $cep);

        // Verifica se o CEP possui 8 caracteres
        if (strlen($cep) !== 8) {
            throw new InvalidArgumentException('CEP inválido');
        }

        if (!ctype_digit($cep)) {
            throw new InvalidArgumentException('CEP inválido');
        }

        return $cep;
    }

    public function __toString(): string
    {
        return substr($this->cep, 0, 5) . '-' . substr($this->cep, 5);
    }

    /** Retorna o cep
     * @return string
     */
    public function getCep(): string
    {
        return $this->cep;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->cep == $outro->cep;
    }
}


