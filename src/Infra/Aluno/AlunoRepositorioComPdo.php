<?php

namespace App\Infra\Aluno;

use App\Dominio\Aluno\Aluno;
use App\Dominio\Aluno\AlunoNaoEncontradoException;
use App\Dominio\Aluno\AlunoRepositorio;
use App\Dominio\Cpf;

class RepositorioDeAlunoComPdo implements AlunoRepositorio
{
    private \PDO $conexao;

    public function __construct(\PDO $conexao)
    {
        $this->conexao = $conexao;
    }

    public function adicionar(Aluno $aluno): void
    {
        $sql = 'INSERT INTO alunos (cpf, nome, email) VALUES (:cpf, :nome, :email);';
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue('cpf', $aluno->cpf());
        $stmt->bindValue('nome', $aluno->nome());
        $stmt->bindValue('email', $aluno->email());
        $stmt->execute();

        $sql = 'INSERT INTO telefones (ddd, numero, cpf_aluno) VALUES (:ddd, :numero, :cpf_aluno)';
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue('cpf_aluno', $aluno->cpf());

        /** @var Telefone $telefone */
        foreach ($aluno->telefones() as $telefone) {
            $stmt->bindValue('ddd', $telefone->ddd());
            $stmt->bindValue('numero', $telefone->numero());
            $stmt->execute();
        }
    }

    public function buscarPorCpf(Cpf $cpf): Aluno
    {
        $sql = 'SELECT cpf, nome, email, ddd, numero as telefone
                FROM alunos
                LEFT JOIN telefones ON telefones.cpf_aluno = alunos.cpf
                WHERE alunos.cpf = ?';
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(1, (string) $cpf);
        $stmt->execute();
        $dados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($dados) === 0) {
            throw new AlunoNaoEncontradoException($cpf);
        }
        return $this->mapearAluno($dados);
    }

    private function mapearAluno(array $dados): Aluno
    {
        $primeiraLinha = $dados[0];
        $aluno = Aluno::comCpfNomeEEmail($primeiraLinha['cpf'], $primeiraLinha['nome'], $primeiraLinha['email']);
        $telefones = array_filter($dados, fn ($linha) => $linha['ddd'] !== null && $linha['telefone'] !== null);
        foreach ($telefones as $linha) {
            $aluno->adicionarTelefone($linha['ddd'], $linha['telefone']);
        }
        return $aluno;
    }

    public function buscarTodos(): array
    {
        $sql = 'SELECT cpf, nome, email, ddd, numero as telefone
                FROM alunos
                LEFT JOIN telefones ON telefones.cpf_aluno = alunos.cpf
                ORDER BY nome';
        $stmt = $this->conexao->query($sql);
        $listaDadosAlunos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $alunos = [];
        foreach ($listaDadosAlunos as $dados) {
            if (!array_key_exists($dados['cpf'], $alunos)) {
                $alunos[$dados['cpf']] = Aluno::comCpfNomeEEmail($dados['cpf'], $dados['nome'], $dados['email']);
            }
            $alunos[$dados['cpf']]->adicionarTelefone($dados['ddd'], $dados['numero_telefone']);
        }
        return array_values($alunos);
    }
}
