<?php

namespace App\Testes\Aplicacao\Aluno;

use App\Aplicacao\Aluno\MatricularAluno\MatricularAluno;
use App\Aplicacao\Aluno\MatricularAluno\MatricularAlunoDto;
use App\Dominio\Cpf;
use App\Infra\Aluno\AlunoRepositorioEmMemoria;
use PHPUnit\Framework\TestCase;

class MatricularAlunoTest extends TestCase
{
    public function testAlunoDeveSerAdicionadoAoRepositorio()
    {
        $dadosAluno = new MatricularAlunoDto(
            '123.456.789-10',
            'Teste',
            'email@example.com',
        );
        $alunoRepositorio = new AlunoRepositorioEmMemoria();

        $matricula = new MatricularAluno($alunoRepositorio);
        $matricula->executa($dadosAluno);

        $aluno = $alunoRepositorio->buscarPorCpf(new Cpf('123.456.789-10'));

        $this->assertSame('Teste', (string) $aluno->nome());
        $this->assertSame('email@example.com', (string) $aluno->email());
        $this->assertEmpty($aluno->telefones());
    }
}
