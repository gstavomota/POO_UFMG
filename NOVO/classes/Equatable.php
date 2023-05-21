<?php
/** Uma exception para quando for utilizado o tipo incorreto em uma equalidade
 *
 */
class EquatableTypeException extends Exception {
    public function __construct(string $message = "Invalid type in Equality", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/** Uma interface que descreve classes igualaveis
 *
 */
interface Equatable
{

    /** Operador de igualdade ==
     * @param Equatable $other
     * @return bool
     */
    public function eq(self $other): bool;
}