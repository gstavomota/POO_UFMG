<?php
include_once "HashableAndEquatable.php";

/**
 * @template K of HashableAndEquatable
 * @template V
 */
class HashMapEntry
{
    /**
     * @var K
     */
    public readonly HashableAndEquatable $key;
    /**
     * @var V
     */
    public mixed $value;

    public function __construct(HashableAndEquatable $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}

/**
 * @template K of HashableAndEquatable
 * @template V
 */
class HashMap
{
    /**
     * @var array<int, array<int, HashMapEntry<K, V>>>
     */
    private array $buckets;
    private int $size;

    public function __construct()
    {
        $this->buckets = [];
        $this->size = 0;
    }

    /**
     * Adds a key-value pair to the map.
     *
     * @param HashableAndEquatable $key The key.
     * @param mixed $value The value.
     * @return void
     */
    public function put(HashableAndEquatable $key, mixed $value): void
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (!isset($this->buckets[$bucketIndex])) {
            $this->buckets[$bucketIndex] = [];
        }

        foreach ($this->buckets[$bucketIndex] as $entry) {
            if ($key->eq($entry->key)) {
                $entry->value = $value;
                return;
            }
        }

        $this->buckets[$bucketIndex][] = new HashMapEntry($key, $value);
        $this->size++;
    }

    /**
     * Retrieves the value associated with the specified key.
     *
     * @param K $key The key.
     * @return V|null The value associated with the key, or null if the key is not found.
     */
    public function get(HashableAndEquatable $key): mixed
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (isset($this->buckets[$bucketIndex])) {
            foreach ($this->buckets[$bucketIndex] as $entry) {
                if ($key->eq($entry->key)) {
                    return $entry->value;
                }
            }
        }

        return null;
    }

    /**
     * Removes the key-value pair associated with the specified key.
     *
     * @param K $key The key.
     * @return void
     */
    public function remove(HashableAndEquatable $key): void
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (isset($this->buckets[$bucketIndex])) {
            foreach ($this->buckets[$bucketIndex] as $index => $entry) {
                if ($key->eq($entry->key)) {
                    unset($this->buckets[$bucketIndex][$index]);
                    $this->size--;
                    return;
                }
            }
        }
    }

    /**
     * Checks if the map contains the specified key.
     *
     * @param K $key The key.
     * @return bool True if the key is found, false otherwise.
     */
    public function containsKey(HashableAndEquatable $key): bool
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (isset($this->buckets[$bucketIndex])) {
            foreach ($this->buckets[$bucketIndex] as $entry) {
                if ($key->eq($entry->key)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes all entries from the map.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->buckets = [];
        $this->size = 0;
    }

    /**
     * Returns the number of entries in the map.
     *
     * @return int The size of the map.
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Returns an array of all the entries in the map.
     *
     * @return HashMapEntry[] An array of HashMapEntry objects.
     */
    public function entries(): array
    {
        $entries = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Returns an array of all the keys in the map.
     *
     * @return HashableAndEquatable[] An array of HashableAndEquatable objects representing the keys.
     */
    public function keys(): array
    {
        $keys = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                $keys[] = $entry->key;
            }
        }

        return $keys;
    }

    /**
     * Returns an array of all the values in the map.
     *
     * @return mixed[] An array of values.
     */
    public function values(): array
    {
        $values = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                $values[] = $entry->value;
            }
        }

        return $values;
    }


    private function getBucketIndex(HashableAndEquatable $key): int
    {
        $hashCode = $key->hashCode();
        return crc32($hashCode) % PHP_INT_MAX;
    }
}