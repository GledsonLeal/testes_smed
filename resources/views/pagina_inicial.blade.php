@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="background-color: transparent;">
                <div class="card-header">{{ __('Bem-vindo aos testes!') }}</div>

                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/'">
                                <img src="{{ asset('img/Design_a_playful_logo_for_an_assessment_to_be_a__2_-removebg.png') }}" style="max-height: 100px;">
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/teste/psicogenese'">
                                <img src="{{ asset('img/psci.png')}}" class="img-fluid" style="max-height: 100px;" onmouseover="this.style.backgroundColor='transparent';" onmouseout="this.style.backgroundColor='transparent';">
                                <span>Psicogênese da Língua Escrita 1º 2º e 3º Anos</span>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/teste/escrita'">
                                <img src="{{ asset('img/escrita.png')}}" class="img-fluid" style="max-height: 100px;" onmouseover="this.style.backgroundColor='transparent';" onmouseout="this.style.backgroundColor='transparent';">
                                <span>Escrita Coletiva 4º e 5º Anos</span>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/teste/aritmetica'">
                                <img src="{{ asset('img/aritmetica.png')}}" alt="Descrição da imagem" class="img-fluid" style="max-height: 100px;" onmouseover="this.style.backgroundColor='transparent';" onmouseout="this.style.backgroundColor='transparent';">
                                <span>Aritmética Coletiva 4º e 5º Anos</span>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/teste/aritmeticaindividual'">
                                <img src="{{ asset('img/aritmetica.png')}}" alt="Descrição da imagem" class="img-fluid" style="max-height: 100px;" onmouseover="this.style.backgroundColor='transparent';" onmouseout="this.style.backgroundColor='transparent';">
                                <span>Aritmética Individual 1º, 2º e 3º Anos</span>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/teste/leitura'">
                                <img src="{{ asset('img/leitura.png')}}" alt="Descrição da imagem" class="img-fluid" style="max-height: 100px;" onmouseover="this.style.backgroundColor='transparent';" onmouseout="this.style.backgroundColor='transparent';">
                                <span>Leitura Individual 4º e 5º Anos</span>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="window.location.href='/teste/leituraindividual'">
                                <img src="{{ asset('img/leitura.png')}}" alt="Descrição da imagem" class="img-fluid" style="max-height: 100px;" onmouseover="this.style.backgroundColor='transparent';" onmouseout="this.style.backgroundColor='transparent';">
                                <span>Leitura Individual 1º, 2º e 3º Anos</span>
                            </button>
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
@endsection

