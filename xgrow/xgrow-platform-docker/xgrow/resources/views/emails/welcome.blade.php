@extends('emails.base-template')

@section('header')
    @include('emails.partials.header', [
        'title' => 'Bem vindo, ##NOME##',
        'subtitle' => 'Estamos felizes de ter você como nosso parceiro!',
    ])
@endsection

@section('content')
    <div>
        Agora que você já se cadastrou em nosso site, será necessário realizar uma confirmação para que você possa
        continuar utilizando nosso sistema. É simples, basta clicar no link abaixo:
    </div>

    <br>
    @include('emails.partials.button', ['href' => 'http://example.com', 'text' => 'Confirmar cadastro'])
    <br>

    <div>
        Caso este não funcione, copie e cole o link abaixo em seu navegador:
    </div>

    @include('emails.partials.link', ['href' => 'http://www.example.com'])
@endsection
