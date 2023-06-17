<?php
include_once "../../classes/endereco.php";

trait EnderecoMixin {
    protected Endereco $endereco;
    function initEndereco() {
        $logradouro = "Avenida Amazonas";
        $numero = 1;
        $bairro = "Gutierrez";
        $cep = new CEP("30150-312");
        $cidade = "Belo Horizonte";
        $estado = Estado::MG;
        $referencia = "Proximo a Avenida Silva Lobo";
        $this->endereco = new Endereco(
            $logradouro,
            $numero,
            $bairro,
            $cep,
            $cidade,
            $estado,
            $referencia
        );
    }
}