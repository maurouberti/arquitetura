<?php

namespace App\Dominio\Aluno;

use App\Dominio\Cpf;

interface AlunoRepositorio
{
    public function adicionar(Aluno $aluno): void;
    public function buscarPorCpf(Cpf $cpf): Aluno;
    /** 
     * @return Aluno[]
     */
    public function buscarTodos(): array;
}
