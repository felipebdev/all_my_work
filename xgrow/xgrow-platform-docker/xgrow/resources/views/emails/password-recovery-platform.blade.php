@extends('emails.base-template')
@section('header')
        <h1 class="header h1" style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans',sans-serif; font-size: 24px; font-weight: 700; font-style: normal; line-height: 100%; text-align: center;"> Olá {{$name}}! </h1>
@endsection
@section('content')
            <div>
                Você recebeu esse email porque nós recebemos um requisição de redefinição de senha de sua conta.
            </div>
            @include('emails.partials.base-template-button', [
                'href' => url('/password/reset/' . $token),
                'text' => 'Redefinir senha'
            ])
            <div>
                Esse link de redefinição se expirará em 30 minutos.
                <br/>
                Se você não solicitou uma redefinição de senha, nenhuma ação adicional será necessária.
                <br />
            </div>
@endsection
