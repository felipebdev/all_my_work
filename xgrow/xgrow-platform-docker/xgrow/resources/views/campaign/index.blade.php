@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/section_index.css') }}" rel="stylesheet">
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


    <script>
        function invokeDelete(id, additionalDescription = '') {
            const deleteRoute = @json(route('campaign.destroy', ':id'));
            const deleteUrl = deleteRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : '';
            const modalOptions = {
                title: 'Excluir campanha',
                description: 'Você tem certeza que deseja excluir a campanha' + desc + '?',  // @todo
                btnSave: 'Sim, excluir',
                btnCancel: 'Não, manter',
                success: 'Campanha excluída com sucesso',
                error: 'Não foi possível excluir a campanha: ',
                url: deleteUrl,
                method: 'DELETE',
                body: {
                    'id': id,
                    '_token': "{{ csrf_token() }}",
                },
                datatables: '#content-table'
            }

            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)))
        }

        $(function () {
            let datatable;
            datatable = $('#content-table').DataTable({
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search">' +
                    '<"d-flex flex-wrap mt-2"<B><"create-button mb-2">>>>>' +
                    '<"filter-div mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                ajax: '/campaign/get-data',
                processing: true,
                serverSide: false,
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página',
                        'Todos os registros'
                    ]
                ],
                language: {
                    'url': "{{ asset('js/datatable-translate-pt-BR.json') }}",
                },
                'columnDefs': [{
                    'visible': false,
                    'searchable': false,
                }],
                columns: [
                    {
                        data: 'name',
                        name: 'campaigns.name',
                        render: function (data, type, row) {
                            return data ? resumeString(data, 20) : '';
                        },
                    },
                    {
                        data: 'type_campaign',
                        name: 'campaigns.type_campaign'
                    },
                    {
                        data: 'start_at',
                        name: 'campaigns.start_at',
                        render: function (data, type, row, meta) {
                            if (type === 'sort') {
                                return data;
                            }
                            return data ? formatter.toBrDatetime(data) : '';
                        },
                    },
                    {
                        data: 'audience_names',
                        sortable: false,
                        render: function (data, type, row, meta) {
                            return data ? formatter.newLineToBr(data) : '';
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function (data, type, row) {
                            const route = @json(route('campaign.edit', ':id'));
                            const url = route.replace(/:id/g, row.id);

                            const menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                        <li><a class="dropdown-item table-menu-item" href="${url}">Editar</a></li>
                                        <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="invokeDelete(${row.id})">Excluir</a></li>
                                    </ul>
                                </div>
                            `;
                            return '<div class="d-flex">' + menu + '';
                        },
                    },

                ],
                buttons: [{
                    extend: 'pdf',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">' +
                        '<i class="fas fa-file-pdf" style="color: red"></i>' +
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
                        text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">' +
                            '<i class="fas fa-file-csv" style="color: blue"></i>' +
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
                        extend: 'excel',
                        text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">' +
                            '<i class="fas fa-file-excel" style="color: green"></i>' +
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
                ],
                initComplete: function (settings, json) {
                    $('.title-table').html(
                        '<h5 class="align-self-center">Campanha: <span id="spn-total-label">{{ $campaigns->count() }}</span></h5>'
                    );
                    $('.create-button').html(
                        '<button onclick="location.href=\'/campaign/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Nova campanha </button>'
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

                                        </div>
                                    </div>
                                </div>
                            </div>`);

                    $('#ipt-global-filter').on('keyup', function () {
                        datatable.search(this.value).draw();
                    });
                },
            });
        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Engajamento</li>
            <li class="breadcrumb-item active mx-2"><span>Campanhas</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            @if ($audiences->count() <= 0)
                <div class="alert alert-warning">
                    Antes de criar uma [CAMPANHA] você precisa:
                    <ul>
                        <li>Criar ao menos um público, que identifica quem é publico alvo da sua campanha.
                            Para criar um público acesse o menu "Engajamento > Público" ou clique
                            <a href="{{ route('audience.create') }}">aqui</a>.
                        </li>
                    </ul>
                </div>
            @else
                <div class="table-responsive m-t-30">
                    @if ($errors->any())
                        @include('elements.alert')
                    @endif
                    <table id="content-table"
                           class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                           style="width:100%">
                        <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Data de lançamento</th>
                            <th>Públicos</th>
                            <th width="50" class="no-export"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
