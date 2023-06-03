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