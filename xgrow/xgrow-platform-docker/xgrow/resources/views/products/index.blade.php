@extends('templates.xgrow.main')

@php
    use App\Constants;
@endphp

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/verify-alert.css') }}" rel="stylesheet">
@endpush

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>

    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>

    <script>
        const contentAPI = @json(config('learningarea.url'));

        function invokeDelete(id, additionalDescription = '') {
            const deleteRoute = @json(route('products.delete', ':id'));
            const deleteUrl = deleteRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : '';
            const modalOptions = {
                title: 'Excluir plano',
                description: 'Você tem certeza que deseja excluir o produto ' + desc + '?',
                btnSave: 'Sim, excluir',
                btnCancel: 'Não, manter',
                success: 'Plano excluído com sucesso',
                error: 'Não foi possível excluir o plano: ',
                url: deleteUrl,
                method: 'DELETE',
                body: {
                    'id': id,
                    '_token': "{{ csrf_token() }}"
                },
                datatables: '#content-table',
                forceReload: true
            };

            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)));
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
                    ajax: "{{ route('products.get.all') }}",
                    processing: true,
                    serverSide: true,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 itens por página', '25 itens por página', '50 itens por página',
                            'Todos os registros'
                        ]
                    ],
                    'columnDefs': [{
                        'visible': false,
                        'searchable': false
                    }],
                    language: {
                        url: '{{ asset('js/datatable-translate-pt-BR.json') }}'
                    },
                    order: [
                        [2, 'desc']
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'name',
                            name: 'products.name',
                            render: function(data, type, row, meta) {
                                const route = @json(route('products.edit-plan', ':id'));
                                const url = route.replace(/:id/g, row.id);

                                return `<a href="${url}" style="color: inherit">${data}</a>`;
                            }
                        },
                        {
                            data: 'type',
                            name: 'products.type',
                            render: function(data, type, row, meta) {
                                return (data) ? formatPaymentType(data) : '-';
                            }
                        },
                        {
                            data: null,
                            name: 'products.delivery',
                            render: function(data, type, row, meta) {
                                let delivery = 'Entrega não selecionada';
                                if (parseInt(row.only_sell)) {
                                    delivery = 'Somente venda';
                                }
                                if (parseInt(row.external_learning_area)) {
                                    delivery = 'Área Externa';
                                }
                                if (parseInt(row.internal_learning_area)) {
                                    delivery = 'Área de Aprendizado Unificada XGROW';
                                }
                                return delivery;
                            }
                        },
                        {
                            data: 'status',
                            name: 'products.status',
                            render: function(data, type, row) {
                                let checked = (data) ? 'checked' : null;
                                let checkbox = `
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="switch-${row.id}" type="checkbox" ${checked} onClick="changeStatus(${row.id}, $(this))">
                                        <label class="form-check-label" for="switch-${row.id}"></label>
                                    </div>`;
                                return checkbox;
                            }
                        },
                        {
                            data: 'price',
                            searchable: false,
                            visible: false,
                        },
                        // {
                        //     data: 'created_at',
                        //     visible: false
                        // },
                        {
                            data: null,
                            searchable: false,
                            orderable: false,
                            render: function(data, type, row) {
                                const route = @json(route('products.edit-plan', ':id'));
                                const url = route.replace(/:id/g, row.id);;

                                let excluir = '';
                                if (row.subscribers_count === 0) {
                                    excluir =
                                        `<li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="invokeDelete(${row.id})">Excluir</a></li>`;
                                }

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
                                                <li><a class="dropdown-item table-menu-item" href="${url}#links">
                                                    Links
                                                </a></li>
                                                ${excluir}
                                            </ul>
                                        </div>
                                    `;
                                return '<div class="d-flex">' + menu + '';
                            }
                        }
                    ],
                    buttons: [
                        // {
                        //     extend: 'pdf',
                        //     text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">\n' +
                        //         '<i class="fas fa-file-pdf" style="color: red"></i>\n' +
                        //         '</button>',
                        //     className: '',
                        //     exportOptions: {
                        //         columns: [':visible:not(.no-export)'],
                        //         modifier: {
                        //             selected: true,
                        //             page: 'all'
                        //         }
                        //     },
                        // },
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
                            }
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
                            }
                        }
                    ],
                    initComplete: function(settings, json) {
                        $('.title-table').html(
                            '<h5 class="align-self-center">Produtos: <span id="spn-total-label"></span></h5>'
                        );
                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                        $('.create-button').html(
                            '<button onclick="location.href=\'/products/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo produto</button>'
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
                                                                    <div class="col-sm-12 col-md-4">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-product-type-filter" class="xgrow-select w-100" multiple>
                                                                                <option value="R">Assinatura</option>
                                                                                <option value="P">Venda única</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 col-md-4">
                                                                        <div class="xgrow-form-control mb-2">
                                                                            <select id="slc-delivery-filter" class="xgrow-select w-100" multiple>
                                                                                <option value="">Entrega não selecionada</option>
                                                                                <option value="external">Área Externa</option>
                                                                                <option value="internal">Área de Aprendizado Unificada XGROW</option>
                                                                                <option value="onlySell">Somente venda</option>
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

                        $('#slc-product-type-filter').select2({
                            allowClear: true,
                            placeholder: 'Tipo do produto'
                        });

                        $('#slc-delivery-filter').select2({
                            allowClear: true,
                            placeholder: 'Tipo da entrega'
                        });

                        $('#slc-status-filter').select2({
                            allowClear: true,
                            placeholder: 'Status'
                        });

                        $('#ipt-global-filter').on('keyup', function() {
                            datatable.search(this.value).draw();
                        });


                        $('#slc-product-type-filter').on('change', function() {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(2).search(filter, true, false).draw();
                        });


                        $('#slc-delivery-filter').on('change', function(event) {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(3).search(filter, true, false).draw();
                        });

                        $('#slc-status-filter').on('change', function() {
                            const selected = $(this).val();
                            const filter = selected.join('|');
                            datatable.columns(4).search(filter, true, false).draw();
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

        function formatRecurrence(recurrence) {
            const recurrences = {
                '1': 'Única',
                '7': 'Semanal',
                '30': 'Mensal',
                '60': 'Bimestral',
                '90': 'Trimestral',
                '180': 'Semestral',
                '360': 'Anual'
            };

            return recurrences[recurrence];
        }

        function formatFreedaysType(freedayType) {
            const types = {
                'free': 'Grátis',
                'trial': 'Experiência'
            };

            return types[freedayType];
        }

        function formatPaymentType(payment) {
            const types = {
                'P': 'Venda única',
                'R': 'Assinatura'
            };

            return types[payment];
        }

        function setTotalLabel(total = 0) {
            let label = 'produto';
            if (total > 0) label = 'produtos';
            $('#spn-total-label').text(`${total} ${label}`);
        }

        function changeStatus(id) {
            const route = @json(route('products.update.status', ':id'));
            const url = route.replace(/:id/g, id);
            axios.put(url).then(function(response) {
                successToast('Registro alterado!', 'Ação feita com sucesso!');
            }).catch(function(error) {
                errorToast('Algum erro aconteceu!',
                    `Houve um erro ao alterar o registro: ${error.response.data.message}`);
            });
        }

        async function replicatePlan(id, additionalDescription = '') {
            const replicateRoute = @json(route('products.replicate', ':id'));
            const replicateUrl = replicateRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : '';
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
                    '_token': "{{ csrf_token() }}"
                },
                datatables: '#plan-table'
            };

            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)));
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

            let textarea = document.createElement('textarea');
            textarea.textContent = url;
            textarea.style.position = 'fixed';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                await document.execCommand('copy');
                document.body.removeChild(textarea);
                successToast('Link copiado!', 'O link foi transferido para sua área de transferência.');
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

    @if ($verifyDocument)
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <img src="{{ asset('xgrow-vendor/assets/img/documents/warning.svg') }}" style="margin-right: 1rem">
                        <div>
                            <h6>Atenção!</h6>
                            @if (!$recipientStatusMessage)
                            <p>Antes de realizar sua primeira venda, nós precisamos verificar a sua identidade.
                                <a style="color:inherit;font-weight:700" href="{{ route('documents') }}">Clique aqui para verificar.</a>
                            </p>
                            @else
                                <p>{{ $recipientStatusMessage }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('elements.alert')
    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            @include('elements.alert')
            <div class="table-responsive m-t-30">
                <table id="plan-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>#</th>
                            <th>Nome</th>
                            <th>Tipo do produto</th>
                            <th>Entrega</th>
                            <th>Status</th>
                            <th>Valor</th>
                            {{--                        <th>Criado em</th> --}}
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
