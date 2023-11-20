@extends('templates.monster.main')

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-warning">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Planos</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/getnet/subscriptions">Assinaturas</a></li>
                <li class="breadcrumb-item active">Getnet</li>
            </ol>
        </div>
    </div>

    <table id="plan-table" class="table fandone-table">
        <thead class="default-background text-white">
        <tr>
            <th>Seller Id (Getnet)</th>
            <th>Order Id</th>
            <th>Data de criação</th>
            <th>Fim da assinatura</th>
            <th>Dia de pagamento</th>
            <th>Cliente Id</th>
            <th>Assinatura Id</th>
            <th>Status</th>
            <th>Status detalhes</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($subscriptions as $item) <tr>
            <td>{{ $item->seller_id }}</td>
            <td>{{ (isset($item->order_id)) ?? $item->order_id }}</td>
            <td>{{ $item->create_date }}</td>
            <td>{{ (isset($item->end_date)) ?? $item->end_date }}</td>
            <td>{{ $item->payment_date }}</td>
            <td>{{ $item->customer->customer_id }}</td>
            <td>{{ $item->subscription->subscription_id }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->status_details }}</td>
            <td>
                <div class="d-flex justify-content-between">
                    <a href="{{ url("/getnet/subscriptions/{$item->subscription->subscription_id}") }}" class="fandone-edit">
                        <i class="fa fa-edit"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection
