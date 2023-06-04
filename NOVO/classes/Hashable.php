<?php

/**
 * An interface for hashable classes
 */
interface Hashable {
    /** Returns the hash code of the object
     * @return int
     */
    public function hashCode(): int;
}
function hashString(string $string): int {
    return crc32($string);
}

function hashInt(int $int): int {
    return crc32((string)$int);
}

function hashFloat(float $float): int {
    return crc32((string)$float);
}

function hashArray(array $array): int {
    $entriesToBeHashed = [];
    foreach ($array as $key => $value) {
        $entriesToBeHashed[] = $key;
        $entriesToBeHashed[] = $value;
    }
    return combineHash($entriesToBeHashed);
}
function hashNull($object): int {
    return 0;
}

function hash_bool(bool $object): int {
    return $object ? 1 : 0;
}

function hashObject(mixed $object): int {
    if ($object instanceof Hashable) {
        return $object->hashCode();
    }
    if (is_string($object)) {
        return hashString($object);
    }
    if (is_int($object)) {
        return hashInt($object);
    }
    if (is_float($object)) {
        return hashFloat($object);
    }
    if (is_array($object)) {
        return hashArray($object);
    }
    if (is_null($object)) {
        return hashNull($object);
    }
    if (is_bool($object)) {
        return hash_bool($object);
    }
    throw new InvalidArgumentException("Object is not hashable");
}
function combineHash(array $objects): int {
    $hash = 7;
    foreach ($objects as $object) {
        $objectHash = hashObject($object);
        $hash = 31 * $hash + $objectHash;
    }
    return $hash;
}