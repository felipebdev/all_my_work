@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/verify-alert.css') }}" rel="stylesheet">
@endpush

@push('before-scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
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
        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait = 400, immediate = false) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        /**
         * Debounce keyup, but invoke callback only if input value changed
         *
         * @param selector
         * @param callbackOnStateChange
         */
        function debounceSearchInputOnChange(selector, callbackOnStateChange) {
            const $input = $(selector);
            var state = $input.val();
            $input.keyup(debounce((e) => {
                const newState = e.target.value;
                if (state !== newState) {
                    state = newState;
                    callbackOnStateChange(state);
                }
            }));
        }

        function getFilters() {
            const searchTerm = $('#ipt-global-filter').val();
            const productsId = $('#product-filter option:selected').map(function () {
                return this.value;
            }).get();
            const producerProductStatusId = $('#contract-status-filter option:selected').map(function () {
                return this.value;
            }).get();

            return {searchTerm, productsId, producerProductStatusId};
        }

        let datatable;
        $(function () {
            $(document).ready(function () {

                datatable = $('#plan-table').DataTable({
                    dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                        '<"title-table d-flex align-self-center justify-content-center me-1">' +
                        '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                        '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                        '<"d-flex flex-wrap mt-2"<B><"create-button mb-2">>>>>' +
                        '<"filter-div mt-2"><"mt-2" rt>' +
                        '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                    ajax: {
                        url: '{{ route('producers.get.all') }}',
                        data: function (d) {
                            Object.assign(d, getFilters());
                        }
                    },
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
                    columns: [
                        {
                            data: 'producers_id',
                            name: 'producers.id',
                            visible: false
                        },
                        {
                            data: 'platforms_users_name',
                            name: 'platforms_users.name',
                            render: function (data, type, row, meta) {
                                return row.platforms_users_name + '<br><small>' + row.platforms_users_email + '</small>';
                            }
                        },
                        {
                            data: 'platforms_users_email',
                            name: 'platforms_users.email',
                            visible: false,
                        },
                        // {
                        //     data: 'recent_access_date',
                        //     name: 'recent_access.date',
                        //     type: 'date',
                        //     render: function (data, type, row, meta) {
                        //         return (data) ? formatter.toBrDatetime(data) : 'Nunca acessou';
                        //     }
                        // },
                        {
                            data: 'products_name',
                            searchable: 'products.name',
                            render: function (data, type, row, meta) {
                                if (!row.products_name) {
                                    return 'Sem produto vinculado';
                                }

                                let status = '';
                                if (row.producer_products_canceled_at) {
                                    status = '<br><small>(cancelado em ' + formatter.toBrDatetime(row.producer_products_canceled_at) + ')</small>';
                                }

                                return row.products_name + ' - ' + row.producer_products_percent + '%' + status;
                            }
                        },
                        {
                            data: 'producer_products_status',
                            name: 'producer_products.status',
                            visible: false,
                        },
                        {
                            data: null,
                            searchable: false,
                            render: function (data, type, row) {
                                const route = @json(route('producers.edit', ':producerId'));
                                const url = route.replace(/:producerId/g, row.producers_id);

                                const menu = `
                                        <div class="dropdown x-dropdown">
                                            <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.producers_id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.producers_id}">
                                                <li><a class="dropdown-item table-menu-item" href="${url}">Editar</a></li>
                                                <li>
                                                    <a class="dropdown-item table-menu-item btn-"
                                                       href="javascript:void(0)"
                                                       onclick="deleteProducer('${row.producers_id}')">
                                                        Excluir coprodutor
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    `;
                                return '<div class="d-flex">' + menu + '</div>';
                            }
                        }
                    ],
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">' +
                                '<i class="fas fa-file-csv" style="color: blue"></i>' +
                                '</button>',
                            action: function (e, dt, node, config) {
                                successToast('Iniciando download!', 'Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral.');
                                axios.post("{{route('producers.export.data')}}", {
                                    ...getFilters(),
                                    typeFile: 'csv',
                                });
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">' +
                                '<i class="fas fa-file-excel" style="color: green"></i>' +
                                '</button>',
                            action: function (e, dt, node, config) {
                                successToast('Iniciando download!', 'Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral.');
                                axios.post("{{route('producers.export.data')}}", {
                                    ...getFilters(),
                                    typeFile: 'xlsx',
                                });
                            }
                        },
                    ],
                    initComplete: function (settings, json) {
                        $('.title-table').html(
                            '<h5 class="align-self-center">Coprodutores: <span id="spn-total-label"></span></h5>'
                        );
                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                        $('.create-button').html(
                            '<button onclick="location.href=\'/producers/create\'" class="xgrow-button" style="height:40px; width:fit-content; padding: 0 1rem"><i class="fa fa-plus"></i> Novo coprodutor</button>'
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
                                                <select id="product-filter" class="xgrow-select w-100" name="product-filter[]" id="product-filter" multiple>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 mt-1">
                                            <div class="xgrow-form-control mb-2">
                                                <select id="contract-status-filter" class="xgrow-select w-100" multiple>
                                                    @foreach (\App\ProducerProduct::listStatus() as $status => $description)
                                                    <option value="{{$status}}">{{$description}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`);

                        axios.get("{{ route('products.list') }}").then(response => {
                            let html = ""
                            response.data.products.forEach(item => html += `<option value="${item.id}">${item.name}</option>`);
                            $('#product-filter').append(html);
                        })

                        $('#product-filter').select2({
                            allowClear: true,
                            placeholder: 'Tipo do produto'
                        }).on('change', function () {
                            datatable.draw();
                        });

                        $('#contract-status-filter').select2({
                            allowClear: true,
                            placeholder: 'Status do contrato'
                        }).on('change', function () {
                            datatable.draw();
                        });

                        debounceSearchInputOnChange('#ipt-global-filter', function (state) {
                            datatable.search(state).draw();
                        });

                        setTotalLabel(datatable.page.info().recordsDisplay);
                    },
                    drawCallback: function (settings) {
                        const total = datatable.page.info().recordsDisplay || 0;
                        setTotalLabel(total);
                    }
                });
            });
        });

        function setTotalLabel(total = 0) {
            const label = total < 2 ? 'coprodução' : 'coproduções';
            $('#spn-total-label').text(`${total} ${label}`);
        }

        async function deleteProducer(producerId) {
            const route = @json(route('producers.destroy', [':producerId']));
            const url = route.replace(/:producerId/g, producerId);
            try {
                const res = await axios.delete(url);
                successToast('Coprodutor excluído', 'Coprodutor excluído com sucesso')
                datatable.ajax.reload();
            } catch (error) {
                const message = error.response.data.message
                    ? error.response.data.message
                    : 'Erro ao excluir coprodutor';
                errorToast('Erro', message);
            }
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Coprodutores</span></li>
        </ol>
    </nav>

    @if (isset($isOwner) && ($isOwner && !$clientApproved))
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <img src="{{ asset('xgrow-vendor/assets/img/documents/warning.svg') }}" style="margin-right: 1rem">
                    <div>
                        <h6>Atenção!</h6>
                        <p>Antes de realizar sua primeira venda, nós precisamos verificar a sua identidade.
                            <a style="color:inherit;font-weight:700" href="{{ route('documents') }}">Clique aqui para verificar.</a>
                        </p>
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
                        <th>Coprodutor</th>
                        <th>Email</th>
{{--                        <th>Último acesso</th>--}}
                        <th>Produto</th>
                        <th>Status do contrato</th>
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
