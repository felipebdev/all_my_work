@extends('templates.monster.main')


@section('jquery')

@endsection

@push('before-styles')

    <link rel="stylesheet" type="text/css"
          href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

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
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <!-- end - This is for export functionality only -->
    <script>
        $(function() {
            $(document).ready(function() {
                $('#payments-table').DataTable({
                    dom: '<"fandone-bar"<"fandone-bar-left"l><"fandone-bar-right"Bf>>rtip',
                    aoColumnDefs: [
                        { "bSortable": false, "aTargets": [ 4 ] },
                        { "bSearchable": false, "aTargets": [ 4 ] },
                    ],
                    scrollX: false,
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('datatables.payments') !!}',
                    columns: [
                        { data: 'name', name: 'subscribers.name' },
                        { data: 'name_integration', name: 'integrations.name_integration' },
                        { data: 'status', name: 'getnet_charges.status' },
                        { data: 'price', name: 'plans.price', render: $.fn.dataTable.render.number( '.', ',', 2, 'R$', '' ) },
                        {
                            data: 'payment_date',
                            name: 'getnet_charges.payment_data',
                            render:function ( data, type, row, meta ) {
                                return (data == null) ? '' : moment(data).format('DD/MM/YY');
                            }
                        },
                        {
                            title: 'Alterar',
                            data: null,
                            bSearchable: false,
                            createdCell: function(td, cellData, rowData, row, col){
                                let href    = `/getnet/subscriptions/${cellData.integration_type_id}`;
                                let buttons = ` <a href="${href}" class="fandone-edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        `
                                $(td).html(buttons)
                            }
                        }

                    ],
                    buttons: [
                        {
                            extend: 'print', text: '<img class="fandone-bar-img" src="/images/icon_print.png">',
                            className: ''
                        },
                        {
                            extend: 'pdf', text: '<img class="fandone-bar-img" src="/images/icon_pdf.png">',
                            className: ''
                        },
                        {
                            extend: 'csv', text: '<img class="fandone-bar-img" src="/images/icon_csv.png">',
                            className: ''
                        },
                        {
                            extend: 'excel', text: '<img class="fandone-bar-img" src="/images/icon_xls.png">',
                            className: ''
                        },
                    ],
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",
                        "decimal": ",",
                        "thousands": "."
                    },
                    initComplete: function (settings, json) {
                        $(".buttons-csv").removeClass("dt-button buttons-csv");
                        $(".buttons-excel").removeClass("dt-button buttons-excel");
                        $(".buttons-pdf").removeClass("dt-button buttons-pdf");
                        $(".buttons-print").removeClass("dt-button buttons-print");
                    }
                });
            });
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

        // <button type="button" onclick="reverseSubscription(${row}, ${cellData.id},'${cellData.name}')" class="fandone-delete">
        //     <i class="fa fa-trash"></i>
        //     </button>
        // function reverseSubscription(row, subscriberId, subscriberName)
        // {
        //     if (!confirm(`Confirma o estorno desse pagamento?`)) {
        //         return false
        //     }

            {{--$.ajax({--}}
            {{--    type: 'POST',--}}
            {{--    url: "{{URL::route('subscribers.destroy')}}",--}}
            {{--    dataType: 'json',--}}
            {{--    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },--}}
            {{--    data: {--}}
            {{--        'id': subscriberId,--}}
            {{--        '_token': "{{ csrf_token() }}"--}}
            {{--    },--}}
            {{--    success: function (data) {--}}
            {{--        let oTable = $('#plan-table').DataTable();--}}
            {{--        oTable.row( $(this).parents('tr') ).remove().draw();--}}
            {{--        alert("Registro excluído com sucesso!")--}}
            {{--    },--}}
            {{--    error: function (data) {--}}
            {{--        alert("Houve um erro na exclusão do registro: " + data.responseJSON.message)--}}
            {{--    }--}}
            {{--});--}}
        // }

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
            <h3 class="text-themecolor mb-0 mt-0">Integrações</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Integrações</li>
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

                <table id="payments-table" class="table fandone-table">
                    <thead class="default-background text-white">
                    <tr>
                        <th>Assinante</th>
                        <th>Gateway</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Data da cobrança</th>
                        <th width="5%"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


@endsection
