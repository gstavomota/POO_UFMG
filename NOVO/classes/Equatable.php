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

/**
 * @param Equatable[] $array1
 * @param Equatable[] $array2
 * @return Equatable[]
 */
function array_diff_equatable(array $array1, array $array2): array {
    /** @var Equatable[] $diff */
    $diff = [];
    foreach ($array1 as $item1) {
        $found = false;
        foreach ($array2 as $item2) {
            if ($item1->eq($item2)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $diff[] = $item1;
        }
    }
    return $diff;
}