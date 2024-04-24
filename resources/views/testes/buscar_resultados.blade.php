@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Resultado dos Testes') }}</div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="get" action="{{ route('teste.escrita') }}">
                        @csrf
                        <h4>Marque os Testes</h4>

                        <div class="checkbox-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="escrita_coletiva_4_5" value="escrita_coletiva_4_5" id="escrita_coletiva_4_5">
                                <label class="form-check-label" for="escrita_coletiva_4_5">
                                    Escrita Coletiva 4º e 5º Anos
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aritmetica_coletiva_4_5" checked disabled value="aritmetica_coletiva_4_5" id="aritmetica_coletiva_4_5">
                                <label class="form-check-label" for="aritmetica_coletiva_4_5">
                                    Aritmética Coletiva 4º e 5º Anos
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="leitura_individual_4_5" checked disabled value="leitura_individual_4_5" id="leitura_individual_4_5">
                                <label class="form-check-label" for="leitura_individual_4_5">
                                    Leitura Individual 4º e 5º Anos
                                </label>
                            </div>
                        </div>
                        <h4>Selecione a Escola</h4>
                        <div class="input-group mb-3">
                            <select class="form-select" id="escola_id" name="escola_id">
                                <option value="">Selecione a Escola</option>
                                @foreach ($escolas as $escola)
                                <option value="{{ $escola->id }}" {{ $escolaId == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Selecionar</button>
                        </div>


                    </form>

                    @if (!is_null($alunosComResultados) && $alunosComResultados->isNotEmpty())
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Resultados dos Alunos do teste </h2>
                            @foreach ($alunosComResultados as $aluno)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Nome do aluno: {{ $aluno->nome }}</h5>
                                    <p class="card-text"><strong>Turma:</strong> {{ $aluno->etapa_aluno }}</p>

                                    @foreach ($aluno->escritas as $index => $escrita)
                                    <div class="card-body">
                                        @if ($index === 0)
                                        <p class="card-text"><strong>Escrita:</strong>
                                            {{ $escrita->escrita }}
                                        </p>
                                        <p class="card-text"><strong>Percentil:</strong>
                                            {{ $escrita->percentil }}
                                        </p>
                                        <p class="card-text"><strong>Interpretação:</strong>
                                            {{ $escrita->interpretacao }}
                                        </p>
                                        @endif
                                    </div>
                                    @endforeach

                                </div>
                            </div>
                            @endforeach




                        </div>
                    </div>
                    @else
                    <p>Nenhum resultado encontrado.</p>
                    @endif

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
@endsection