<?php
require_once "Sessao.php";
interface IAuthenticatable
{
    public function getSessao(): Sessao;
}