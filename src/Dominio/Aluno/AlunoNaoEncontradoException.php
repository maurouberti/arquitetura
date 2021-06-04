<?php

namespace App\Dominio\Aluno;

use App\Dominio\Cpf;

class AlunoNaoEncontradoException extends \DomainException
{
    public function __construct(Cpf $cpf)
    {
        parent::__construct("Aluno com CPF $cpf não encontrado");
    }
}
