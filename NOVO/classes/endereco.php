<?php
require_once "estado.php";
require_once "identificadores.php";
require_once "log.php";

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
    private ?string $referencia;

    public function __construct(
        string $logradouro,
        int    $numero,
        string $bairro,
        CEP    $cep,
        string $cidade,
        Estado $estado,
        ?string $referencia,)
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
        return log::getInstance()->logRead($this->logradouro);
    }

    /** Retorna o numero
     * @return int
     */
    public function getNumero(): int
    {
        return log::getInstance()->logRead($this->numero);
    }

    /** Retorna o bairro
     * @return string
     */
    public function getBairro(): string
    {
        return log::getInstance()->logRead($this->bairro);
    }

    /** Retorna o CEP
     * @return CEP
     */
    public function getCep(): CEP
    {
        return log::getInstance()->logRead($this->cep);
    }

    /** Retorna a cidade
     * @return string
     */
    public function getCidade(): string
    {
        return log::getInstance()->logRead($this->cidade);
    }

    /** Retorna o estado
     * @return Estado
     */
    public function getEstado(): Estado
    {
        return log::getInstance()->logRead($this->estado);
    }

    /** Retorna a referencia
     * @return ?string
     */
    public function getReferencia(): ?string
    {
        return log::getInstance()->logRead($this->referencia);
    }

    private static function validaLogradouro(string $v): string
    {
        if (empty($v))
            throw new InvalidArgumentException('Por favor, insira o logradouro.');
        return $v;
    }

    private static function validaNumero(int $v): int
    {
        if ($v < 0)
            throw new InvalidArgumentException('O número da residência não pode ser negativo.');
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

    private static function validaReferencia(?string $v): ?string
    {
        if (is_null($v)) {
            return null;
        }
        if (empty($v)) {
            throw new InvalidArgumentException('Por favor, insira uma referência ou passe nulo.');
        }
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
