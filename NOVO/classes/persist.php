<?php
include_once('container.php');

abstract class persist
{
    private ?int $index = null;

    public function __construct()
    {
        if (func_num_args() == 0) {

        } else if (func_num_args() == 1) {
            $this->index = func_get_arg(1);
        } else {
            throw(new Exception('Eror ao instanciar objeto da classe Persist - Número de parâmetros incorreto.'));
        }
    }

    public function __destruct()
    {
        //print "Destroying " . __CLASS__ . "\n";
    }

    public function load($p_obj): void
    {
        $class_vars = get_class_vars(get_class($p_obj));
        foreach ($class_vars as $name => $value) {
            echo "$name : $value\n";
            $this->$name = $value;
        }
    }

    public function save(): void
    {
        if ($this->index != null)
            $this->edit();
        else
            $this->insert();
    }

    private function insert(): void
    {
        $container = new container(get_called_class()::getFilename());
        //print_r(get_called_class()::getFilename()); exit();
        $container->addObject($this);
        $container->persist();
    }

    private function edit(): void
    {
        $container = new container(get_called_class()::getFilename());
        $container->editObject($this->index, $this);
        $container->persist();
    }

    static public function getRecordsByField(string $p_field, mixed $p_value): array
    {
        $container = new container(get_called_class()::getFilename());
        //$container = container::getInstance(get_called_class()::getFilename());
        $objs = $container->getObjects();
        $matchObjects = array();
        for ($i = 0; $i < count($objs); $i++) {
            if (equals($objs[$i]->$p_field, $p_value)) {
                array_push($matchObjects, $objs[$i]);
            }
        }
        //if ( count($matchObjects) > 0 )
        return $matchObjects;
        //else
        //    throw( new Exception('Registro não encontrado.'));
    }

    static public function getRecords(): array
    {
        $container = new container(get_called_class()::getFilename());
        //$container = container::getInstance(get_called_class()::getFilename());
        $objs = $container->getObjects();
        return $objs;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    public function __toString()
    {
        return print_r($this, true);
    }

    abstract static public function getFilename();
}