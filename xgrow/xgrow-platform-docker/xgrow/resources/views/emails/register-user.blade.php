@extends('emails.base-template')
@section('header')
    <h1 class="header h1" style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans',sans-serif; font-size: 24px; font-weight: 700; font-style: normal; line-height: 100%; text-align: center;">
        Olá {{ $data['name'] }}!
    </h1>
@endsection
@section('content')
    <div>
        <p style="padding:0;margin:0;color: #E8E8E8; font-family: 'Open Sans','sans-serif';">
            Parabéns, seu cadastro na Xgrow foi aprovado! Estamos muito felizes
            por você fazer parte da revolução do ensino.
        </p>
        <p style="padding:0;margin:0; color: #E8E8E8; font-family: 'Open Sans','sans-serif';">
            Você pode acessar a sua conta com os seguintes dados:
        </p>
        <p style="padding:0;margin:20px 0 0 0;">
            Email: <b>{{ $data['email'] }}</b><br />
            Senha: <b>{{ $data['password'] }}</b>
        </p>
    </div>
    @include('emails.partials.base-template-button', [
        'href' => env('APP_URL'),
        'text' => 'Acessar'
    ])
@endsection
