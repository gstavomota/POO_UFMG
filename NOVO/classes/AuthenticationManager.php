<?php

require_once "persist.php";
require_once "Sessao.php";
require_once "Usuario.php";

class AuthenticationException extends Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class AuthenticationManager extends persist
{
    private ?Sessao $sessaoAtual = null;
    private GeradorDeSessao $geradorDeSessao;
    /**
     * @var array<string, Usuario>
     */
    private array $usuarios;
    private static string $filename = "authentication_manager.txt";
    private function __construct()
    {
        $this->geradorDeSessao = new GeradorDeSessao();
        parent::__construct(0);
    }
    private static ?AuthenticationManager $instance = null;

    static public function getInstance(): AuthenticationManager {
        if (!is_null(static::$instance)) {
            return static::$instance;
        }
        if (count(static::getRecords()) > 1) {
            static::deleteAllRecords();
        }
        if (count(static::getRecords()) == 0) {
            return static::$instance = new AuthenticationManager();
        }
        return static::getRecords()[0];
    }

    public function checkSession(IAuthenticatable $authenticatable): void {
        if ($this->sessaoAtual && $authenticatable->getSessao()->eq($this->sessaoAtual)) {
            return;
        }
        throw new AuthenticationException();
    }

    public function authenticate(string $usuario, string $senha): Sessao {
        $usuarioObj = $this->usuarios[$usuario];
        if (isset($usuarioObj) && $usuarioObj->getSenha() == $senha) {
            return $this->geradorDeSessao->gerar($usuarioObj);
        }
        throw new AuthenticationException();
    }

    public function criarConta(string $usuario, string $email, string $senha): void {
        if (isset($this->usuarios[$usuario])) {
            throw new InvalidArgumentException("usuario ja existente");
        }
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getEmail() != $email) {
                continue;
            }
            throw new InvalidArgumentException("usuario com o mesmo email ja existente");
        }
        $this->usuarios[] = new Usuario($usuario, $email, $senha);
    }

    public function autenticarComEmail(string $email, string $senha): Sessao {
        /**
         * @var ?Usuario $usuarioComEmail
         */
        $usuarioComEmail = null;
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getEmail() != $email) {
                continue;
            }
            $usuarioComEmail = $usuario;
            break;
        }
        if (is_null($usuarioComEmail)) {
            throw new AuthenticationException();
        }
        if ($usuarioComEmail->getSenha() != $senha) {
            throw new AuthenticationException();
        }
        return $this->geradorDeSessao->gerar($usuarioComEmail);
    }
    public function getUsuarioAtual(): ?string {
        if (is_null($this->sessaoAtual)) {
            return null;
        }
        return $this->sessaoAtual->getUsuario()->getUsuario();
    }

    static public function getFilename(): string
    {
        return static::$filename;
    }
}