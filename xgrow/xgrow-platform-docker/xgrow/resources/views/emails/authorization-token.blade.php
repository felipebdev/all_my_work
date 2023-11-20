@extends('emails.base-template')
@section('header')
    <h1 class="header h1" style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans',sans-serif; font-size: 24px; font-weight: 700; font-style: normal; line-height: 100%; text-align: center;">
        Olá {{ $name }}!
    </h1>
@endsection
@section('content')
    <div>
        <p>
            Recentemente, você recebeu instruções para enviar um código de autenticação
            para alteração de seus dados bancários da sua conta na Xgrow.
        </p>
        <p>
            Seu código é: <b>{{$token}}</b>
        </p>
        <p>
            O código de autenticação vai expirar em 10 minutos.
        </p>
        <p>
            Copie e cole este dado no local indicado na sua dashboard.
        </p>
    </div>
@endsection
