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
function equals(mixed $a, mixed $b, bool $strict = true): bool
{
    if ($a instanceof Equatable && $b instanceof Equatable) {
        return $a->eq($b);
    }
    if (is_string($a) && is_string($b)) {
        return strcmp($a, $b) === 0;
    }
    if (is_array($a) && is_array($b)) {
        $a_keys = array_keys($a);
        $b_keys = array_keys($b);

        if (count($a_keys) !== count($b_keys)) {
            return false;
        }

        sort($a_keys);
        sort($b_keys);

        for ($i = 0; $i < count($a_keys); $i++) {
            if (!equals($a_keys[$i], $b_keys[$i], $strict)) {
                return false;
            }
        }

        foreach ($a_keys as $key) {
            if (!equals($a[$key], $b[$key], $strict)) {
                return false;
            }
        }

        return true;
    }
    return $strict ? $a === $b : $a == $b;
}