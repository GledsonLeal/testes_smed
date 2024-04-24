@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="background-color: transparent;">
                <div class="card-header">{{ __('Erro!') }}</div>
                <div class="card-body text-center">
                    <img src="{{ asset('img/419page.png') }}" style="max-height: 350px;">
                    <button type="button" class="btn btn-outline-primary mt-3" onclick="window.location.href='/home'">
                        <span>Clique aqui para retornar à página inicial</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
