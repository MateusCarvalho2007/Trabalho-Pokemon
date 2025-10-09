<?php

require_once __DIR__."\..\bd\MySQL.php";

class Treinador {
    private int $idTreinador;
    
    public function __construct(private string $nome,private string $email,private string $senha){
    }

    public function setIdTreinador(int $idTreinador): void {
        $this->idTreinador = $idTreinador;
    }


    public function getIdTreinador(): ?int {
        return $this->idTreinador;
    }


    public function setSenha(string $senha): void {
        $this->senha = $senha;
    }

    public function setNome(string $nome): void {
        $this->nome = $nome;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }


    public function getSenha(): string {
        return $this->senha;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function save(): bool{
        $conexao = new MySQL();
        $this->senha = password_hash($this->senha, PASSWORD_BCRYPT);
        if (isset($this->idTreinador)) {
            $sql = "UPDATE treinador SET nome = '{$this->nome}',email = '{$this->email}', senha = '{$this->senha}' WHERE idTreinador = {$this->idTreinador}";
        } else {
            $sql = "INSERT INTO treinador (nome,email, senha) VALUES ('{$this->nome}','{$this->email}', '{$this->senha}')";
        }
        return $conexao->executa($sql);
    }

    public function authenticate():bool{
        $conexao = new MySQL();
        $sql = "SELECT idTreinador, nome, email, senha FROM treinador WHERE email = '{$this->email}'";
        $resultados = $conexao->consulta($sql);
        if(count($resultados)>0){
            if(password_verify($this->senha, $resultados[0]['senha'])){
                session_start();
                $_SESSION['idTreinador'] = $resultados[0]['idTreinador'];
                $_SESSION['email'] = $resultados[0]['email'];
                $_SESSION['nome'] = $resultados[0]['nome'];
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}