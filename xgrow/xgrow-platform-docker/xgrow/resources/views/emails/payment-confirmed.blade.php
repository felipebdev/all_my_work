@extends('emails.base-template')

@section('header')
    @include('emails.partials.header', [
        'title' => 'Confirmação de compra',
        'subtitle' => 'ID da compra: ##IDCOMPRA##',
    ])
@endsection

@section('content')
    <div>
        Olá, <b>##NOME##!</b> Somos da Xgrow.
    </div>

    <div>
        Gostaríamos de informar que a sua compra referente ao produto <b>##NOMEPRODUTO##</b> foi confirmada com sucesso!
        Veja os detalhes abaixo:
    </div>

    <div style="background: #343434; height: 1px; margin: 12px 0px"></div>

    <div>
        @include('emails.partials.product-detail')
    </div>

    <div style="background: #343434; height: 1px; margin: 12px 0px"></div>

    <div>
        @include('emails.partials.buyer-info')
    </div>

    <div style="background: #343434; height: 1px; margin: 12px 0px"></div>

    <div>
        Agradecemos a preferência!
    </div>

@endsection
