<?php

namespace App\Infra\Aluno;

use App\Dominio\Aluno\Aluno;
use App\Dominio\Aluno\AlunoNaoEncontradoException;
use App\Dominio\Aluno\AlunoRepositorio;
use App\Dominio\Cpf;

class AlunoRepositorioEmMemoria implements AlunoRepositorio
{
    private array $alunos = [];

    public function adicionar(Aluno $aluno): void
    {
        $this->alunos[] = $aluno;
    }

    public function buscarPorCpf(Cpf $cpf): Aluno
    {
        $alunosFiltrados = array_filter(
            $this->alunos,
            fn (Aluno $aluno) => $aluno->cpf() == $cpf
        );

        if (count($alunosFiltrados) === 0) {
            throw new AlunoNaoEncontradoException($cpf);
        }

        if (count($alunosFiltrados) > 1) {
            throw new \Exception();
        }

        return $alunosFiltrados[0];
    }

    public function buscarTodos(): array
    {
        return $this->alunos;
    }
}
