<?php
    /* import re
    from enum import Enum
    from math import ceil
    from typing import Optional, Tuple

    from pydantic import BaseModel, validator */
    include_once("estado.php");
    include_once("enum_to_array.php");

    class SiglaCompanhiaAerea{
        public string $sigla;

        public function __construct(string $sigla)
        {
            $this->sigla = SiglaCompanhiaAerea::valida_sigla($sigla);
        }

        public function __toString(): string
        {
            return $this->sigla;
        }

        public static function valida_sigla(string $v): string
        {
            if (strlen($v) !== 2) {
                throw new Exception("Sigla invalida");
            }
            return $v;
        }
    }

    class CodigoVoo {
        public SiglaCompanhiaAerea $sigla_da_companhia;
        public int $numero;

        public function __construct(
            SiglaCompanhiaAerea $sigla_da_companhia,
            int $numero
        ) {
            $this->sigla_da_companhia = $sigla_da_companhia;
            $this->numero = CodigoVoo::valida_numero($numero);
        }

        public function __toString(): string
        {
            return $this->sigla_da_companhia->__toString() .
                sprintf('%04d', $this->numero);
        }

        public function valida_numero(int $v): int
        {
            if ($v < 0) {
                throw new Exception("O numero é negativo");
            }
            if ($v > 9999) {
                throw new Exception("O numero é muito grande");
            }
            return $v;
        }
    }

    enum PrefixoRegistroDeAeronave: string {
        use EnumToArray;
        case PT = "PT";
        case PR = "PR";
        case PP = "PP";
        case PS = "PS";
    }


    class RegistroDeAeronave {
        public PrefixoRegistroDeAeronave $prefixo;
        public string $sufixo;

        public function __construct(
            PrefixoRegistroDeAeronave $prefixo,
            string $sufixo
        ) {
            $this->prefixo = $prefixo;
            $this->sufixo = RegistroDeAeronave::valida_sufixo($sufixo);
        }

        public function __toString(): string
        {
            return "{$this->prefixo->value}-{$this->sufixo}";
        }

        public static function valida_sufixo(string $v): string
        {
            if (strlen($v) !== 3) {
                throw new Exception("O sufixo deve ter 3 letras");
            }
            if (strtolower($v) !== $v) {
                throw new Exception("O sufixo deve ser uppercase");
            }
            if (!ctype_alpha($v)) {
                throw new Exception("O sufixo deve ser composto somente por letras");
            }
            return $v;
        }
    }

    class RegistroDePassagem {
        public int $number;

        public function __construct(int $number)
        {
            $this->number = RegistroDePassagem::valida_numero($number);
        }

        public static function valida_numero(int $numero): int
        {
            if ($numero < 0) {
                throw new Exception("Numero deve ser não negativo");
            }
            return $numero;
        }

        public function __toString(): string
        {
            return "{$this->number}";
        }
    }

    class RegistroDeViagem {
        public string $prefixo;
        public int $numero;

        public function __construct(string $prefixo, int $numero)
        {
            $this->prefixo = RegistroDeViagem::valida_prefixo($prefixo);
            $this->numero = RegistroDeViagem::valida_numero($numero);
        }

        public function __toString(): string
        {
            return "{$this->prefixo}{$this->numero}";
        }

        public static function valida_prefixo(string $v): string
        {
            if (strlen($v) != 2) {
                throw new Exception("O prefixo deve ter dois caracteres");
            }
            if (!ctype_upper($v)) {
                throw new Exception("O prefixo deve ser uppercase");
            }
            if (!ctype_alpha($v)) {
                throw new Exception("O prefixo deve ser feito de letras");
            }
            return $v;
        }

        public static function valida_numero(int $v): int
        {
            if ($v < 0) {
                throw new Exception("O numero é negativo");
            }
            if ($v > 9999) {
                throw new Exception("O numero é muito grande");
            }
            return $v;
        }
    }

    class SiglaAeroporto {
        public $sigla;
    
        public function __construct($sigla) {
            $this->sigla = SiglaAeroporto::valida_sigla($sigla);
        }
    
        public function __toString() {
            return $this->sigla;
        }
    
        public function valida_sigla($v) {
            if (strlen($v) != 3) {
                throw new Exception("A sigla deve ter 3 caracteres");
            }
            if ($v != strtoupper($v)) {
                throw new Exception("A sigla deve ser uppercase");
            }
            if (!ctype_alpha($v)) {
                throw new Exception("A sigla deve ser feita de caracteres");
            }
            return $v;
        }
    }

    class RegistroDeTripulante {
        private int $idTripulante;

        public function __construct(int $idTripulante) {
            $this->idTripulante = $idTripulante;
        }

        public function __toString(): string {
            return "{$this->idTripulante}";
        }
    }

    class GeradorDeRegistroDeTripulante {
        private int $ultimo_id;

        public function __construct(int $ultimo_id = null){
            $this->ultimo_id = $ultimo_id ?? - 1;
        }

        public function gerar(): RegistroDeTripulante {
            $this->ultimo_id += 1;
            $id = $this->ultimo_id;
            return new RegistroDeTripulante($id);
        }
    }

        class RG {
        public $rg;

        public function __construct($rg)
        {
            $this->rg = RG::valida_rg($rg);
        }

        public function valida_rg($rg)
        {
            $estado = substr($rg, 2);
            $numeros = substr($rg, 0, 2);

            if (!in_array($estado, Estado::names())) {
                throw new Exception("Estado invalido");
            }

            if (!ctype_digit($numeros)) {
                throw new Exception("Resto do rg não é numero");
            }

            if (strlen($numeros) != 9) {
                throw new Exception("Não são 9 numeros");
            }

            $digitos = str_split($numeros);
            $weights = [2, 3, 4, 5, 6, 7, 8, 9];
            $sum_ = array_sum(array_map(function($d, $w) {
                return $d * $w;
            }, array_slice($digitos, 0, -1), $weights));
            $dv = ($sum_ % 11);
            if ($dv == 0) {
                $dv = 11;
            }
            if ($dv != $digitos[8]) {
                throw new Exception('Numero de RG invalido');
            }

            return $rg;
        }

        public function __toString()
        {
            return $this->rg;
        }
    }

    class Passaporte {
        public string $passaporte;

        public function __construct(string $passaporte) {
            $this->passaporte = Passaporte::valida_passaporte($passaporte);
        }

        public function __toString() {
            return $this->passaporte;
        }

        public function valida_passaporte(string $passaporte) {
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
    }

    class DocumentoPassageiro {
        private ?Passaporte $passaporte;
        private ?RG $rg;
    
        public function __construct(?Passaporte $passaporte = null, ?RG $rg = null) {
            if (!$passaporte && !$rg) {
                throw new Exception("Ou um passaporte ou um rg devem ser especificados");
            }
            if ($passaporte && $rg) {
                throw new Exception("somente rg ou passaporte devem ser especificados");
            }
            $this->passaporte = $passaporte;
            $this->rg = $rg;
        }
    
        public function documento(): Passaporte | RG {
            return $this->passaporte ?? $this->rg;
        }
    
        public function __toString(): string {
            return strval($this->documento());
        }
    }
    
    class GeradorDeRegistroDeViagem {
        private int $ultimo_id;
    
        public function __construct(int $ultimo_id = null) {
            $this->ultimo_id = $ultimo_id ?? -1;
        }
    
        public function gerar(): RegistroDeViagem {
            $this->ultimo_id += 1;
            $id = $this->ultimo_id;
            $numero = $id % 10000;
            $prefixo = chr(ord('A') + ($id - 1) / 26) . chr(ord('A') + ($id - 1) % 26);
            return new RegistroDeViagem($prefixo, $numero);
        }
    }
    
    class GeradorDeRegistroDePassagem {
        private int $ultimo_id;
    
        public function __construct(int $ultimo_id = null) {
            $this->ultimo_id = $ultimo_id ?? -1;
        }
    
        public function gerar(): RegistroDePassagem {
            $this->ultimo_id += 1;
            $id = $this->ultimo_id;
            return new RegistroDePassagem($id);
        }
    }
    
    enum Classe: string {
        case EXECUTIVA = "executiva";
        case STANDARD = "standard";

        public static function prefixo(Classe $classe): string {
            match ($classe) {
                Classe::EXECUTIVA => 'E',
                Classe::STANDARD => 'S',
            };
            throw new Exception("Classe desconhecida");
        }
    }

    class CodigoDoAssento {
        public function __construct(
            public Classe $classe,
            public string $coluna,
            public int $fileira
        ) {}

        public function __toString(): string {
            return Classe::prefixo($this->classe) . $this->coluna . str_pad($this->fileira, 2, "0", STR_PAD_LEFT);
        }
    }

    class GeradorDeCodigoDoAssento {
        private $passenger_count;
        private $executive_ratio;
        private $executive_count;
        private $standard_count;
        private $seats_per_row;
        private $rows_executive;
        private $rows_standard;
        private $current_index = 1;

        public function __construct(int $passenger_count, float $executive_ratio = 0.2) {
            $this->passenger_count = $passenger_count;
            $this->executive_ratio = $executive_ratio;
            $this->executive_count = (int) ($passenger_count * $executive_ratio);
            $this->standard_count = $passenger_count - $this->executive_count;
            $this->seats_per_row = $this->_calculate_seats_per_row();
            $this->rows_executive = ceil($this->executive_count / $this->seats_per_row);
            $this->rows_standard = ceil($this->standard_count / $this->seats_per_row);
        }

        private function _calculate_seats_per_row(): int {
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

        public function gerar(): CodigoDoAssento {
            if ($this->current_index <= $this->executive_count) {
                $row = (int) (($this->current_index - 1) / $this->seats_per_row) + 1;
                $coluna = ($this->current_index - 1) % $this->seats_per_row + 1;
                $classe = Classe::EXECUTIVA;
            } else {
                $row = (int) (($this->current_index - $this->executive_count - 1) / $this->seats_per_row) + 1;
                $coluna = ($this->current_index - $this->executive_count - 1) % $this->seats_per_row + 1;
                $classe = Classe::STANDARD;
            }
            $fileira = chr(ord('A') + $coluna - 1);
            $this->current_index += 1;
            $passengers_left = $this->passenger_count - ($this->current_index - 1);
            return new CodigoDoAssento($classe, $fileira, $row);
        }

        public function gerar_todos(): array {
            if ($this->current_index != 1) {
                throw new Exception("O gerador deve iniciar vazio");
            }
            $codigos = [];
            while ($this->passenger_count - ($this->current_index - 1) != 0) {
                $codigos[] = $this->gerar();
            }
            return $codigos;
        }
    }

    use Symfony\Component\Validator\Constraints as Assert;

    class Email {
        private const EMAIL_REGEX = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i';

        /**
         * @Assert\Email(message="Formato inválido de email")
         */
        private string $email;

        public function __construct(string $email)
        {
            $this->email = $email;
        }

        public function __toString(): string
        {
            return $this->email;
        }
    }

    class CPF {
        /**
         * @Assert\Regex(pattern="/^\d{3}\.\d{3}\.\d{3}-\d{2}$/", message="CPF inválido")
         */
        private string $cpf;

        public function __construct(string $cpf)
        {
            $this->cpf = $cpf;
        }

        public function __toString(): string
        {
            return $this->cpf;
        }
    }

/** Um CEP normalizado e validado.
 *
 */
    class CEP {
        private string $cep;

        /** Constroi um CEP
         * @param string $cep
         * @throws InvalidArgumentException se o CEP não conter 8 digitos
         */
        public function __construct(string $cep) {
            $this->cep = CEP::validaCep($cep);
        }

        /** Valida um CEP
         * @param string $cep
         * @return string
         * @throws InvalidArgumentException se o CEP não conter 8 digitos
         */
        private static function validaCep(string $cep): string {
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
            return $this->cep;
        }

        /** Retorna o cep
         * @return string
         */
        public function getCep(): string
        {
            return $this->cep;
        }
    }

/** Um endereço normalizado.
 * TODO: BACKLOG: Integrar api dos correios para validar os dados.
 *
 */
class Endereco {
    private string $logradouro;
    private int $numero;
    private string $bairro;
    private CEP $cep;
    private string $cidade;
    private Estado $estado;
    private string $referencia;

    public function __construct(
        string $logradouro,
        int $numero,
        string $bairro,
        CEP $cep,
        string $cidade,
        Estado $estado,
        string $referencia,) {
        $this->logradouro = $logradouro;
        $this->numero = $numero;
        $this->bairro = $bairro;
        $this->cep = $cep;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->referencia = $referencia;
    }
    /** Retorna o logradouro
     * @return string
     */
    public function getLogradouro(): string {
        return $this->logradouro;
    }

    /** Retorna o numero
     * @return int
     */
    public function getNumero(): int {
        return $this->numero;
    }

    /** Retorna o bairro
     * @return string
     */
    public function getBairro(): string {
        return $this->bairro;
    }

    /** Retorna o CEP
     * @return CEP
     */
    public function getCep(): CEP {
        return $this->cep;
    }

    /** Retorna a cidade
     * @return string
     */
    public function getCidade(): string {
        return $this->cidade;
    }

    /** Retorna o estado
     * @return Estado
     */
    public function getEstado(): Estado {
        return $this->estado;
    }

    /** Retorna a referencia
     * @return string
     */
    public function getReferencia(): string {
        return $this->referencia;
    }
}
                    


