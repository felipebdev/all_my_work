@extends('templates.xgrow.main')

@php
$total_label = count($sales);

if ($total_label == 0) {
    $total_label = 'Nenhuma venda';
} elseif ($total_label == 1) {
    $total_label = '1 venda';
} else {
    $total_label = "$total_label vendas";
}
@endphp

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        let datatable;
        datatable = $('#plan-table').DataTable({
            dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                '<"title-table d-flex align-self-center justify-content-center me-1">' +
                '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                '<"d-flex flex-wrap"<B><"create-button mb-2">>>>>' +
                '<"filter-div mt-2"><"mt-2" rt>',
            lengthMenu: [
                [10, 25, 50, -1],
                ['10 itens por página', '25 itens por página', '50 itens por página', 'Todos os registros']
            ],
            language: {
                'url': "{{ asset('js/datatable-translate-pt-BR.json') }}",
            },
            buttons: [{
                    extend: 'pdf',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">\n' +
                        '                  <i class="fas fa-file-pdf" style="color: red"></i>\n' +
                        '                </button>',
                    className: '',
                    exportOptions: {
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                },
                {
                    extend: 'csv',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">\n' +
                        '                  <i class="fas fa-file-csv" style="color: blue"></i>\n' +
                        '                </button>',
                    className: '',
                    exportOptions: {
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                },
                {
                    extend: 'excel',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">\n' +
                        '                  <i class="fas fa-file-excel" style="color: green"></i>\n' +
                        '                </button>',
                    className: '',
                    exportOptions: {
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                },
            ],
            initComplete: function(settings, json) {
                $('.title-table').html(
                    '<h5 class="align-self-center">Vendas: {{ $total_label }}</h5>');
                $('.buttons-csv').removeClass('dt-button buttons-csv');
                $('.buttons-excel').removeClass('dt-button buttons-excel');
                $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                $('.dataTables_filter input').attr('placeholder', 'Buscar');
                $('.filter-button').html(`
                                                    <div class="d-flex align-items-center py-2">
                                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-bs-expanded="false" aria-bs-controls="collapseExample" class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                                        <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                                        </button>
                                                    </div>
                                                `);
                $('.global-search').html(`
                                            <div class="xgrow-input me-1" style="background-color: var(--input-bg); height: 40px;" >
                                                <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                                <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                                            </div>
                                            `);
                $('.filter-div').html(`
                                    <div class="mb-3 collapse" id="collapseExample">
                                        <div class="filter-container">
                                            <div class="p-2 px-3">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 mt-1">
                                                        <div class="xgrow-form-control mui-textfield mui-textfield--float-label">
                                                            <select class="xgrow-select" id="slc-course-filter">
                                                                <option value="" selected hidden></option>
                                                                @foreach ($courses as $course)
                                                                    <option value="{{ $course->name }}">{{ $course->name }}</option>
                                                                @endforeach
                                                                <option value="">Todos</option>
                                                            </select>
                                                            <label>Curso</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-3 mt-1">
                                                        <div class="xgrow-form-control mui-textfield mui-textfield--float-label">
                                                            <select class="xgrow-select" id="slc-status-filter">
                                                                <option value="" selected hidden></option>
                                                                <option value="APPROVED">APPROVED</option>
                                                                <option value="DENIED">DENIED</option>
                                                                <option value="CANCELED">CANCELED</option>
                                                            </select>
                                                            <label>Status</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-3 mt-1">
                                                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                                                            <input type="text" class="custom-datepicker xgrow-datepicker" id="ipt-received-filter" data-provide="datepicker">
                                                            <label>Data recebido</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="xgrow-card-footer border-top p-3">
                                                <button class="xgrow-button" id="btnApplyFilter" style="height: 36px">Aplicar filtros</button>
                                            </div> -->
                                        </div>
                                    </div>`);

                $('.xgrow-datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                });

                $('#ipt-global-filter').on('blur', function() {
                    if (this.value.length === 0 || this.value.length >= 3) {
                        datatable.search(this.value).draw();
                    }
                });

                $('#slc-course-filter').on('change', function() {
                    datatable.columns(4).search(this.value).draw();
                });

                $('#slc-status-filter').on('change', function() {
                    datatable.columns(2).search(this.value).draw();
                });

                $('#ipt-received-filter').on('change', function() {
                    datatable.columns(5).search(this.value).draw();
                });
            },
        });

        function cancelPayment(paymentId) {
            if (!confirm(`Deseja fazer o cancelamento do pagamento ${paymentId}?`)) {
                return false
            }

            const url = '{{ route('getnet.sales.cancel-payment', ':payment_id') }}';
            const _url = url.replace(':payment_id', paymentId);

            $.ajax({
                type: 'GET',
                url: _url,
                dataType: 'json',
                success: function(data) {
                    console.log(data)
                    if (data.status === 'success') {
                        successToast("Cancelamento feito!", `${data.data.message}`);
                    }

                    if (data.status === 'error') {
                        errorToast("Algum erro aconteceu!", `Veja mais em: ${data.data.message}`);
                    }
                },
                error: function(data) {
                    errorToast("Algum erro aconteceu!", "Houve um erro no cancelamento do pagamento");
                }
            });
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/integracao">Integrações</a></li>
            <li class="breadcrumb-item active mx-2"><span>Vendas</span></li>
        </ol>
    </nav>

    <div class="wide-view xgrow-card card-dark p-0">
        <div class="xgrow-card-body px-3 py-4">
            <table id="plan-table" class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer">
                @if ($errors->any())
                    <div class="alert alert-warning">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <thead>
                    <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                        <th scope="col">Payment ID</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Status</th>
                        <th scope="col">Assinante</th>
                        <th scope="col">Curso</th>
                        <th scope="col">Data recebido</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ------------------------------------------------------------------------------------------------------ -->
                    @forelse ($sales as $item)
                        <tr>
                            <td>
                                <p>{{ $item->payment_id }}</p>
                            </td>
                            <td>
                                <p>R$ {{ number_format($item->amount, 2, ',', '.') }}</p>
                            </td>
                            <td>
                                <p>{{ $item->status }}</p>
                            </td>
                            <td>
                                <p>{{ $item->subscribers_name }}</p>
                            </td>
                            <td>
                                <p>{{ $item->course_name }}</p>
                            </td>
                            <td>
                                <p>
                                    @if ($item->received_at != null)
                                        {{ date('d/m/Y', strtotime($item->received_at)) }}@else @endif
                                </p>
                            </td>
                            <td>
                                <div class="d-flex justify-content-around">
                                    <button class="xgrow-button table-action-button"
                                        onclick="cancelPayment('{{ $item->payment_id }}')">
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
                    <!-- ------------------------------------------------------------------------------------------------------ -->
                </tbody>
            </table>
        </div>
    </div>
    @include('elements.toast')
@endsection
