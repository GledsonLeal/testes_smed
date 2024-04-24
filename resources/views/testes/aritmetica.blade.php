@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Teste de Aritmética Coletiva 4º e 5º Anos') }}</div>

                    <div class="card-body">
                        <div class="text-center"> <!-- Div centralizada horizontalmente -->
                            <a href="" style="margin-right: 10px;">
                                <img src="{{ asset('img/aritmetica.png') }}" alt="Descrição da imagem" style="width: 15%;">
                            </a>
                            <h1><span>Teste de Aritmética Coletiva 4º e 5º Anos</span></h1>
                        </div>
                        <div>
                            <form method="get" action="{{ route('aluno.aritmetica') }}">
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

                            @if ($escolaId && count($alunosQuartoAno) > 0)
                                <form method="POST" action="{{ route('aluno.aritmeticapostTeste') }}">
                                    @csrf
                                    <div class="card-header"><strong>Alunos do 4º ano da {{ $nomedaescola }}:</strong></div>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nome do aluno</th>
                                                <th>Resposta</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($alunosQuartoAno as $aluno)
                                                <tr>
                                                    <td>{{ $aluno->nome }}</td>
                                                    <td class="text-right">
                                                        <div class="input-group has-validation">
                                                            <input
                                                                class="form-control @if ($errors->has('resposta_' . $aluno->id)) is-invalid @endif"
                                                                type="text" value="{{ old('resposta_' . $aluno->id) }}"
                                                                name="resposta_{{ $aluno->id }}">
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
                            @endif
                            @if ($escolaId && count($alunosQuintoAno) > 0)
                                <div class="card-header"><strong>Alunos do 5º ano da {{ $nomedaescola }}:</strong></div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nome do aluno</th>
                                            <th>Resposta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alunosQuintoAno as $aluno)
                                            <tr>
                                                <td>{{ $aluno->nome }}</td>
                                                <td class="text-right">
                                                    <div class="input-group has-validation">
                                                        <input
                                                            class="form-control @if ($errors->has('resposta_' . $aluno->id)) is-invalid @endif"
                                                            type="text" value="{{ old('resposta_' . $aluno->id) }}"
                                                            name="resposta_{{ $aluno->id }}">
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
