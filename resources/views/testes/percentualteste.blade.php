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
                        <form method="post" action="{{ route('resultado.percentual') }}">
                            @csrf
                            <div class="input-group mb-3">
                                <select class="form-select" id="escola_id" name="escola_id">
                                    <option value="">Selecione a Escola</option>
                                    @foreach ($escolas as $escola)
                                        <option value="{{ $escola->id }}"
                                            {{ old('escola_id') == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select" id="tipo_teste" name="tipo_teste">
                                    <option value="">Selecione o Tipo de Teste</option>
                                    <option value="escrita_coletiva">Teste de Escrita Coletiva</option>
                                    <option value="aritmetica_coletiva">Teste de Aritmética Coletiva</option>
                                    <option value="aritmetica_individual">Teste de Aritmética Individual</option>
                                    <option value="leitura_individual">Teste de Leitura Individual</option>
                                    <option value="psicogenese">Teste da Psicogênese da Língua Escrita</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Selecionar</button>
                            </div>
                        </form>
                        
                        @if(isset($percentuais))
                            <h1>Resultado da escola {{$nomedaescola}}</h1>
                            @foreach ($percentuais as $tipoTeste => $dadosTeste)
                                <h5>{{ $tipoTeste }}:</h5>
                                <ul>
                                    @foreach ($dadosTeste as $interpretacao => $percentual)
                                        <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @else
                            <p>Não há dados disponíveis para o teste selecionado.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
