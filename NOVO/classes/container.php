<?php

//namespace Persist;
class container
{
    private string $folder = 'dataFiles';
    private string $filename;
    private array $objects;
    static private container|null $ptr_container = null;

    public function __construct()
    {
        if (func_num_args() == 1) {
            //$this->filename = func_get_arg(0);
            $this->setFilename(func_get_arg(0));
        } else if (func_num_args() == 0) {
            $this->filename = 'testFile.txt';
        } else {
            throw(new Exception('Eror ao instanciar objeto da classe Container - Número de parâmetros incorreto.'));
        }
    }

    static function getInstance(string $filename): container
    {
        if (self::$ptr_container == null)
            self::$ptr_container = new container($filename);
        else
            self::$ptr_container->setFilename($filename);
        return self::$ptr_container;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = __DIR__ . '/' . $this->folder . '/' . $filename;
        $this->objects = [];
        $this->load();
    }

    public function addObject(mixed $p_obj): void
    {
        $this->objects[] = $p_obj;
    }

    public function editObject(int $p_index, mixed $p_obj): void
    {
        $this->objects[$p_index - 1] = $p_obj;
    }

    // Deletes an object from objects array
    public function deleteObject(int $p_index): void
    {
        unset($this->objects[$p_index]);
    }

    public function deleteAllObjects(): void {
        $this->objects = [];
    }

    public function getObjects(): array
    {
        $this->load();
        return $this->objects;
    }


    public function load(): void
    {
        if (is_file($this->filename)) {
            $dados = file_get_contents($this->filename);
            if ($dados <> '') {
                /**
                 * @var container $reconstructed_container
                 */
                $reconstructed_container = unserialize($dados);
                $this->objects = $reconstructed_container->objects;
                //print_r($jogador); exit();
            }
        } else
            $this->objects = [];
    }

    public function persist(): void
    {
        $i = 0;
        foreach ($this->objects as $object) {
            $object->setIndex($i + 1);
            $i++;
        }
        $serialized = serialize($this);
        file_put_contents($this->filename, $serialized);
    }

    /* get's e set's aqui */
    public function __sleep()
    {
        return ["filename", "objects"];
    }

    public function __wakeup()
    {

    }


}