@extends('templates.monster.main')

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Planos</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/subscribers">Clientes</a></li>
                <li class="breadcrumb-item active">Getnet</li>
            </ol>
        </div>
    </div>

    <table id="plan-table" class="table fandone-table">
        <thead class="default-background text-white">
        <tr>
            <th>Customer Id (Getnet)</th>
            <th>Nome</th>
            <th>Sobrenome</th>
            <th>Tipo de documento</th>
            <th>Número do documento</th>
            <th>Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($customers as $item) <tr>
            <td>{{ $item->customer_id }}</td>
            <td>{{ $item->first_name }}</td>
            <td>{{ $item->last_name }}</td>
            <td>{{ $item->document_type }}</td>
            <td>{{ $item->document_number }}</td>
            <td>{{ $item->status }}</td>
            <td>
                <div class="d-flex justify-content-between">
                    <a href="{{ url("/getnet/clients/{$item->customer_id}") }}" class="fandone-edit">
                        <i class="fa fa-edit"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection
