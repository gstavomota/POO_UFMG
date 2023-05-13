<?php
    require_once('identificadores.php');
    require_once('nacionalidades.php');
    require_once('companhia_area.php');
    require_once('aeroporto.php');
    require_once('cargo.php');

    use CPF;
    use Nacionalidade;
    use DateTime;
    use Cargo;
    use Email;

    class Tripulante {
        public string $nome;
        public string $sobrenome;
        public CPF $cpf;
        public Nacionalidade $nacionalidade;
        public DateTime $data_de_nascimento;
        public Email $email;
        public string $cht; // criar um identificador para esse documento
        public string $endereco;
        public CompanhiaAerea $companhia;
        public Aeroporto $aeroporto_base;
        public Cargo $cargo;
        public string $registro;


        public function __construct(companhia$nome, $sobrenome, $cpf, $nacionalidade, $data_de_nascimento, $email, $cht, $logradouro, $numero, $bairro, $cep, $cidade, $estado, $companhia, $aeroporto_base,$cargo, $registro) {
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->cpf = $cpf;
        $this->nacionalidade = $nacionalidade;
        $this->data_de_nascimento = $data_de_nascimento;
        $this->email = $email;
        $this->cht = $cht;
        $this->validaEndereco( $logradouro, $numero, $bairro, $cep, $cidade, $estado );       
        $this->companhia = $companhia;
        $this->aeroporto_base = $aeroporto_base;
        $this->cargo = $cargo;
        $this->registro = $registro;
        }

        public function getNome() {
            return $this->nome; 
        }

        public function getSobrenome() {
            return $this->sobrenome; 
        }

        public function getCpf() {
            return $this->cpf; 
        }

        public function getNacionalidade() {
            return $this->nacionalidade; 
        }

        public function getDataDeNascimento() {
            return $this->data_de_nascimento; 
        }

        public function getEmail() {
            return $this->email; 
        }

        public function getCht() {
            return $this->cht; 
        }

        public function getEndereco() {
            return $this->endereco; 
        }

        public function getCompanhia() {
            return $this->companhia; 
        }

        public function getAeroportoBase() {
            return $this->aeroporto_base; 
        }


        public function getCargo() {
            return $this->cargo; 
        }


        public function getRegistro() {
            return $this->registro; 
        }

        public function validaEndereco($logradouro, $numero, $bairro, $cep, $cidade, $estado) {
            // Verifica se o logradouro está vazio
            if (empty($logradouro)) {
                print_r('Por favor, insira o logradouro.');
            }
        
            // Verifica se o número está vazio
            if (empty($numero)) {
                print_r('Por favor, insira o número da residência.');
            }
        
            // Verifica se o bairro está vazio
            if (empty($bairro)) {
                print_r('Por favor, insira o bairro.');
            }
        
            // Verifica se o CEP está vazio ou não possui 8 dígitos
            if (empty($cep) || strlen($cep) != 8) {
                print_r('Por favor, insira um cep válido.');
            }
        
            // Verifica se a cidade está vazia
            if (empty($cidade)) {
                print_r('Por favor, insira a cidade.');
            }
        
            // Verifica se o estado está vazio ou não possui 2 caracteres
            if (empty($estado) || strlen($estado) != 2) {
                print_r('Por favor, insira a sigla do seu estado.');
            }
    
            $p_endereco = "{$logradouro}, número: {$numero}, {$bairro}, {$cidade}, {$estado}, cep: {$cep}";
    
            $this->endereco = $p_endereco;
        }
    }

    