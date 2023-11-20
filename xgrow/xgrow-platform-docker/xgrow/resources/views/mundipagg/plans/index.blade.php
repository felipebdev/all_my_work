@extends('templates.monster.main')

@section('content')
    @if (session()->has('message'))
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
                <li class="breadcrumb-item"><a href="/plans">Planos</a></li>
                <li class="breadcrumb-item active">Getnet</li>
            </ol>
        </div>
    </div>

    <table id="plan-table" class="table fandone-table ">
        <thead class="default-background text-white">
            <tr>
                <th>Plan Id (Getnet)</th>
                <th>Valor</th>
                <th>Data de criação</th>
                <th>Moeda</th>
                <th>Nome</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $item)
                <tr>
                    <td>{{ $item->plan_id }}</td>
                    <td>{{ number_format(substr($item->amount, 0, -2) . '.' . substr($item->amount, -2), 2, ',', '.') }}
                    </td>
                    <td>{{ $item->create_date }}</td>
                    <td>{{ $item->currency }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <div class="d-flex justify-content-between">
                            <a href="{{ url("/getnet/plans/{$item->plan_id}") }}" class="fandone-edit">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form method="POST"
                                onsubmit="return confirm(`Deseja alterar o status do plano {{ addslashes($item->name) }}?`)"
                                action="{{ url("/getnet/plans/status/{$item->plan_id}/{$item->status}") }}">
                                {{ csrf_field() }}
                                {{ method_field('POST') }}
                                <button type="submit" class="fandone-delete" title="Alterar status" alt="Alterar status">
                                    <i class="fa fa-share" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
