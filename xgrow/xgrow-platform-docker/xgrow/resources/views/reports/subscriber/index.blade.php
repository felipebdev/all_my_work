@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let btnTypeX, eX, dtX, nodeX, configX;
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
        const customTable = $('#subscriber-table').DataTable({
            dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                '<"title-table d-flex align-self-center justify-content-center me-1">' +
                '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                '<"d-flex flex-wrap"<B>>>>>' +
                '<"filter-div mt-2"><"mt-2" rt>' +
                '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
            lengthMenu: [
                [10, 25, 50, -1],
                ['10 itens por página', '25 itens por página', '50 itens por página',
                    'Todos os registros'
                ]
            ],
            responsive: false,
            processing: true,
            serverSide: false,
            ajax: {
                url: '/api/reports/subscribers/get-all'
            },
            language: {
                url: "{{ asset('js/datatable-translate-pt-BR.json') }}"
            },
            order: [
                [4, 'desc']
            ],
            columns: [{
                    data: 'plans',
                    className: 'w-25'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'main_phone'
                },
                {
                    data: 'created_at',
                    render: function(data, type) {
                        return (data != null) ? formatDateTimePTBR(data) : '';
                    },
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        return row.status_description;
                    }
                },
                {
                    data: 'login',
                    render: function(data, type) {
                        return (data != null) ? formatDateTimePTBR(data) : '';
                    },
                },
            ],
            buttons: [{
                    extend: 'pdf',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">' +
                        '       <i class="fas fa-file-pdf" style="color: red"></i>' +
                        '  </button>',
                    exportOptions: {
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                    action: function(e, dt, node, config) {
                        btnTypeX = this;
                        eX = e;
                        dtX = dt;
                        nodeX = node;
                        configX = config;
                        requestFile('pdf');
                    }
                },
                {
                    extend: 'csv',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">' +
                        '      <i class="fas fa-file-csv" style="color: blue"></i>' +
                        '  </button>',
                    exportOptions: {
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                    action: function(e, dt, node, config) {
                        btnTypeX = this;
                        eX = e;
                        dtX = dt;
                        nodeX = node;
                        configX = config;
                        requestFile('csv');
                    }
                },
                {
                    extend: 'excel',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">' +
                        '      <i class="fas fa-file-excel" style="color: green"></i>' +
                        '  </button>',
                    exportOptions: {
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                    action: function(e, dt, node, config) {
                        btnTypeX = this;
                        eX = e;
                        dtX = dt;
                        nodeX = node;
                        configX = config;
                        requestFile('excel');
                    }
                },
            ],
            initComplete: function(settings, json) {
                $('.title-table').html(
                    '<h5 class="align-self-center">Alunos: <span id="spn-total-label">{{ $total }}</span></h5>'
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
                                    <div class="col-sm-12 col-md-3 mt-1">
                                        <div class="xgrow-form-control mb-2">
                                            <select id="slc-plan-filter" class="xgrow-select w-100" name="plan-filter[]" id="product" multiple>
                                                @foreach ($plans as $plan)
                                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                                @endforeach
                </select>
    </div>
</div>
<div class="col-sm-12 col-md-3 mt-1">
    <div class="xgrow-form-control mb-2">
        <select id="slc-status-filter" class="xgrow-select w-100" multiple>
            <option value="Ativo">Ativo</option>
            <option value="Trial">Trial</option>
            <option value="Pendente">Pendente</option>
            <option value="Inativo">Inativo</option>
            <option value="Cancelado">Cancelado</option>
        </select>
    </div>
</div>
<div class="col-sm-12 col-md-3 mt-1">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
        <input type="text" value="" class="form-control" id="ipt-created-range"
            style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
            autocomplete="off">
        <label for="ipt-created-range">Data de cadastro</label>
    </div>
</div>
<div class="col-sm-12 col-md-3 mt-1">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
        <input type="text" value="" class="form-control" id="ipt-last-access-range"
            style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
            autocomplete="off">
        <label for="ipt-last-access-range">Último acesso</label>
    </div>
</div>
</div>
<div class="form-check form-switch">
<input id="swt-not-acess" class="form-check-input" type="checkbox"/>
<label for="not-acess">Mostrar apenas alunos que nunca acessaram</label>
</div>
</div>
</div>
</div>`);
                //Column sempre começa em 0
                $('.xgrow-datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                });
                $('#slc-plan-filter').select2({
                    allowClear: true,
                    placeholder: 'Produto'
                });
                $('#slc-status-filter').select2({
                    allowClear: true,
                    placeholder: 'Status'
                });

                $('#ipt-global-filter').on('keyup', function() {
                    customTable.search(this.value).draw();
                });

                $('#slc-plan-filter').on('change', function() {
                    const selected = $('#slc-plan-filter').val();
                    const filter = selected.join('|');
                    customTable.columns(0).search(filter, true, false).draw();
                });

                $('#slc-status-filter').on('change', function() {
                    const selected = $('#slc-status-filter').val();
                    const filter = selected.join('|');
                    customTable.columns(5).search(filter, true, false).draw();
                });

                $('#swt-not-acess').on('change', function() {
                    customTable.columns(6).search('').draw();
                });

                $('#ipt-created-range').daterangepicker(dateRangeOptions)
                    .on('apply.daterangepicker', function(ev, picker) {
                        if (!picker.startDate.isValid() && !picker.endDate.isValid()) {
                            return $(this).trigger('cancel.daterangepicker');
                        }
                        $(this).val(picker.startDate.format('DD/MM/YYYY') + '-' + picker.endDate
                            .format('DD/MM/YYYY'));
                        $(this).removeClass('mui--is-empty');
                        $(this).addClass('mui--is-not-empty');

                        customTable.columns(4).search('').draw();
                    })
                    .on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                        customTable.columns(4).search('').draw();
                    });

                $('#ipt-last-access-range').daterangepicker(dateRangeOptions)
                    .on('apply.daterangepicker', function(ev, picker) {
                        if (!picker.startDate.isValid() && !picker.endDate.isValid()) {
                            return $(this).trigger('cancel.daterangepicker');
                        }
                        $(this).val(picker.startDate.format('DD/MM/YYYY') + '-' + picker.endDate
                            .format('DD/MM/YYYY'));
                        $(this).removeClass('mui--is-empty');
                        $(this).addClass('mui--is-not-empty');

                        customTable.columns(6).search('').draw();
                    })
                    .on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                        customTable.columns(6).search('').draw();
                    });
            }
        });

        function requestFile(type) {
            const loading = $('#customLoading');
            const codeReceive = $('#codeReceive');
            const exportEmail = $('#exportEmail');
            const exportModal = $('#exportModal');
            const btnExportModal = $('#btnExportModal');
            loading.removeClass('d-none');

            axios.post('/api/action-send-code')
                .then((res) => {
                    codeReceive.val('');
                    exportEmail.text(res.data.email);
                    exportModal.modal('show');
                    loading.addClass('d-none');
                    btnExportModal.attr('disabled', 'true');

                    codeReceive.on("input", function() {
                        const inputLenght = $(this).val().length;
                        if (inputLenght > 7) {
                            btnExportModal.removeAttr('disabled');
                        } else {
                            btnExportModal.attr('disabled', 'true');
                        }
                    });
                    btnExportModal.attr('data-type', type);
                })
                .catch((error) => {
                    errorToast('Algum erro aconteceu!', `${error.response.data.message}`);
                });
        }

        function sendCode() {
            const codeReceive = $('#codeReceive');
            const exportModal = $('#exportModal');
            const btnExportModal = $('#btnExportModal');

            if (codeReceive.val().trim() === '') {
                errorToast('Algum erro aconteceu!', 'Digite o PIN recebido em seu e-mail.');
                return false;
            } else {
                axios.post('/api/verify-pin-code', {
                        code: codeReceive.val()
                    })
                    .then((sendRes) => {
                        successToast('Iniciando download!', sendRes.data.message);
                        if (btnExportModal.data('type') === 'pdf') {
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(btnTypeX, eX, dtX, nodeX, configX);
                        }
                        if (btnExportModal.data('type') === 'csv') {
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(btnTypeX, eX, dtX, nodeX, configX);
                        }
                        if (btnExportModal.data('type') === 'excel') {
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(btnTypeX, eX, dtX, nodeX, configX);
                        }
                        exportModal.modal('hide');
                    })
                    .catch((sendError) => {
                        errorToast('Algum erro aconteceu!', `${sendError.response.data.message}`);
                    });
            }
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Relatórios</span></li>
            <li class="breadcrumb-item active mx-2"><span>Produtos</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            <div class="table-responsive m-t-30">
                <table id="subscriber-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                            <th style="width: 20%">Produto</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Cadastro</th>
                            <th>Status</th>
                            <th>Útimo Acesso</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('elements.code-action-modal')
    @include('elements.toast')
@endsection
