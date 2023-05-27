<?php

include_once("estado.php");
include_once("enum_to_array.php");
include_once("Equatable.php");

class SiglaCompanhiaAerea implements Equatable
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
}

class CodigoVoo implements Equatable
{
    public SiglaCompanhiaAerea $sigla_da_companhia;
    public int $numero;

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
}

enum PrefixoRegistroDeAeronave: string
{
    use EnumToArray;

    case PT = "PT";
    case PR = "PR";
    case PP = "PP";
    case PS = "PS";
}


class RegistroDeAeronave implements Equatable
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
        if (strtolower($v) !== $v) {
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
}

class RegistroDePassagem implements Equatable
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
}

class RegistroDeViagem implements Equatable
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

class GeradorDeRegistroDeVeiculo {
    private int $ultimo_id;

    public function __construct(int $ultimo_id = null) {
        $this->ultimo_id = $ultimo_id  ?? -1;
    }
    
    public function gerar(): RegistroDeVeiculo 
    {
        $this->ultimo_id += 1;
        $id = $this->ultimo_id;
        return new RegistroDeVeiculo($id);
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

class RegistroDeTripulante implements Equatable
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
}

class GeradorDeRegistroDeTripulante
{
    private int $ultimo_id;

    public function __construct(int $ultimo_id = null)
    {
        $this->ultimo_id = $ultimo_id ?? -1;
    }

    public function gerar(): RegistroDeTripulante
    {
        $this->ultimo_id += 1;
        $id = $this->ultimo_id;
        return new RegistroDeTripulante($id);
    }
}

class RG implements Equatable
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
}

class Passaporte implements Equatable
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
}

class DocumentoPassageiro implements Equatable
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
}

class GeradorDeRegistroDeViagem
{
    private int $ultimo_id;

    public function __construct(int $ultimo_id = null)
    {
        $this->ultimo_id = $ultimo_id ?? -1;
    }

    public function gerar(): RegistroDeViagem
    {
        $this->ultimo_id += 1;
        $id = $this->ultimo_id;

        $prefixIndex = floor($id / 10000);
        $numberPart = $id % 10000;

        $prefix = chr(($prefixIndex / 26) + 65) . chr(($prefixIndex % 26) + 65);

        return new RegistroDeViagem($prefix, $numberPart);
    }
}

class GeradorDeRegistroDePassagem
{
    private int $ultimo_id;

    public function __construct(int $ultimo_id = null)
    {
        $this->ultimo_id = $ultimo_id ?? -1;
    }

    public function gerar(): RegistroDePassagem
    {
        $this->ultimo_id += 1;
        $id = $this->ultimo_id;
        return new RegistroDePassagem($id);
    }
}

enum Classe: string
{
    case EXECUTIVA = "executiva";
    case STANDARD = "standard";

    public static function prefixo(Classe $classe): string
    {
        match ($classe) {
            Classe::EXECUTIVA => 'E',
            Classe::STANDARD => 'S',
        };
        throw new InvalidArgumentException("Classe desconhecida");
    }
}

class CodigoDoAssento implements Equatable
{
    private Classe $classe;
    private string $coluna;
    private int $fileira;

    public function __construct(
        Classe $classe,
        string $coluna,
        int    $fileira
    )
    {
        $this->classe = $classe;
        $this->coluna = $coluna;
        $this->fileira = $fileira;
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

}

class GeradorDeCodigoDoAssento
{
    private int $passenger_count;
    private int $executive_count;
    private int $standard_count;
    private int $seats_per_row;
    private int $current_index = 1;

    public function __construct(int $passenger_count, float $executive_ratio = 0.2)
    {
        $this->passenger_count = $passenger_count;
        $this->executive_count = (int)($passenger_count * $executive_ratio);
        $this->standard_count = $passenger_count - $this->executive_count;
        $this->seats_per_row = $this->_calculate_seats_per_row();
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
        if ($this->current_index <= $this->executive_count) {
            $row = (int)(($this->current_index - 1) / $this->seats_per_row) + 1;
            $coluna = ($this->current_index - 1) % $this->seats_per_row + 1;
            $classe = Classe::EXECUTIVA;
        } else {
            $row = (int)(($this->current_index - $this->executive_count - 1) / $this->seats_per_row) + 1;
            $coluna = ($this->current_index - $this->executive_count - 1) % $this->seats_per_row + 1;
            $classe = Classe::STANDARD;
        }
        $fileira = chr(ord('A') + $coluna - 1);
        $this->current_index += 1;
        $passengers_left = $this->passenger_count - ($this->current_index - 1);
        return new CodigoDoAssento($classe, $fileira, $row);
    }

