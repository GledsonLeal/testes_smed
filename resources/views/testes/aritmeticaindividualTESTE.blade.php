@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Teste de Aritmética Individual') }}</div>

                <div class="card-body">
                    <div class="text-center"> <!-- Div centralizada horizontalmente -->
                        <a href="" style="margin-right: 10px;">
                            <img src="{{ asset('img/aritmetica.png') }}" alt="Descrição da imagem" style="width: 15%;">
                        </a>
                        <h1><span>Aritmética Individual 1º, 2º e 3º Anos</span></h1>
                    </div>
                    <div>
                        <form method="get" action="{{ route('teste.aritmeticaindividual') }}">
                            @csrf
                            <div class="input-group mb-3">
                                <select class="form-select" id="escola_id" name="escola_id">
                                    <option value="">Selecione a Escola</option>
                                    @foreach ($escolas as $escola)
                                    <option value="{{ $escola->id }}" {{ $escolaId == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}
                                    </option>
                                    @endforeach
                                </select>
                                <select class="form-select" id="turma" name="turma">
                                    <option value="">Selecione a Turma</option>
                                    <option value="1">1º Ano</option>
                                    <option value="2">2º Ano</option>
                                    <option value="3">3º Ano</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Selecionar</button>
                            </div>
                        </form>

                        <div id="alunos_primeiro_ano" style="display: none;">
                            @if ($escolaId && count($alunosPrimeiroAno) > 0)
                            <form method="POST" action="{{ route('aluno.aritmeticaindividualpost') }}">
                                @csrf
                                <div class="card-header"><strong>Alunos do 1º ano da {{ $nomedaescola }}:</strong></div>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nome do aluno</th>
                                            <th>Resposta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alunosPrimeiroAno as $aluno)
                                        <tr>
                                            <td>{{ $aluno->nome }}</td>
                                            <td class="text-right">
                                                <div class="input-group has-validation">
                                                    <input class="form-control @if ($errors->has('resposta_' . $aluno->id)) is-invalid @endif" type="text" value="{{ old('resposta_' . $aluno->id) }}" name="resposta_{{ $aluno->id }}">
                                                    @if ($errors->has('resposta_' . $aluno->id))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('resposta_' . $aluno->id) }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary">Gravar Teste</button>
                            </form>
                            @endif
                        </div>

                        <div id="alunos_segundo_ano" style="display: none;">
                            @if ($escolaId && count($alunosSegundoAno) > 0)
                            <form method="POST" action="{{ route('aluno.aritmeticaindividualpost') }}">
                                @csrf
                                <div class="card-header"><strong>Alunos do 2º ano da {{ $nomedaescola }}:</strong></div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nome do aluno</th>
                                            <th>Resposta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alunosSegundoAno as $aluno)
                                        <tr>
                                            <td>{{ $aluno->nome }}</td>
                                            <td class="text-right">
                                                <div class="input-group has-validation">
                                                    <input class="form-control @if ($errors->has('resposta_' . $aluno->id)) is-invalid @endif" type="text" value="{{ old('resposta_' . $aluno->id) }}" name="resposta_{{ $aluno->id }}">
                                                    @if ($errors->has('resposta_' . $aluno->id))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('resposta_' . $aluno->id) }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary">Gravar Teste</button>
                            </form>
                            @endif
                        </div>

                        <div id="alunos_terceiro_ano" style="display: none;">
                            @if ($escolaId && count($alunosTerceiroAno) > 0)
                            <form method="POST" action="{{ route('aluno.aritmeticaindividualpost') }}">
                                @csrf
                                <div class="card-header"><strong>Alunos do 3º ano da {{ $nomedaescola }}:</strong></div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nome do aluno</th>
                                            <th>Resposta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alunosTerceiroAno as $aluno)
                                        <tr>
                                            <td>{{ $aluno->nome }}</td>
                                            <td class="text-right">
                                                <div class="input-group has-validation">
                                                    <input class="form-control @if ($errors->has('resposta_' . $aluno->id)) is-invalid @endif" type="text" value="{{ old('resposta_' . $aluno->id) }}" name="resposta_{{ $aluno->id }}">
                                                    @if ($errors->has('resposta_' . $aluno->id))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('resposta_' . $aluno->id) }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary">Gravar Teste</button>
                            </form>
                            @endif
                        </div>

                    </div>
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#turma').change(function() {
            var turma = $(this).val();
            if (turma == 1) {
                $('#alunos_primeiro_ano').show();
                $('#alunos_segundo_ano').hide();
                $('#alunos_terceiro_ano').hide();
            } else if (turma == 2) {
                $('#alunos_primeiro_ano').hide();
                $('#alunos_segundo_ano').show();
                $('#alunos_terceiro_ano').hide();
            } else if (turma == 3) {
                $('#alunos_primeiro_ano').hide();
                $('#alunos_segundo_ano').hide();
                $('#alunos_terceiro_ano').show();
            }
        });
    });
</script>
@endsection
