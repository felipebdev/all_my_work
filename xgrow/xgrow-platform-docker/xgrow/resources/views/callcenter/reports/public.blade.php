@extends('templates.xgrow.main')

@push('jquery')
    <script>
        $(function() {
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex){
                    const element = document.getElementById('ipt-contact-range');
                    if (!element) return true;
                    const period = element.value;
                    if (!period) return true;

                    const [start, end] = period.split('-');
                    // const parsedDate = parseDatatablesDate(aData[3]);
                    const parsedDate = aData[3].split(' ')[0].split('/').reverse().join('');
                    const parsedStart = parseDatatablesDate(start);
                    const parsedEnd = parseDatatablesDate(end);

                    if (parsedDate >= parsedStart && parsedDate <= parsedEnd) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
            );
        });

        function startAttendanceAgain(id) {
            const route = @json(route('callcenter.audience.start-attendances-again', ':id'));
            const url = route.replace(/:id/g, id);

            successToast('Reativando...', 'Estamos reativando os atendimentos');

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    successToast('Sucesso', response.data);
                    window.location.reload();
                },
                error: function(data) {
                    errorToast('Erro', `Houve um erro ao reativar o atendimento: ${data.responseJSON.message}`);
                }
            });
        }
    </script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">

    <style>
        .x-dropdown {
            position: unset !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>

    <script>
        $(function() {
            const dateRangeOptions = {
                autoUpdateInput: false,
                'locale': {
                    'format': 'DD/MM/YYYY',
                    'separator': ' - ',
                    'applyLabel': 'Aplicar',
                    'cancelLabel': 'Cancelar',
                    'daysOfWeek': [
                        'Dom',
                        'Seg',
                        'Ter',
                        'Qua',
                        'Qui',
                        'Sex',
                        'Sab',
                    ],
                    'monthNames': [
                        'Janeiro',
                        'Fevereiro',
                        'Março',
                        'Abril',
                        'Maio',
                        'Junho',
                        'Julho',
                        'Agosto',
                        'Setembro',
                        'Outubro',
                        'Novembro',
                        'Dezembro',
                    ],
                    'customRangeLabel': 'Personalizar'
                },
                ranges: {
                    'Hoje': [moment(), moment()],
                    'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Ultimos 7 dias': [moment().subtract(6, 'days'), moment()],
                    'Ultimos 30 dias': [moment().subtract(29, 'days'), moment()],
                    'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                    'Ultimo Mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                },
            };

            $(document).ready(function() {
                let ajax_url = "/callcenter/reports/public/data";

                let datatable;
                datatable = $('#attendants-table').DataTable({
                    dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                        '<"title-table d-flex align-self-center justify-content-center me-1">' +
                        '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                        '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                        '<"d-flex flex-wrap"<B><"create-button mb-2">>>>>' +
                        '<"filter-div mt-2"><"mt-2" rt>' +
                        '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                    ajax: ajax_url,
                    processing: true,
                    serverSide: false,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 itens por página', '25 itens por página', '50 itens por página',
                            'Todos os registros'
                        ]
                    ],
                    'columnDefs': [{
                        'visible': false,
                        'searchable': false,
                    }],
                    language: {
                        url: '{{ asset('js/datatable-translate-pt-BR.json') }}',
                    },
                    order: [
                        [2, 'asc'],
                    ],
                    columns: [
                        {
                            data: 'id',
                            visible: false
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'init_date',
                            render: function (data) {
                                return (data != null) ? formatter.toBrDatetime(data) : '-';
                            },
                        },
                        {
                            data: 'end_date',
                            render: function (data) {
                                return (data != null) ? formatter.toBrDatetime(data) : '-';
                            },
                        },
                        {
                            data: 'number_leads'
                        },
                        {
                            data: 'number_pending'
                        },
                        {
                            data: 'number_gain'
                        },
                        {
                            data: 'number_lost'
                        },
                        {
                            data: 'number_contactless'
                        },
                        {
                            data: 'number_attendants'
                        },
                        {
                            data: null,
                            searchable: false,
                            render: function(data, type, row) {
                                const menu = `
                                        <div class="dropdown x-dropdown">
                                            <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                                <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="startAttendanceAgain(${row.id})">Reativar atendimentos</a></li>
                                            </ul>
                                        </div>
                                    `;
                                return '<div class="d-flex flex-row-reverse">' + menu + '';
                            },
                        }
                    ],
                    buttons: [{
                            extend: 'pdf',
                            text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">\n' +
                                '<i class="fas fa-file-pdf" style="color: red"></i>\n' +
                                '</button>',
                            className: '',
                            exportOptions: {
                                columns: [':visible:not(.no-export)'],
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
                                columns: [':visible:not(.no-export)'],
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
                                columns: [':visible:not(.no-export)'],
                                modifier: {
                                    selected: true,
                                    page: 'all'
                                }
                            },
                        },
                    ],
                    initComplete: function(settings, json) {
                        $('.title-table').html(
                            `<h5 class="align-self-center"><span id="spn-total-label">${json.totalLabel}</span></h5>`
                        );
                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                        $('.dataTables_filter input').attr('placeholder', 'Buscar');
                        $('.create-label').html(
                            '<p class="xgrow-medium-bold me-2">Exportar em</p>');
                        $('.global-search').html(`
                                                        <div class="xgrow-input me-1 pt-0" style="background-color: var(--input-bg); height: 40px;" >
                                                            <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                                            <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                                                        </div>
                                                    `);
                    },
                    drawCallback: function(settings) {
                        const total = datatable.page.info().recordsDisplay || 0;
                    }
                });
            });
        });

        function formatStatus(status) {
            switch (status) {
                case 'pending':
                    return 'Pendente'
                case 'gain':
                    return 'Ganho'
                case 'lost':
                    return 'Perda'
                case 'contactless':
                    return 'Sem contato'
                case 'expired':
                    return 'Expirado'
                default:
                    return '--'
            }
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('callcenter.dashboard') }}">Call center</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Relatórios</a></li>
            <li class="breadcrumb-item active mx-2"><span>Públicos</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            {{-- @include('elements.alert') --}}
            <div class="table-responsive m-t-30">
                <table id="attendants-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>ID</th>
                            <th>Público</th>
                            <th>Data Início</th>
                            <th>Data Encerramento</th>
                            <th>Leads</th>
                            <th>Pendentes</th>
                            <th>Ganhos</th>
                            <th>Perdidos</th>
                            <th>Sem Contato</th>
                            <th>Atendentes</th>
                            <th class="no-export"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('elements.toast')
@endsection
