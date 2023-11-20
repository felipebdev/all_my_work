@extends('templates.horizontal.main')

@section('jquery')

@endsection

@push('before-styles')

<link rel="stylesheet" type="text/css"
    href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}">

@endpush

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')

<!-- This is data table -->
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/datatables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<!-- end - This is for export functionality only -->
<script>
    $(function() {
        $(document).ready(function() {
            $('#platform-table').DataTable({
                dom: 'l<br>Bfrtip',
                "aaSorting": [],
                "aoColumnDefs": [],
                buttons: [
                    { extend: 'copy', text: 'Copiar' },
                    'csv', 'excel', 'pdf',
                    { extend: 'print', text: 'Imprimir' },
                ],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
            });
        });
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

</script>



@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Plataformas</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Plataformas</li>
        </ol>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <a href="{{ url('/platforms/create') }}" class="btn btn-rounded btn-outline-primary">
                <i class="fa fa-star"></i> Nova plataforma
            </a>
        </div>
        <div class="table-responsive m-t-30">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <table id="platform-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Atualização</th>
                        <th>Criação</th>
                        <th>Cliente</th>
                        <th>Plataforma</th>
                        <th>Url</th>
                        <th>Modelo</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($platforms as $platform)
                    <tr>
                        <td>{{ date('d/m/Y H:i', strtotime($platform->updated_at)) }}</td>
                        <td>{{ $platform->created_at }}</td>
                                                                        <td>{{ $platform->customer_name}}</td>
                                                                        <td>{{ $platform->name }}</td>
                                                                        <td><a href="{{ $platform->url }}" target="_blank">{{ $platform->url }}</a></td>
                        <td>{{ $platform->template_schema == 1 ? 'jQuery': 'Vue.js'}}</td>
                        <td>

                            <form method="POST"
                                    onsubmit="return confirm('Deseja atualizar a plataforma: {{addslashes($platform->name)}} ?')"
                                    action='{{url("/platforms/{$platform->id}/renew")}}'>
                                    {{ csrf_field() }}
                                    {{ method_field('GET') }}
                                    <button type="submit" class="btn btn-rounded btn-outline-danger">
                                        <i class="fa fa-recycle"></i> Atualizar
                                    </button>
                                </form>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between">
                                <a href="{{ url("/platforms/{$platform->id}/edit") }}"
                                    class="btn btn-rounded btn-outline-primary">
                                    <i class="fa fa-edit"></i> Editar
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
