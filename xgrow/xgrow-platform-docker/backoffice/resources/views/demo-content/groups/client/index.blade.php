@extends('templates.horizontal.main')

@push('before-styles')
<link rel="stylesheet" type="text/css"
    href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
@endpush

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')
<script>
    $(function() {
    $('#client-table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        language: {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
        }
    });

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
});

</script>
@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Clientes</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Clientes</li>
        </ol>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <a href="{{ url('/client/create') }}" class="btn btn-rounded btn-outline-primary">
                <i class="fa fa-star"></i> Novo cliente
            </a>
        </div>
        <div class="table-responsive m-t-30">
            <table id="client-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Primeiro nome</th>
                    <th>Último nome</th>
                    <th>E-mail</th>
                    <th>Nome empresa</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                     @forelse ($clients as $client)
                    <tr>
                        <td>{{ $client->first_name }}</td>
                        <td>{{ $client->last_name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->company_name }}</td>
                        <td>
                            <div class="d-flex justify-content-between">
                                <a href="{{ url('/client/edit/' . $client->id) }}" class="btn btn-rounded btn-outline-primary">
                                    <i class="fa fa-edit"></i> Editar
                                </a>
                                <a href="{{ url('/client/destroy/' . $client->id) }}" class="btn btn-rounded btn-outline-danger">
                                    <i class="fa fa-trash"></i> Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Não há plataformas cadastradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
