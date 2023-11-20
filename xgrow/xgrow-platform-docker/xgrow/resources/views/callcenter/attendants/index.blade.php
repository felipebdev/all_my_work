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
            const deleteRoute = @json(route('attendant.destroy', ':id'));
            const deleteUrl = deleteRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : '';
            const modalOptions = {
                title: 'Bloquear atendente',
                description: 'Você tem certeza que deseja bloquear o atendente' + desc + '? \n',  // @todo
                btnSave: 'Sim, bloquear',
                btnCancel: 'Não, manter',
                success: 'Atendente bloqueado com sucesso',
                error: 'Não foi possível bloquear o atendente: ',
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

        function invokeRestore(id) {
            const restoreRoute = @json(route('attendant.restore', ':id'));
            const restoreUrl = restoreRoute.replace(/:id/g, id);
            $.ajax({
                url: restoreUrl,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    $('#content-table').DataTable().ajax.reload();
                },
            });
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
                ajax: '/callcenter/attendant/get-data',
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
                    },
                    {
                        data: 'email',
                    },
                    {
                        data: 'audience',
                    },
                    {
                        data: 'trashed',
                        name: 'attendants.trashed',
                        render: function (data) {
                            return (data === 'active') ? 'Ativo' : 'Bloqueado';
                        },
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            const route = @json(route('attendant.edit', ':id'));
                            const url = route.replace(/:id/g, row.id);

                            const route_report = @json(route('callcenter.reports.attendant', ':id'));
                            const url_report = route_report.replace(/:id/g, row.id);

                            const menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                        <li><a class="dropdown-item table-menu-item" href="${url_report}">Ver relatórios</a></li>
                                        <li><a class="dropdown-item table-menu-item" href="${url}">Editar</a></li>
                                        <li>
                                                ${
                                                    row.trashed === 'active' ?
                                                    '<a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="invokeDelete(' + row.id + ')">Bloquear</a>' :
                                                    '<a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="invokeRestore(' + row.id + ')">Reativar</a>'
                                                }
                                        </li>
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
                            modifier: {
                                selected: true,
                                page: 'all'
                            }
                        },
                    },
                ],
                initComplete: function (settings, json) {
                    $('.title-table').html(
                        '<h5 class="align-self-center">Atendentes: <span id="spn-total-label">{{ $attendants->count() }}</span></h5>'
                    );
                    $('.create-button').html(
                        '<button onclick="location.href=\'/callcenter/attendant/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo atendente </button>'
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

                                        </div>
                                    </div>
                                </div>
                            </div>`);

                    $('#ipt-global-filter').on('keyup', function () {
                        datatable.search(this.value).draw();
                    });

                },
                drawCallback: function(settings) {
                    const total = datatable.page.info().recordsDisplay || 0;
                    setTotalLabel(total);
                }
            });
        });

        function setTotalLabel(total = 0) {
            $('#spn-total-label').text(`${total}`);
        }
    </script>
@endpush

@section('content')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('callcenter.dashboard') }}">Call center</a></li>
            <li class="breadcrumb-item active mx-2"><span>Atendentes</span></li>
        </ol>
    </nav>

    @if ($availabeAudience === false && $numberAttendants > 0)
        <div class="alert alert-warning">
            Antes de criar um atendente, você precisa ter um público ativo. Você pode:
            <ul>
                <li>
                    Criar ou reativar um público pelo menu "Engajamento > Públicos" ou
                    <a href="/audience">clique aqui</a>
                </li>
            </ul>
        </div>
    @endif

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">

            @if ($availabeAudience || $numberAttendants > 0)
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
                                <th>Email</th>
                                <th>Públicos</th>
                                <th>Status</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    Antes de criar um atendente, você precisa criar um público.
                    Para criar um público acesse o menu "Engajamento > Públicos" ou
                    <a href="/audience">clique aqui</a>
                </div>
            @endif
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
