@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>{{ __($titulo) }} da {{$nomedaescola}}</h1>
                    </div>
                    <div class="card-body">

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <h1>Alunos do Primeiro Ano:</h1>
                        @foreach ($alunosPrimeiroAno as $aluno)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Nome do aluno: {{ $aluno['nome'] }}</h5>
                                    <p class="card-text"><strong>Turma:</strong> {{ $aluno['turma'] }}</p>
                                    <p class="card-text"><strong>{{$teste}}:</strong>
                                        {{ htmlspecialchars(implode(', ', $aluno['resposta'])) }}</p>
                                    <p class="card-text"><strong>Percentil:</strong> {{ $aluno['percentil'] }}</p>
                                    <p class="card-text"><strong>Interpretação:</strong> {{ $aluno['interpretação'] }}</p>
                                </div>
                            </div>
                        @endforeach
                        <h1>Alunos do Segundo Ano</h1>
                        @foreach ($alunosSegundoAno as $aluno)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Nome do aluno: {{ $aluno['nome'] }}</h5>
                                    <p class="card-text"><strong>Turma:</strong> {{ $aluno['turma'] }}</p>
                                    <p class="card-text"><strong>{{$teste}}:</strong>
                                        {{ htmlspecialchars(implode(', ', $aluno['resposta'])) }}</p>
                                    <p class="card-text"><strong>Percentil:</strong> {{ $aluno['percentil'] }}</p>
                                    <p class="card-text"><strong>Interpretação:</strong> {{ $aluno['interpretação'] }}</p>
                                </div>
                            </div>
                        @endforeach
                        <h1>Alunos do Terceiro Ano</h1>
                        @foreach ($alunosTerceiroAno as $aluno)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Nome do aluno: {{ $aluno['nome'] }}</h5>
                                <p class="card-text"><strong>Turma:</strong> {{ $aluno['turma'] }}</p>
                                <p class="card-text"><strong>{{$teste}}:</strong>
                                    {{ htmlspecialchars(implode(', ', $aluno['resposta'])) }}</p>
                                <p class="card-text"><strong>Percentil:</strong> {{ $aluno['percentil'] }}</p>
                                <p class="card-text"><strong>Interpretação:</strong> {{ $aluno['interpretação'] }}</p>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
