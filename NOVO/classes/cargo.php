<?php

require_once("enum_to_array.php");
require_once "log.php";

enum Cargo: string
{
    use EnumToArray;

    case PILOTO = 'piloto';
    case COPILOTO = 'copiloto';
    case COMISSARIO = 'comissario';
}

?>