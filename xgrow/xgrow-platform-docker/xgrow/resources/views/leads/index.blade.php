@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>

    <script>
        $(function() {
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex){
                    const element = document.getElementById('ipt-created-range');
                    if (!element) return true;
                    const period = element.value;
                    if (!period) return true;

                    const [start, end] = period.split('-');
                    const parsedDate = parseDatatablesDate(aData[5]);
                    const parsedStart = parseDatatablesDate(start);
                    const parsedEnd = parseDatatablesDate(end);
                    return parsedDate >= parsedStart && parsedDate <= parsedEnd;
                }
            );
        });
    </script>
@endpush

@push('after-styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
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

            function invokeDelete(id, additionalDescription = '')
            {
                const deleteRoute = @json(route('subscribers.destroy', ':id'));
                const deleteUrl = deleteRoute.replace(/:id/g, id);

                let desc = additionalDescription ? (': ' + additionalDescription) : ''
                const modalOptions = {
                    title: 'Excluir lead',
                    description: 'Você tem certeza que deseja excluir o lead' + desc + '?',
                    btnSave: 'Sim, excluir',
                    btnCancel: 'Não, manter',
                    success: 'Lead excluído com sucesso',
                    error: 'Não foi possível excluir o lead: ',
                    url: deleteUrl,
                    method: 'DELETE',
                    body: {
                        'id': id,
                        '_token': "{{ csrf_token() }}",
                    },
                    datatables: '#subscriber-table'
                }

                openConfirmationModal(window.btoa(JSON.stringify(modalOptions)))
            }

            let datatable;
            datatable = $('#subscriber-table').DataTable({
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                    '<"d-flex flex-wrap"<B>>>>>' +
                    '<"filter-div mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                ajax: {
                    url: '{{ route('get.leads') }}',
                    data: function (d) {
                        d.searchTerm = $('#ipt-global-filter').val();
                        d.plansFilter = $('#slc-plan-filter option:selected').map(function () {
                            return this.value;
                        }).get();
                        d.createdPeriodFilter = $('#ipt-created-range').val();
                        d.onlyFailedTransactions = $('#swt-failed-transactions').is(':checked');
                    }
                },

                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página',
                        "Todos os registros"
                    ]
                ],
                "order": [],
                language: {
                    url: '{{ asset('js/datatable-translate-pt-BR.json') }}',
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'email',
                        render: function(data, type, row, meta) {
                            return data;
                        },
                    },
                    {
                        data: 'cel_phone',
                        render: function(data, type, row) {
                            return (data) ? data : row.main_phone || '';
                        },
                    },
                    {
                        data: 'created',
                        name: 'leads.created_at',
                        render: function(data, type, row, meta) {
                            return (data != null) ? moment(data).format('DD/MM/YYYY HH:mm:ss') : '';
                        },
                    },
                    {
                        data: 'products_name',
                        name: 'products.name',
                        render: function (data) {
                            return data ? data.split(',').join(',<br>') : '';
                        },
                    },
                    {
                        data: 'product_id',
                        orderable: false,
                        searchable: false,
                        visible: false
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            const route = @json(route('subscribers.edit', ':id'));
                            const url = route.replace(/:id/g, row.id);

                            const menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                        <li><a class="dropdown-item table-menu-item" href="javascript:showDetailModal(${row.id})">Detalhes</a></li>
                                    </ul>
                                </div>
                            `;
                            return '<div class="d-flex">' + menu + '';
                        },
                    },
                ],
                buttons: [
                    {
                        extend: 'csv',
                        text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">\n' +
                            '                  <i class="fas fa-file-csv" style="color: blue"></i>\n' +
                            '                </button>',
                        className: '',
                        action: function (e, dt, node, config) {
                            successToast('Iniciando download!', 'Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral.');
                            axios.post("{{route('report.download.lead')}}", {
                                searchTerm: $('#ipt-global-filter').val(),
                                plansFilter: $('#slc-plan-filter option:selected').map(function () {
                                    return this.value;
                                }).get(),
                                createdPeriodFilter: $('#ipt-created-range').val(),
                                onlyFailedTransactions: $('#swt-failed-transactions').is(':checked'),
                                typeFile: 'csv',
                                reportName: 'leads',
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">\n' +
                            '                  <i class="fas fa-file-excel" style="color: green"></i>\n' +
                            '                </button>',
                        className: '',
                        action: function (e, dt, node, config) {
                            successToast('Iniciando download!', 'Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral.');
                            axios.post("{{route('report.download.lead')}}", {
                                searchTerm: $('#ipt-global-filter').val(),
                                plansFilter: $('#slc-plan-filter option:selected').map(function () {
                                    return this.value;
                                }).get(),
                                createdPeriodFilter: $('#ipt-created-range').val(),
                                onlyFailedTransactions: $('#swt-failed-transactions').is(':checked'),
                                typeFile: 'xlsx',
                                reportName: 'leads',
                            });
                        }
                    },
                ],
                initComplete: function(settings, json) {
                    $('.title-table').html(
                        '<h5 class="align-self-center">Leads: <span id="spn-total-label"></span></h5>'
                        );
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
                            <div class="xgrow-input me-1 pt-1" style="background-color: var(--input-bg); height: 40px;" >
                                <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                            </div>
                        `);
                    $('.filter-div').html(`
                            <div class="mb-3 collapse" id="collapseExample">
                                <div class="filter-container">
                                    <div class="p-2 px-3">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6">
                                                <div class="xgrow-form-control mb-2">
                                                    <select id="slc-plan-filter" class="xgrow-select w-100" name="plan-filter[]" multiple>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                                                    <input type="text" class="form-control" id="ipt-created-range"
                                                        style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; min-width: 230px; color: var(--contrast-green)"
                                                        autocomplete="off">
                                                    <label for="ipt-created-range">Data de cadastro</label>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch ms-2">
                                                <input id="swt-failed-transactions" class="form-check-input" type="checkbox" value="true"/>
                                                <label for="not-acess">Mostrar apenas leads que possuem transações negadas</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`);

                    $('.xgrow-datepicker').datepicker({
                        format: 'dd/mm/yyyy',
                    });
                    $('#slc-plan-filter').select2({
                        allowClear: true,
                        placeholder: 'Produto'
                    });

                    $('#ipt-global-filter').on('keyup', function() {
                        datatable.search(this.value).draw();
                    });

                    $('#slc-plan-filter').on('change', function() {
                        const selected = $('#slc-plan-filter').val();
                        const filter = selected.join('|');
                        datatable.columns(5).search(filter, true, false).draw();
                    });

                    $('#swt-failed-transactions').on('change', function () {
                        datatable.search('').draw();
                    });

                    axios.get("{{ URL::route('products.list') }}").then(response => {
                        let html = ""
                        response.data.products.sort(function (a, b) {
                            if (a.name > b.name) return 1;
                            if (a.name < b.name) return -1;
                            return 0;
                        });
                        response.data.products.forEach(item => html += `<option value="${item.id}">${item.name}</option>`)
                        $('#slc-plan-filter').append(html)
                    })

                    $('#ipt-created-range').daterangepicker(dateRangeOptions)
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
                        })
                        .on("blur", function () {
                            datatable.columns(3).search('').draw();
                        });

                    datatable.columns(3).search('').draw();
                },
                drawCallback: function(settings) {
                    const total = datatable.page.info().recordsDisplay || 0;
                    setTotalLabel(total);
                }
            });
        });

        function parseDateValue(rawDate) {
            var dateArray= rawDate.split("/");
            var parsedDate= dateArray[2] + dateArray[0] + dateArray[1];
            return parsedDate;
        }

        function setTotalLabel(total = 0) {
            let label = 'lead';
            if (total > 0) label = 'leads';
            $('#spn-total-label').text(`${total} ${label}`);
        }

    </script>
@endpush

@section('content')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/subscribers">Alunos</a></li>
            <li class="breadcrumb-item active mx-2"><span>Leads</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body px-3 py-4">
            @include('elements.alert')
            <div class="table-responsive m-t-30">
                <table id="subscriber-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Cadastro</th>
                            <th>Produto</th>
                            <th>Pid</th>
                            <th class="no-export"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('leads.modal.detail-modal')
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
