<?php

require_once "Equatable.php";

/** Uma exception para quando for utilizado o tipo incorreto em uma comparação
 *
 */
class ComparableTypeException extends Exception {
    public function __construct(string $message = "Invalid type in Comparission", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/** Uma interface que descreve classes comparaveis
 *
 */
interface Comparable extends Equatable {
    /** Operador de comparação >
     * @param Comparable $other
     * @return bool
     */
    public function gt(self $other);

    /** Operador de comparação >=
     * @param Comparable $other
     * @return bool
     */
    public function gte(self $other);

    /** Operador de comparação <
     * @param Comparable $other
     * @return bool
     */
    public function st(self $other);

    /** Operador de comparação <=
     * @param Comparable $other
     * @return bool
     */
    public function ste(self $other);
}