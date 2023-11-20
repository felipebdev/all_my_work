@extends('templates.monster.main')


@section('jquery')

@endsection

@push('before-styles')

<link rel="stylesheet" type="text/css" href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

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
            $('#integracao-table').DataTable({
                //dom: 'lBfrtip',
                dom: '<"fandone-bar"<"fandone-bar-left"l><"fandone-bar-right"Bf>>rtip'
                , "aoColumnDefs": [{
                        "bSortable": false
                        , "aTargets": [7]
                    }
                    , {
                        "bSearchable": false
                        , "aTargets": [7]
                    }
                ]
                , scrollX: false
                , buttons: [{
                        extend: 'print'
                        , text: '<img class="fandone-bar-img" src="/images/icon_print.png">'
                        , className: ''
                    }
                    , {
                        extend: 'pdf'
                        , text: '<img class="fandone-bar-img" src="/images/icon_pdf.png">'
                        , className: ''
                    }
                    , {
                        extend: 'csv'
                        , text: '<img class="fandone-bar-img" src="/images/icon_csv.png">'
                        , className: ''
                    }
                    , {
                        extend: 'excel'
                        , text: '<img class="fandone-bar-img" src="/images/icon_xls.png">'
                        , className: ''
                    }
                , ]
                , language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
                , initComplete: function(settings, json) {
                    $(".buttons-csv").removeClass("dt-button buttons-csv");
                    $(".buttons-excel").removeClass("dt-button buttons-excel");
                    $(".buttons-pdf").removeClass("dt-button buttons-pdf");
                    $(".buttons-print").removeClass("dt-button buttons-print");
                }
            });
        });
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

    function changeStatus(id) {
        $.ajax({
            url: `/integracao/${id}/status`
            , type: 'PUT'
            , data: {
                "_token": "{{ csrf_token() }}"
            }
            , success: function(data) {
                console.log('success')
                console.log(data)
                console.log({
                    {
                        csrf_token()
                    }
                })
            }
            , error: function(data) {
                console.log('error')
                console.log(data)
                console.log({
                    {
                        csrf_token()
                    }
                })
            }
        });
    }

</script>

@endpush

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
        <h3 class="mb-0 mt-0"><i class="mdi mdi-account"></i> Logs das Integrações</h3>
        <ol class="breadcrumb fandone-bc ">
            <li class="fandone-bc-item"><a href="/">Home</a></li>
            <li>
                <div class="arrow"></div>
            </li>
            <li>Logs das Integrações</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive m-t-30">
            @if ($errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <ul class="nav nav-tabs customtab" role="tablist">

                <li class="nav-item"> <a href="{{ route('integracao-logs.index') }}" class="nav-link {{ in_array(Route::current()->getName(), ['integracao-logs.index'])   ? 'active' : ''}} "><span class="hidden-sm-up"><i class="fa fa-home"></i></span> <span class="hidden-xs-down">Logs</span></a></li>
                <li class="nav-item"><a href="{{ route('integracao-logs.errors') }}" class="nav-link {{ in_array(Route::current()->getName(), ['integracao-logs.errors'])   ? 'active' : ''}} "><span class="hidden-sm-up"><i class="fa fa-clone"></i></span> <span class="hidden-xs-down">Logs Com Erro</span></a></li>

            </ul>
            <table id="integracao-table" class="table">
                <thead style="background-color:#062d58;color:white;">
                    <tr>
                        <th>ID</th>
                        <th>Rota</th>
                        <th>Metodo</th>
                        <th>Ação</th>
                        <th>Gatilho</th>
                        <th>Data</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($integracaologs as $integracaolog)
                    <tr>
                        <td>{{$integracaolog->_id}}</td>
                        <td>{{$integracaolog->route}}</td>
                        <td>{{$integracaolog->method}}</td>
                        <td>{{$integracaolog->action_name}}</td>
                        <td>{{$integracaolog->trigger}}</td>
                        <td>{{ date("d.m.y - H:i:s", strtotime($integracaolog->createdAt)) }}</td>
                        <td>
                            @if(in_array(Route::current()->getName(), ['integracao-logs.index']))
                            <div class="d-flex justify-content-between">
                                <a href="{{route('integracao-logs.details',['id'=>$integracaolog->_id])}}">
                                    <i class="fa fa-eye" style="color:purple;"></i>
                                </a>
                            </div>
                            @else
                            <div class="d-flex justify-content-between">
                                <a href="{{route('integracao-logs.detailsError',['id'=>$integracaolog->_id])}}">
                                    <i class="fa fa-eye" style="color:purple;"></i>
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Não há webhooks cadastrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
