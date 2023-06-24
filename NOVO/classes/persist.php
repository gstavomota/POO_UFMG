<?php
require_once('container.php');

/**
 * @template T of persist
 */
abstract class persist
{
    private ?int $index = null;

    public function __construct(?int $index = null)
    {
        $this->index = $index;
    }

    /**
     * @param T $pObj
     * @return void
     */
    public function load(mixed $pObj): void
    {
        $reflectionClass = new ReflectionClass($this);
        $reflectionProperties = $reflectionClass->getProperties();
        foreach ($reflectionProperties as $reflectionProperty) {
            $pObjValue = $reflectionProperty->getValue($pObj);
            if (!$reflectionProperty->isPublic()) {
                $reflectionProperty->setAccessible(true);
            }
            echo "{$reflectionProperty->getName()} : $pObjValue\n";
            $reflectionProperty->setValue($this, $pObjValue);
        }
    }

    public function save(): void
    {
        if (!is_null($this->index))
            $this->edit();
        else
            $this->insert();
    }

    private function insert(): void
    {
        /**
         * @var class-string<persist> $calledClass
         */
        $calledClass = get_called_class();
        $container = container::getInstance($calledClass::getFilename());
        $container->addObject($this);
        $container->persist();
    }

    private function edit(): void
    {
        /**
         * @var class-string<persist> $calledClass
         */
        $calledClass = get_called_class();
        $container = container::getInstance($calledClass::getFilename());
        $container->editObject($this->index, $this);
        $container->persist();
    }

    private static function getPossiblyNonPublicProperty(mixed $object, string $name): mixed
    {
        $rp = new ReflectionProperty($object, $name);
        if ($rp->isPublic()) {
            return $object->$name;
        }
        $rp->setAccessible(true);
        return $rp->getValue($object);
    }

    /**
     * @param string $p_field
     * @param mixed $p_value
     * @return T[]
     * @throws Exception
     */
    static public function getRecordsByField(string $p_field, mixed $p_value): array
    {
        /**
         * @var class-string<persist> $calledClass
         */
        $calledClass = get_called_class();
        $container = container::getInstance($calledClass::getFilename());
        $objs = $container->getObjects();
        $matchObjects = [];
        for ($i = 0; $i < count($objs); $i++) {
            if (equals(static::getPossiblyNonPublicProperty($objs[$i], $p_field), $p_value)) {
                $matchObjects[] = $objs[$i];
            }
        }
        return $matchObjects;
    }

    /**
     * @return T[]
     * @throws Exception
     */
    static public function getRecords(): array
    {
        /**
         * @var class-string<persist> $calledClass
         */
        $calledClass = get_called_class();
        $container = container::getInstance($calledClass::getFilename());
        return $container->getObjects();
    }

    static public function deleteAllRecords(): void {
        /**
         * @var class-string<persist> $calledClass
         */
        $calledClass = get_called_class();
        $container = container::getInstance($calledClass::getFilename());
        $container->deleteAllObjects();
        $container->persist();
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    public function delete(): void
    {
        if (is_null($this->index)) {
            echo "WARNING: Asking to delete an object of type " . get_called_class() . " that was not stored" . PHP_EOL;
            return;
        }
        /**
         * @var class-string<persist> $calledClass
         */
        $calledClass = get_called_class();
        $container = container::getInstance($calledClass::getFilename());
        $container->deleteObject($this->index);
        $container->persist();
    }

    abstract static public function getFilename(): string;
}