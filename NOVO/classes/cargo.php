<?php

include_once("enum_to_array.php");

enum Cargo: string {
    use EnumToArray;
    case PILOTO = 'piloto';
    case COPILOTO = 'copiloto';
    case COMISSARIO = 'comissario';
}
?>