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
                    'Limpar': [null, null],
                },
            };

            $(document).ready(function() {
                let current_url = "{{ Route::current()->getName() }}";
                let ajax_url = "/callcenter/reports/get-list";

                if (current_url === 'callcenter.reports.attendant') {
                    ajax_url += '/{{ request()->route("id") }}'
                }

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
                        [3, 'DESC'],
                    ],
                    columns: [
                        {
                            data: 'attendance.attendant.name'
                        },
                        {
                            data: 'attendance.subscriber',
                            render: function (data) {
                                return  data.name + '<br />' + data.email;
                            }
                        },
                        {
                            data: 'attendance.audience.name'
                        },
                        {
                            data: 'created_at',
                            render: function (data) {
                                return (data != null) ? formatter.toBrDatetime(data) : '-';
                            },
                        },
                        {
                            data: 'attendance.status',
                            render: function (data) {
                                return formatStatus(data);
                            }
                        },
                        {
                            data: 'reasons_loss',
                            render: function (data) {
                                return data != null ? data.description : '--';
                            }
                        },
                        {
                            data: 'ip'
                        },
                        {
                            data: null,
                            searchable: false,
                            render: function(data, type, row) {
                                const route = @json(route('callcenter.reports.show', ':id'));
                                const url = route.replace(/:id/g, row.attendance_id);
                                const menu = `
                                        <div class="dropdown x-dropdown">
                                            <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                                <li><a class="dropdown-item table-menu-item" href="${url}">Ver</a></li>
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
                            `<h5 class="align-self-center"><span id="spn-total-label"></span></h5>`
                        );
                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                        $('.dataTables_filter input').attr('placeholder', 'Buscar');
                        $('.create-label').html(
                            '<p class="xgrow-medium-bold me-2">Exportar em</p>');
                        $('.filter-button').html(`
                                                        <div class="d-flex align-items-center py-2">
                                                            <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-bs-expanded="false" aria-bs-controls="collapseExample" class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                                                <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                                            </button>
                                                        </div>
                                                    `);
                        $('.global-search').html(`
                                                        <div class="xgrow-input me-1 pt-0" style="background-color: var(--input-bg); height: 40px;" >
                                                            <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                                            <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                                                        </div>
                                                    `);
                        $('.filter-div').html(`
                                                    <div class="mb-3 collapse" id="collapseExample">
                                                        <div class="filter-container">
                                                            <div class="p-2 px-3">
                                                                <div class="row">
                                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-attendant-filter" class="xgrow-select w-100" multiple>
                                                                                @foreach ($attendants_filter as $attendant)
                                                                                    <option value="{{ $attendant->name }}">{{ $attendant->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-audience-filter" class="xgrow-select w-100" multiple>
                                                                                @foreach ($audiences_filter as $audience)
                                                                                    <option value="{{ $audience->name }}">{{ $audience->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-status-filter" class="xgrow-select w-100" multiple>
                                                                                @foreach ($status_filter as $status)
                                                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                                                            <input type="text" class="form-control" id="ipt-contact-range"
                                                                                style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
                                                                                autocomplete="off">
                                                                            <label for="ipt-contact-range">Data de contato</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`);

                        $('#slc-audience-filter').select2({
                            allowClear: true,
                            placeholder: 'Público'
                        });

                        $('#slc-attendant-filter').select2({
                            allowClear: true,
                            placeholder: 'Atendente'
                        });

                        $('#slc-status-filter').select2({
                            allowClear: true,
                            placeholder: 'Status'
                        });

                        $('#slc-audience-filter').on('change', function() {
                            const selected = $(this).val();
                            console.log($(this).val());
                            const filter = selected.join('|');
                            datatable.columns(2).search(filter, true, false).draw();
                        });

                        $('#slc-attendant-filter').on('change', function() {
                            const selected = $(this).val();
                            console.log($(this).val());
                            const filter = selected.join('|');
                            datatable.columns(0).search(filter, true, false).draw();
                        });

                        $('#slc-status-filter').on('change', function() {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(4).search(filter, true, false).draw();
                        });

                        $('#ipt-global-filter').on('keyup', function () {
                            datatable.search(this.value).draw();
                        });

                        $('#ipt-contact-range').daterangepicker(dateRangeOptions)
                            .on('apply.daterangepicker', function(ev, picker) {
                                if (!picker.startDate.isValid() && !picker.endDate.isValid()) {
                                    return $(this).trigger('cancel.daterangepicker');
                                }
                                $(this).val(picker.startDate.format('DD/MM/YYYY') + '-' + picker.endDate
                                    .format('DD/MM/YYYY'));
                                $(this).removeClass('mui--is-empty');
                                $(this).addClass('mui--is-not-empty');

                                datatable.columns(3).search('').draw();
                            })
                            .on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                                datatable.columns(3).search('').draw();
                            });

                        setTotalLabel(datatable.page.info().recordsDisplay);
                    },
                    drawCallback: function(settings) {
                        const total = datatable.page.info().recordsDisplay || 0;
                        setTotalLabel(total);
                    }
                });
            });
        });

        function setTotalLabel(total = 0) {
            let label = 'relatório';
            if (total !== 1) label += 's';
            $('#spn-total-label').text(`${total} ${label}`);
        }

        function formatStatus(status) {
            switch (status) {
                case 'pending':
                    return 'Pendente'
                case 'gain':
                    return 'Ganho'
                case 'lost':
                    return 'Perdido'
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
            <li class="breadcrumb-item active mx-2"><span>Atendentes</span></li>
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
                            <th>Atendente</th>
                            <th>Cliente</th>
                            <th>Público</th>
                            <th>Data de Contato</th>
                            <th>Status</th>
                            <th>Motivo Perda</th>
                            <th>IP Atendente</th>
                            <th class="no-export"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