    /** Gera todos os codigos do assento desse gerador
     * @return CodigoDoAssento[]
     */
    public function gerar_todos(): array
    {
        if ($this->current_index != 1) {
            throw new InvalidArgumentException("O gerador deve iniciar vazio");
        }
        $codigos = [];
        while ($this->passenger_count - ($this->current_index - 1) != 0) {
            $codigos[] = $this->gerar();
        }
        return $codigos;
    }
}


class Email implements Equatable
{
    private const EMAIL_REGEX = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i';
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

        if (!is_numeric($cpf) || strlen($cpf) != 11) {
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
        return $this->email == $outro->email;
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

        // Verifica se o CEP possui 8 dígitos
        if (strlen($cep) !== 8) {
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

/** Um endereço normalizado.
 * TODO: BACKLOG: Integrar api dos correios para validar os dados.
 *
 */
class Endereco implements Equatable
{
    private string $logradouro;
    private int $numero;
    private string $bairro;
    private CEP $cep;
    private string $cidade;
    private Estado $estado;
    private string $referencia;

    public function __construct(
        string $logradouro,
        int    $numero,
        string $bairro,
        CEP    $cep,
        string $cidade,
        Estado $estado,
        string $referencia,)
    {
        $this->logradouro = Endereco::validaLogradouro($logradouro);
        $this->numero = Endereco::validaNumero($numero);
        $this->bairro = Endereco::validaBairro($bairro);
        $this->cep = $cep;
        $this->cidade = Endereco::validaCidade($cidade);
        $this->estado = $estado;
        $this->referencia = Endereco::validaReferencia($referencia);
    }

    /** Retorna o logradouro
     * @return string
     */
    public function getLogradouro(): string
    {
        return $this->logradouro;
    }

    /** Retorna o numero
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /** Retorna o bairro
     * @return string
     */
    public function getBairro(): string
    {
        return $this->bairro;
    }

    /** Retorna o CEP
     * @return CEP
     */
    public function getCep(): CEP
    {
        return $this->cep;
    }

    /** Retorna a cidade
     * @return string
     */
    public function getCidade(): string
    {
        return $this->cidade;
    }

    /** Retorna o estado
     * @return Estado
     */
    public function getEstado(): Estado
    {
        return $this->estado;
    }

    /** Retorna a referencia
     * @return string
     */
    public function getReferencia(): string
    {
        return $this->referencia;
    }

    private static function validaLogradouro(string $v): string
    {
        if (empty($v))
            throw new InvalidArgumentException('Por favor, insira o logradouro.');
        return $v;
    }

    private static function validaNumero(int $v): int
    {
        if (empty($v))
            throw new InvalidArgumentException('Por favor, insira o número da residência.');
        return $v;
    }

    private static function validaBairro(string $v): string
    {
        if (empty($v))
            throw new InvalidArgumentException('Por favor, insira o bairro.');
        return $v;
    }

    private static function validaCidade(string $v): string
    {
        if (empty($v))
            throw new InvalidArgumentException('Por favor, insira a cidade.');
        return $v;
    }

    private static function validaReferencia(string $v): string
    {
        if (empty($v))
            throw new InvalidArgumentException('Por favor, insira uma referência.');
        return $v;
    }

    public function eq(Equatable $outro): bool
    {
        if (!$outro instanceof self) {
            throw new EquatableTypeException();
        }
        return $this->logradouro === $outro->logradouro &&
            $this->numero === $outro->numero &&
            $this->bairro === $outro->bairro &&
            $this->cep === $outro->cep &&
            $this->cidade === $outro->cidade &&
            $this->estado === $outro->estado &&
            $this->referencia === $outro->referencia;
    }
}
                    


