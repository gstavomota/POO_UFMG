<?php
require_once "AuthenticationManager.php";
require_once "Sessao.php";
require_once "companhia_aerea.php";
require_once 'IAuthenticatable.php';

class CompanhiaAereaController implements IAuthenticatable
{
    private CompanhiaAerea $companhiaAerea;
    private Sessao $sessao;
public function __construct(CompanhiaAerea $companhiaAerea, Sessao $sessao)
{
    $this->companhiaAerea = $companhiaAerea;
    $this->sessao = $sessao;
}

    public function getSessao(): Sessao
    {
        return $this->sessao;
    }
    public function getNome(): string
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->getNome();
    }
    public function getCodigo(): string
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->getCodigo();
    }
    public function getRazaoSocial(): string
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->getRazaoSocial();
    }
    public function getSigla(): SiglaCompanhiaAerea
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->getSigla();
    }
    public function getTarifaFranquia(): float
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->getTarifaFranquia();
    }
    public function adicionarViagensEmVenda(): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
         $this->companhiaAerea->adicionarViagensEmVenda();
    }
    public function registrarAeronaveNaViagem(RegistroDeViagem $registroDeViagem, RegistroDeAeronave $registroDeAeronave, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->registrarAeronaveNaViagem($registroDeViagem, $registroDeAeronave, );
    }
    public function registrarTripulanteNaViagem(RegistroDeViagem $registroDeViagem, RegistroDeTripulante $registroDeTripulante, ICoordenada $coordenada, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->registrarTripulanteNaViagem($registroDeViagem, $registroDeTripulante, $coordenada, );
    }
    public function registrarQueViagemAconteceu(DataTempo $hora_de_partida, DataTempo $hora_de_chegada, RegistroDeViagem $registro_de_viagem, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->registrarQueViagemAconteceu($hora_de_partida, $hora_de_chegada, $registro_de_viagem, );
    }
    public function cancelarPassagem(RegistroDePassagem $registroDePassagem, ): Passagem
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->cancelarPassagem($registroDePassagem, );
    }
    public function abrirCheckInParaPassagens(RegistroDePassagem $args, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->abrirCheckInParaPassagens($args, );
    }
    public function acessarHistoricoDeViagens(DocumentoPessoa $documentoPessoa, ): array
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->acessarHistoricoDeViagens($documentoPessoa, );
    }
    public function fazerCheckIn(RegistroDePassagem $registroDePassagem, ): array
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->fazerCheckIn($registroDePassagem, );
    }
    public function embarcar(RegistroDePassagem $registroDePassagem, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->embarcar($registroDePassagem, );
    }
    public function comprarPassagem(DocumentoPessoa $documentoPessoa, Data $data, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada, FranquiasDeBagagem $franquias, ?CodigoDoAssento $assento, ): ?RegistroDePassagem
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->comprarPassagem($documentoPessoa, $data, $aeroporto_de_saida, $aeroporto_de_chegada, $franquias, $assento, );
    }
    public function registrarVoo(int $numero, SiglaAeroporto $aeroporto_de_saida, SiglaAeroporto $aeroporto_de_chegada, Tempo $hora_de_partida, Duracao $duracao_estimada, array $dias_da_semana, RegistroDeAeronave $aeronave_padrao, float $tarifa, int $pontuacaoMilhagem, ): Voo
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->registrarVoo($numero, $aeroporto_de_saida, $aeroporto_de_chegada, $hora_de_partida, $duracao_estimada, $dias_da_semana, $aeronave_padrao, $tarifa, $pontuacaoMilhagem, );
    }
    public function encontrarVoo(CodigoVoo $voo, ): ?Voo
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->encontrarVoo($voo, );
    }
    public function adicionarPassageiro(Passageiro $passageiro, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->adicionarPassageiro($passageiro, );
    }
    public function encontrarPassageiro(DocumentoPessoa $passageiro, ): ?Passageiro
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->encontrarPassageiro($passageiro, );
    }
    public function registrarTripulante(string $nome, string $sobrenome, DocumentoPessoa $documento, Nacionalidade $nacionalidade, ?CPF $cpf, Data $data_de_nascimento, Email $email, string $cht, Endereco $endereco, SiglaAeroporto $aeroporto_base, Cargo $cargo, ): Tripulante
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->registrarTripulante($nome, $sobrenome, $documento, $nacionalidade, $cpf, $data_de_nascimento, $email, $cht, $endereco, $aeroporto_base, $cargo, );
    }
    public function encontrarTripulante(RegistroDeTripulante $tripulante, ): ?Tripulante
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->encontrarTripulante($tripulante, );
    }
    public function encontrarViagem(RegistroDeViagem $registroDeViagem, ): ?Viagem
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->encontrarViagem($registroDeViagem, );
    }
    public function registrarAeronave(string $fabricante, string $modelo, int $capacidade_passageiros, float $capacidade_carga, RegistroDeAeronave $registro, ): Aeronave
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->registrarAeronave($fabricante, $modelo, $capacidade_passageiros, $capacidade_carga, $registro, );
    }
    public function encontrarAeronave(RegistroDeAeronave $registro, ): ?Aeronave
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->encontrarAeronave($registro, );
    }
    public function encontrarPassagem(RegistroDePassagem $registro, ): ?Passagem
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->encontrarPassagem($registro, );
    }
    public function getFurgao(RegistroDeViagem $registroDeViagem, ): Onibus
    {
        AuthenticationManager::getInstance()->checkSession($this);
        return $this->companhiaAerea->getFurgao($registroDeViagem, );
    }
    public function load(mixed $pObj, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->load($pObj, );
    }
    public function save(): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->save();
    }
    public function setIndex(int $index, ): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->setIndex($index, );
    }
    public function delete(): void
    {
        AuthenticationManager::getInstance()->checkSession($this);
        $this->companhiaAerea->delete();
    }

}