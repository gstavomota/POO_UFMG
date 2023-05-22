<?php

//namespace Persist;
class container
{
    private string $folder = 'dataFiles';
    private string $filename;
    private array $objects;
    static private container|null $ptr_container = null;
    static private bool $criticalSection = false;

    public function __construct()
    {
        if (func_num_args() == 1) {
            //$this->filename = func_get_arg(0);
            $this->setFilename(func_get_arg(0));
            $this->objects = [];
            $this->load();
        } else if (func_num_args() == 0) {
            $this->filename = 'testFile.txt';
        } else {
            throw(new Exception('Eror ao instanciar objeto da classe Container - Número de parâmetros incorreto.'));
        }
        self::$criticalSection = true;
    }

    public function __destruct()
    {
        self::$criticalSection = false;
    }

    static function getInstance(string $filename): container
    {
        while (self::$criticalSection)
            sleep(1);
        if (self::$ptr_container == null)
            self::$ptr_container = new container($filename);
        else
            self::$ptr_container->setFilename($filename);
        return self::$ptr_container;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = __DIR__ . '/' . $this->folder . '/' . $filename;
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
        // TO DO
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
                $jogador = unserialize($dados);
                $this->objects = $jogador->objects;
                //print_r($jogador); exit();
            }
        } else
            $this->objects = [];
    }

    public function persist(): void
    {
        for ($i = 0; $i < count($this->objects); $i++)
            $this->objects[$i]->setIndex($i + 1);
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