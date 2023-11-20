@extends('emails.base-template')

@section('header')
    @include('emails.partials.header', [
        'title' => 'Confirmação de pagamento',
        'subtitle' => 'ID da compra: ##IDCOMPRA##',
    ])
@endsection

@section('content')
    <div>
        Olá, <b>##NOME##!</b> Somos da Xgrow.
    </div>

    <div>
        Gostaríamos de informar que foi realizada uma compra em sua conta referente ao produto <b>##NOMEPRODUTO##</b>.
        Para finalizá-la, voce precisa realizar o pagamento do boleto, que pode ser acessado logo abaixo:
    </div>

    <br>
    @include('emails.partials.button', ['href' => 'http://example.com', 'text' => 'Fazer download do boleto'])
    <br>

    <div>
        Ou, você pode copiar o código de barras:
    </div>

    <br>
    <div style="width: 100%; border: 1px solid white">
        <center>
            00000.00000 00000.000000 00000.000000 0 0000 0000000000
        </center>
    </div>
    <br>

    <div style="background: #343434; height: 1px; margin: 12px 0px"> </div>

    <div>
        Componente DETALHES_DA_COMPRA
    </div>

    <div style="background: #343434; height: 1px; margin: 12px 0px"> </div>

    <div>
        Caso já tenha efetuado o pagamento ignore esta mensagem.
    </div>

@endsection
