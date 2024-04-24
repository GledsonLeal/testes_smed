@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Teste da Psicogênese da Língua Escrita') }}</div>

                    <div class="card-body">
                        <div class="text-center"> <!-- Div centralizada horizontalmente -->
                            <a href="" style="margin-right: 10px;">
                                <img src="{{ asset('img/psci.png') }}" alt="Descrição da imagem" style="width: 15%;">
                            </a>
                            <h1><span>Teste da Psicogênese da Língua Escrita 1º 2º e 3º Anos</span></h1>
                        </div>
                        <div>
                            <form method="get" action="{{ route('teste.psicogenese') }}">
                                @csrf
                                <div class="input-group mb-3">
                                    <select class="form-select" id="escola_id" name="escola_id">
                                        <option value="">Selecione a Escola</option>
                                        @foreach ($escolas as $escola)
                                            <option value="{{ $escola->id }}"
                                                {{ $escolaId == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">Selecionar</button>
                                </div>
                            </form>

                            @if ($escolaId && count($alunosPrimeiroAno) > 0)
                                <form method="POST" action="{{ route('aluno.psicogenesepost') }}">
                                    @csrf
                                    <div class="card-header"><strong>Alunos do 1º ano da {{ $nomedaescola }}:</strong></div>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nome do aluno</th>
                                                <th>Pré-silábico</th>
                                                <th>Silábico</th>
                                                <th>Silábico-alfabético</th>
                                                <th>Alfabético</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($alunosPrimeiroAno as $aluno)
                                                <tr>
                                                    <td>{{ $aluno->nome }}</td>
                                                    <div class="input-group has-validation">
                                                        <input type="hidden" name="resposta_{{ $aluno->id }}"
                                                            value="">
                                                        <!-- Adicione um campo hidden para garantir que pelo menos uma opção seja selecionada -->
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Pré-silábico"
                                                                    {{ old('resposta_' . $aluno->id) === 'Pré-silábico' ? 'checked' : '' }}>
                                                                Pré-silábico
                                                            </label></td>
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Silábico"
                                                                    {{ old('resposta_' . $aluno->id) === 'Silábico' ? 'checked' : '' }}>
                                                                Silábico
                                                            </label></td>
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Silábico-alfabético"
                                                                    {{ old('resposta_' . $aluno->id) === 'Silábico-alfabético' ? 'checked' : '' }}>
                                                                Silábico-alfabético
                                                            </label></td>
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Alfabético"
                                                                    {{ old('resposta_' . $aluno->id) === 'Alfabético' ? 'checked' : '' }}>
                                                                Alfabético
                                                            </label></td>
                                                    </div>
                                                </tr>
                                                <tr style="border-top: none">
                                                    <td></td> <!-- Coluna vazia para manter a estrutura da tabela -->
                                                    <td colspan="4">
                                                        @if ($errors->has('resposta_' . $aluno->id))
                                                            <div class="invalid-feedback"
                                                                style="display: inline-block; border-top: none;">
                                                                {{ $errors->first('resposta_' . $aluno->id) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            @endif

                            @if ($escolaId && count($alunosSegundoAno) > 0)
                                <form method="POST" action="{{ route('aluno.psicogenesepost') }}">
                                    @csrf
                                    <div class="card-header"><strong>Alunos do 2º ano da {{ $nomedaescola }}:</strong>
                                    </div>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nome do aluno</th>
                                                <th>Pré-silábico</th>
                                                <th>Silábico</th>
                                                <th>Silábico-alfabético</th>
                                                <th>Alfabético</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($alunosSegundoAno as $aluno)
                                                <tr>
                                                    <td>{{ $aluno->nome }}</td>
                                                    <div class="input-group has-validation">
                                                        <input type="hidden" name="resposta_{{ $aluno->id }}"
                                                            value="">
                                                        <!-- Adicione um campo hidden para garantir que pelo menos uma opção seja selecionada -->
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Pré-silábico"
                                                                    {{ old('resposta_' . $aluno->id) === 'Pré-silábico' ? 'checked' : '' }}>
                                                                Pré-silábico
                                                            </label></td>
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Silábico"
                                                                    {{ old('resposta_' . $aluno->id) === 'Silábico' ? 'checked' : '' }}>
                                                                Silábico
                                                            </label></td>
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Silábico-alfabético"
                                                                    {{ old('resposta_' . $aluno->id) === 'Silábico-alfabético' ? 'checked' : '' }}>
                                                                Silábico-alfabético
                                                            </label></td>
                                                        <td><label class="radio-inline">
                                                                <input type="radio" name="resposta_{{ $aluno->id }}"
                                                                    value="Alfabético"
                                                                    {{ old('resposta_' . $aluno->id) === 'Alfabético' ? 'checked' : '' }}>
                                                                Alfabético
                                                            </label></td>
                                                    </div>
                                                </tr>
                                                <tr style="border-top: none">
                                                    <td></td> <!-- Coluna vazia para manter a estrutura da tabela -->
                                                    <td colspan="4">
                                                        @if ($errors->has('resposta_' . $aluno->id))
                                                            <div class="invalid-feedback"
                                                                style="display: inline-block; border-top: none;">
                                                                {{ $errors->first('resposta_' . $aluno->id) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            @endif

                            @if ($escolaId && count($alunosTerceiroAno) > 0)
                                <div class="card-header"><strong>Alunos do 3º ano da {{ $nomedaescola }}:</strong></div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nome do aluno</th>
                                            <th>Pré-silábico</th>
                                            <th>Silábico</th>
                                            <th>Silábico-alfabético</th>
                                            <th>Alfabético</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alunosTerceiroAno as $aluno)
                                        <tr>
                                            <td>{{ $aluno->nome }}</td>
                                            <div class="input-group has-validation">
                                                <input type="hidden" name="resposta_{{ $aluno->id }}"
                                                    value="">
                                                <!-- Adicione um campo hidden para garantir que pelo menos uma opção seja selecionada -->
                                                <td><label class="radio-inline">
                                                        <input type="radio" name="resposta_{{ $aluno->id }}"
                                                            value="Pré-silábico"
                                                            {{ old('resposta_' . $aluno->id) === 'Pré-silábico' ? 'checked' : '' }}>
                                                        Pré-silábico
                                                    </label></td>
                                                <td><label class="radio-inline">
                                                        <input type="radio" name="resposta_{{ $aluno->id }}"
                                                            value="Silábico"
                                                            {{ old('resposta_' . $aluno->id) === 'Silábico' ? 'checked' : '' }}>
                                                        Silábico
                                                    </label></td>
                                                <td><label class="radio-inline">
                                                        <input type="radio" name="resposta_{{ $aluno->id }}"
                                                            value="Silábico-alfabético"
                                                            {{ old('resposta_' . $aluno->id) === 'Silábico-alfabético' ? 'checked' : '' }}>
                                                        Silábico-alfabético
                                                    </label></td>
                                                <td><label class="radio-inline">
                                                        <input type="radio" name="resposta_{{ $aluno->id }}"
                                                            value="Alfabético"
                                                            {{ old('resposta_' . $aluno->id) === 'Alfabético' ? 'checked' : '' }}>
                                                        Alfabético
                                                    </label></td>
                                            </div>
                                        </tr>
                                        <tr style="border-top: none">
                                            <td></td> <!-- Coluna vazia para manter a estrutura da tabela -->
                                            <td colspan="4">
                                                @if ($errors->has('resposta_' . $aluno->id))
                                                    <div class="invalid-feedback"
                                                        style="display: inline-block; border-top: none;">
                                                        {{ $errors->first('resposta_' . $aluno->id) }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <button type="submit" class="btn btn-primary">Gravar Teste</button>
                                </form>
                            @endif
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                    {{--
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
            @endif
            --}}
                </div>
            </div>
        </div>
    </div>
@endsection
