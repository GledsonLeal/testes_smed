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
                                <button type="submit" class="btn btn-primary">Selecionar</button>
                            </div>
                        </form>

                        @if (!empty($percentuaisEscrita))
                            <h3>Resultado da escola {{ $nomedaescola }}</h3>
                            <h5>Teste de Escrita Coletiva 4º e 5º Anos:</h5>
                            <ul>
                                @foreach ($percentuaisEscrita as $interpretacao => $percentual)
                                    <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                @endforeach
                            </ul>

                        @endif


                        @if (!empty($percentuaisAritmeticacoletiva))
                            <h5>Teste de Aritmética Coletiva 4º e 5º Anos:</h5>
                            <ul>
                                @foreach ($percentuaisAritmeticacoletiva as $interpretacao => $percentual)
                                    <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (!empty($percentuaisAritmeticaindividual))
                            <h5>Teste de Aritmética Individual 1º, 2º e 3º Anos:</h5>
                            <ul>
                                @foreach ($percentuaisAritmeticacoletiva as $interpretacao => $percentual)
                                    <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (!empty($percentuaisLeituraindividual))
                            <h5>Teste de Leitura Individual 1º, 2º, 3º, 4º e 5º Anos:</h5>
                            <ul>
                                @foreach ($percentuaisLeituraindividual as $interpretacao => $percentual)
                                    <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (!empty($percentuaisPsicogenese))
                            <h5>Teste da Psicogênese da Língua Escrita 1º, 2º e 3º Anos:</h5>
                            <ul>
                                @foreach ($percentuaisPsicogenese as $interpretacao => $percentual)
                                    <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (!empty($percentuaisPCFO))
                            <h5>Prova de Consciência Fonológica por Produção Oral:</h5>
                            <ul>
                                @foreach ($percentuaisPCFO as $interpretacao => $percentual)
                                    <li>{{ $interpretacao }}: {{ $percentual }}%</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (empty($percentuaisEscrita) &&
                                empty($percentuaisAritmeticacoletiva) &&
                                empty($percentuaisAritmeticaindividual) &&
                                empty($percentuaisLeituraindividual) &&
                                empty($percentuaisPsicogenese) &&
                                empty($percentuaisPCFO))
                            <p>Não há dados disponíveis na {{ $nomedaescola }} para exibição.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
