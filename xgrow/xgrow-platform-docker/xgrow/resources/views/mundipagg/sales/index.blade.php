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
    <!-- end - This is for export functionality only -->
    <script>
        $(function() {
            $(document).ready(function() {
                $('#plan-table').DataTable({
                    //dom: 'lBfrtip',
                    dom: '<"fandone-bar"<"fandone-bar-left"f><B><"fandone-bar-right"l>>rt<"fandone-footer"ip>',
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ 0, 1 ] },
                        { "bSearchable": false, "aTargets": [ 0, 1 ] }
                    ],
                    scrollX: false,
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
                        "url": "{{ asset("js/datatable-translate-pt-BR.json")}}"
                    },
                    initComplete: function (settings, json) {
                        $(".buttons-csv").removeClass("dt-button buttons-csv");
                        $(".buttons-excel").removeClass("dt-button buttons-excel");
                        $(".buttons-pdf").removeClass("dt-button buttons-pdf");
                        $(".buttons-print").removeClass("dt-button buttons-print");
                        $("div.create-button").html('<a href="{{URL::route('plans.create')}}" class="btn btn-themecolor"><i class="mdi mdi-plus"></i> Novo </a>');
                        $('.dataTables_filter input').attr("placeholder", "Pesquisar");
                    }
                });
            });
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

        function cancelPayment(paymentId)
        {
            if (!confirm(`Confirma o cancelamento do pagamento ${paymentId}?`)) {
                return false
            }

            const url = '{{route("getnet.sales.cancel-payment", ":payment_id")}}';
            const _url = url.replace(':payment_id', paymentId);

            $.ajax({
                type: 'GET',
                url: _url,
                dataType: 'json',
                success: function (data) {
                    console.log(data)
                    if (data.status === 'success') {
                        toastr["success"](data.data.message);
                    }

                    if (data.status === 'error') {
                        toastr["error"](data.data.message);
                    }
                },
                error: function (data) {
                    toastr["error"]("Houve um erro no cancelamento do pagamento");
                }
            });
        }

    </script>

@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="mb-0 mt-0"><i class="mdi mdi-account"></i> Vendas</h3>
            <ol class="breadcrumb fandone-bc ">
                <li class="fandone-bc-item"><a href="/">Configurações</a></li>
                <li><div class="arrow"></div></li>
                <li class="fandone-bc-item"><a href="/">Integrações</a></li>
                <li><div class="arrow"></div></li>
                <li>Vendas</li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="fandone-cardbody-header">

            </div>
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
                <table id="plan-table" class="table fandone-table">
                    <thead class="default-background text-white">
                    <tr>
                        <th>Payment Id</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Assinante</th>
                        <th>Curso</th>
                        <th>Data recebido</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($sales as $item) <tr>
                        <td>{{ $item->payment_id }}</td>
                        <td>R$ {{ number_format($item->amount, 2, ',', '.') }} </td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->subscribers_name }}</td>
                        <td>{{ $item->course_name }}</td>
                        <td>@if($item->received_at!=null){{date('d/m/Y',strtotime($item->received_at))}}@else @endif</td>
                        <td>
                            <div class="d-flex justify-content-between">
                                <button type="button" onclick="cancelPayment('{{$item->payment_id}}')" class="fandone-delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Não há pagamentos</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
