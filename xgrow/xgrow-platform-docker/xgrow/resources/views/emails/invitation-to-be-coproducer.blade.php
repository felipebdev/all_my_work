@extends('emails.base-template')
@section('header')
    <h1 class="header h1" style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans',sans-serif; font-size: 24px; font-weight: 700; font-style: normal; line-height: 100%; text-align: center;">
        Olá {{ $data['name'] }}!
    </h1>
@endsection
@section('content')
    <div>
        <p style="padding:0;margin:0">
            Você foi convidado para fazer parte como Co-Produtor, do Produto
            <b>{{ $data['product_name'] }}</b>, acesse o link abaixo e confirme para poder ter o acesso.
        </p>
        @include('emails.partials.base-template-button', [
            'href' => url('/login'),
            'text' => 'Link do convite'
        ])
        <p style="padding:0;margin:20px 0 0 0;">
            Dúvidas, entrem em contato com o suporte do produtor através do email suporteprodutor@xgrow.com
        </p>
    </div>
@endsection

