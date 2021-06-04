<?php

namespace App\Aplicacao\Aluno\MatricularAluno;

use App\Dominio\Aluno\Aluno;
use App\Dominio\Aluno\AlunoRepositorio;

class MatricularAluno
{
    private AlunoRepositorio $alunoRepositorio;

    public function __construct(AlunoRepositorio $alunoRepositorio)
    {
        $this->alunoRepositorio = $alunoRepositorio;
    }

    public function executa(MatricularAlunoDto $dados): void
    {
        $aluno = Aluno::comCpfNomeEEmail($dados->cpfAluno, $dados->nomeAluno, $dados->emailAluno);
        $this->alunoRepositorio->adicionar($aluno);
    }
}
