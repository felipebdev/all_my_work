<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Comprovante de estorno</title>
</head>

<body>
    <div>
        <div>
            <h4>Comprovante de estorno</h4><br>
        </div>
        <div>
            <div><br></div>
            <div style="font-size:18px">O valor de {{ $data['purchase']['total'] }} do número de pedido
                {{ $data['refund']['code'] }} foi estornado e estará disponível em sua conta de acordo com o
                processamento do seu
                banco.</div>
        </div>
        <div><br></div>
        <div style="font-size:14px">
            <div>Dados do comprador:</div>
            <div>Nome: {{ $data['subscriber']['name'] }}</div>
            <div>{{ $data['subscriber']['document_type'] }}: {{ $data['subscriber']['document_number'] }}</div>
            <div>E-mail: {{ $data['subscriber']['email'] }}</div>
            <div>Celular: {{ $data['subscriber']['cellphone'] }}</div>
            <div><br></div>
            <div>Dados da compra:</div>
            <div>Produto: {{ $data['purchase']['product'] }}</div>
            <div>Total: {{ $data['purchase']['total'] }}</div>
        </div>
        <div><br></div>
        <div><br></div>
        <div>
            @php
                use Carbon\Carbon;
                $now = Carbon::now();
            @endphp
            <p style="font-size:12px">Gerado em {{ $now->format('d/m/Y \à\s H:i:s') }}</p><br>
        </div>
    </div>
</body>

</html>
