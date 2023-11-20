@extends('templates.xgrow.main')

@php
use App\Constants;
@endphp

@push('before-scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .x-dropdown {
            position: unset !important;
        }
    </style>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>

    <script>
        function invokeDelete(id, additionalDescription = '') {
            const deleteRoute = @json(route('plans.destroy', ':id'));
            const deleteUrl = deleteRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : ''
            const modalOptions = {
                title: 'Excluir produto',
                description: 'Você tem certeza que deseja excluir o produto' + desc + '?',
                btnSave: 'Sim, excluir',
                btnCancel: 'Não, manter',
                success: 'Produto excluído com sucesso',
                error: 'Não foi possível excluir o produto: ',
                url: deleteUrl,
                method: 'DELETE',
                body: {
                    'id': id,
                    '_token': "{{ csrf_token() }}",
                },
                datatables: '#plan-table' // @todo
            }

            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)))
        }

        $(function() {
            $(document).ready(function() {
                let datatable;
                datatable = $('#plan-table').DataTable({
                    dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                        '<"title-table d-flex align-self-center justify-content-center me-1">' +
                        '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                        '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                        '<"d-flex flex-wrap mt-2"<B><"create-button mb-2">>>>>' +
                        '<"filter-div mt-2"><"mt-2" rt>' +
                        '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                    ajax: '/plans',
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
                        [8, 'desc'],
                    ],
                    columns: [{
                            data: 'name',
                            render: function(data, type, row, meta) {
                                return '<a href="/plans/' + row.id +
                                    '/edit" style="color: inherit">' + data + '</a>';
                            },
                        },
                        {
                            data: 'type_plan',
                            render: function(data, type, row, meta) {
                                return (data) ? formatPaymentType(data) : '-';
                            }
                        },
                        {
                            data: 'freedays',
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return (data) ? data : '-';
                            }
                        },
                        {
                            data: 'freedays_type',
                            render: function(data, type, row, meta) {
                                return (data) ? formatFreedaysType(data) : '-';
                            }
                        },
                        {
                            data: 'integratable[0].integration[0].id_integration',
                            searchable: false,
                            render: function(data, type, row) {
                                return (data && data != 'FANDONE') ? data : 'XGROW';
                            }
                        },
                        {
                            data: 'status',
                            render: function(data, type, row) {
                                let checked = (data === 'active' || data === '1') ?
                                    'checked' : null;
                                let checkbox = `
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" id="switch-${row.id}" type="checkbox" ${checked} onClick="changeStatus(${row.id}, $(this))">
                                                            <label class="form-check-label" for="switch-${row.id}"></label>
                                                        </div>`;

                                return checkbox;
                            },
                        },
                        {
                            data: 'status',
                            visible: false
                        },
                        {
                            data: 'price',
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return formatCoin(row.price, 'BRL');
                            }
                        },
                        {
                            data: 'created_at',
                            visible: false
                        },
                        {
                            data: null,
                            searchable: false,
                            render: function(data, type, row) {
                                const route = @json(route('plans.edit', ':id'));
                                const url = route.replace(/:id/g, row.id);

                                const menu = `
                                        <div class="dropdown x-dropdown">
                                            <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                                <li><a class="dropdown-item table-menu-item" href="${url}">Editar</a></li>
                                                <li><a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                                    onclick="replicatePlan(${row.id})">
                                                    Duplicar produto
                                                </a></li>
                                                <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="invokeDelete(${row.id})">Excluir</a></li>
                                                <li><a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                                    onclick="copyCheckoutLink(${row.id}, ${ row.integratable[0] != undefined ? `'${row.integratable[0].integration[0].id_integration}'` : null })">
                                                    Copiar link do checkout
                                                </a></li>
                                            </ul>
                                        </div>
                                    `;
                                return '<div class="d-flex">' + menu + '';
                            },
                        },
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
                            '<h5 class="align-self-center">Produtos: <span id="spn-total-label">{{ $totalLabel }}</span></h5>'
                        );
                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                        $('.create-button').html(
                            '<button onclick="location.href=\'/plans/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo produto</button>'
                        );
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
                                                                    <div class="col-sm-12 col-md-4">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-payment-type-filter" class="xgrow-select w-100" multiple>
                                                                                <option value="Assinatura">Assinatura</option>
                                                                                <option value="Venda única">Venda única</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 col-md-4">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-freedays-filter" class="xgrow-select w-100" multiple>
                                                                                <option value="Grátis">Grátis</option>
                                                                                <option value="Experiência">Experiência</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 col-md-4">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select class="xgrow-select w-100" id="slc-status-filter" multiple>
                                                                                <option value="1">Ativo</option>
                                                                                <option value="0">Inativo</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`);

                        $('#slc-payment-type-filter').select2({
                            allowClear: true,
                            placeholder: 'Tipo de pagamento'
                        });
                        $('#slc-freedays-filter').select2({
                            allowClear: true,
                            placeholder: 'Tipo de Teste'
                        });
                        $('#slc-status-filter').select2({
                            allowClear: true,
                            placeholder: 'Status'
                        });

                        $('#ipt-global-filter').on('keyup', function() {
                            datatable.search(this.value).draw();
                        });

                        $('#slc-payment-type-filter').on('change', function() {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(1).search(filter, true, false).draw();
                        });

                        $('#slc-freedays-filter').on('change', function() {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(3).search(filter, true, false).draw();
                        });

                        $('#slc-status-filter').on('change', function() {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(6).search(filter, true, false).draw();
                        });
                    },
                    drawCallback: function(settings) {
                        const total = datatable.page.info().recordsDisplay || 0;
                        setTotalLabel(total);
                    }
                });
            });
        });

        function formatRecurrence(recurrence) {
            const recurrences = {
                '1': 'Única',
                '7': 'Semanal',
                '30': 'Mensal',
                '60': 'Bimestral',
                '90': 'Trimestral',
                '180': 'Semestral',
                '360': 'Anual',
            };

            return recurrences[recurrence];
        }

        function formatFreedaysType(freedayType) {
            const types = {
                'free': 'Grátis',
                'trial': 'Experiência',
            };

            return types[freedayType];
        }

        function formatPaymentType(payment) {
            const types = {
                'P': 'Venda única',
                'R': 'Assinatura',
            };

            return types[payment];
        }

        function setTotalLabel(total = 0) {
            let label = 'produto';
            if (total > 0) label = 'produtos';
            $('#spn-total-label').text(`${total} ${label}`);
        }

        function changeStatus(id, component) {
            $.ajax({
                url: `/plans/${id}/status`,
                type: 'PUT',
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    successToast('Registro alterado!', 'Ação feita com sucesso!');
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!',
                        `Houve um erro ao alterar o registro: ${data.responseJSON.message}`);
                }
            });
        }

        async function replicatePlan(id, additionalDescription = '') {
            const replicateRoute = @json(route('plans.replicate', ':id'));
            const replicateUrl = replicateRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : ''
            const modalOptions = {
                title: 'Duplicar o porduto',
                description: 'Você tem certeza que duplicar o produto' + desc + '?',
                btnSave: 'Sim, duplicar',
                btnCancel: 'Não, manter como está',
                success: 'Produto duplicado com sucesso',
                error: 'Não foi possível duplicar o produto: ',
                url: replicateUrl,
                method: 'POST',
                body: {
                    'id': id,
                    '_token': "{{ csrf_token() }}",
                },
                datatables: '#plan-table'
            }

            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)))
        }

        async function copyCheckoutLink(id_checkout, id_integration) {
            let mundipagg = `{{ Constants::CONSTANT_INTEGRATION_MUNDIPAGG }}`;

            let url = `{{ config('app.url_checkout') }}/`;

            if (id_integration == mundipagg || id_integration == null) {
                url += `{{ Auth::user()->platform_id }}/`;
                url += window.btoa(id_checkout);
            } else {
                // url += `${id_integration.toLowerCase()}/`;
                url += `{{ Auth::user()->platform_id }}/`;
                url += window.btoa(id_checkout);
                url += '/c';
            }

            let textarea = document.createElement("textarea");
            textarea.textContent = url;
            textarea.style.position = "fixed";
            document.body.appendChild(textarea);
            textarea.select();
            try {
                await document.execCommand("copy");
                document.body.removeChild(textarea);
                successToast('Link copiado!', "O link foi transferido para sua área de transferência.");
            } catch (ex) {
                errorToast('Aconteceu um erro!', `Não foi possível executar a ação: ${ex}`);
            }
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Produtos</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            @include('elements.alert')
            <div class="table-responsive m-t-30">
                <table id="plan-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>Nome</th>
                            <th>Tipo de pagamento</th>
                            <th>Dias Teste</th>
                            <th>Tipo Teste</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Criado em</th>
                            <th class="no-export"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
