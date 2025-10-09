<?php

require_once __DIR__."\..\bd\MySQL.php";

class Pokemon {
    private int $idPokemon;
    
    public function __construct(private string $nome,private string $tipo,private string $descricao){
    }

    public function setIdPokemon(int $idPokemon): void {
        $this->idPokemon = $idPokemon;
    }

    public function getIdPokemon(): ?int {
        return $this->idPokemon;
    }

    public function setNome(string $nome): void {
        $this->nome = $nome;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function setTipo(string $tipo): void {
        $this->tipo = $tipo;
    }

    public function getTipo(): string {
        return $this->tipo;
    }

    public function setDescricao(string $descricao): void {
        $this->descricao = $descricao;
    }

    public function getDescricao(): string {
        return $this->descricao;
    }

    public function save(): bool{
        $conexao = new MySQL();
        if (isset($this->idPokemon)) {
            $sql = "UPDATE pokemon SET nome = '{$this->nome}', tipo = '{$this->tipo}', descricao = '{$this->descricao}' WHERE idPokemon = {$this->idPokemon}";
        } else {
            $sql = "INSERT INTO pokemon (nome, tipo, descricao) VALUES ('{$this->nome}', '{$this->tipo}', '{$this->descricao}')";
        }
        return $conexao->executa($sql);
    }

    public static function findall():array{
        $conexao = new MySQL();
        $sql = "SELECT * FROM pokemon";
        $resultados = $conexao->consulta($sql);
        $pokemons = array();
        foreach($resultados as $resultado){
            $p = new Pokemon($resultado['nome'],$resultado['tipo'],$resultado['descricao']);
            $p->setIdPokemon($resultado['idPokemon']);
            $pokemons[] = $p;
        }
        return $pokemons;
    }

    public static function find($id):Pokemon{
        $conexao = new MySQL();
        $sql = "SELECT * FROM pokemon WHERE idPokemon = {$id}";
        $resultado = $conexao->consulta($sql);
        $p = new Pokemon($resultado[0]['nome'],$resultado[0]['tipo'],$resultado[0]['descricao']);
        $p->setIdPokemon($resultado[0]['idPokemon']);
        return $p;
    }

    public static function findallByUsuario($idTreinador):array{
        $conexao = new MySQL();
        $sql = "SELECT * FROM pokemon WHERE idTreinador = {$idTreinador}";
        $resultados = $conexao->consulta($sql);
        $pokemons = array();
        foreach($resultados as $resultado){
            $p = new Pokemon($resultado['nome'],$resultado['tipo'],$resultado['descricao']);
            $p->setIdPokemon($resultado['idPokemon']);
            $pokemons[] = $p;
        }
        return $pokemons;
    }

}