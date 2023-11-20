@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script>
        $(function() {
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex){
                    let dataReturn = true;
                    const iptCreatedRange = document.getElementById('ipt-created-range');
                    if (!iptCreatedRange) return true;

                    const iptCreatedPeriod = iptCreatedRange.value;
                    if (!iptCreatedPeriod) return true;

                    if (iptCreatedPeriod) {
                        const [start, end] = iptCreatedPeriod.split('-');
                        const parsedDate = parseDatatablesDate(aData[2]);
                        const parsedStart = parseDatatablesDate(start);
                        const parsedEnd = parseDatatablesDate(end);
                        if (parsedDate >= parsedStart && parsedDate <= parsedEnd) {
                            dataReturn = true;
                        }
                        else {
                            dataReturn = false;
                        }
                    }

                    return dataReturn;
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
{{--    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">--}}
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- end - This is for export functionality only -->

    <script>

        function invokeDelete(id, additionalDescription = '')
        {
            const deleteRoute = @json(route('audience.destroy', ':id'));
            const deleteUrl = deleteRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : ''
            const modalOptions = {
                title: 'Excluir público',
                description: 'Você tem certeza que deseja excluir o público' + desc + '?',
                btnSave: 'Sim, excluir',
                btnCancel: 'Não, manter',
                success: 'Público excluído com sucesso',
                error: 'Não foi possível excluir o público: ',
                url: deleteUrl,
                method: 'DELETE',
                body: {
                    'id': id,
                    '_token': "{{ csrf_token() }}",
                },
                datatables: '#audience-table'
            }

            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)))
        }

        $(document).ready(function() {
            const dateRangeOptions = {

                autoUpdateInput: false,
                'locale': {
                    'format': 'DD/MM/YYYY',
                    'separator': ' - ',
                    'applyLabel': 'Aplicar',
                    'cancelLabel': 'Limpar',
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

            let createdAtRange = '';
            let lastAccessRange = '';
            let datatable;
            datatable = $('#audience-table').DataTable({
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                    '<"d-flex flex-wrap mt-2"<B><"create-button mb-2">>>>>' +
                    '<"filter-div mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                ajax: {
                    url: '{{route('audience.datatables')}}',
                },
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
                order: [
                    [2, 'desc'],
                ],
                columns: [{
                        data: 'id',
                        orderable: false,
                        visible: false
                    },
                    {
                        data: 'name',
                        name: 'audiences.name',
                        visible: true,
                    },
                    {
                        data: 'created_at',
                        name: 'audiences.created_at',
                        type: 'date',
                        render: function(data, type) {
                            if (type === 'sort') {
                                return data;
                            }
                            return (data != null) ? formatter.toBrDatetime(data) : '';
                        },
                    },
                    {
                        data: 'condition_text',
                        orderable: false,
                        visible: true,
                        render: function (data, type, row, meta) {
                            return (data != null) ? formatter.newLineToBr(data) : '';
                        },
                    },
                    {
                        data: 'description',
                        orderable: false,
                        visible: true,
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            const route = @json(route('audience.edit', ':id'));
                            const url = route.replace(/:id/g, row.id);

                            let menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">`;

                            if (row.callcenter_active === false)
                                menu += `<li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="startAttendanceAgain(${row.id})">Reativar atendimentos no callcenter</a></li>`;

                            menu += `
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
                initComplete: function(settings, json) {
                    $('.title-table').html(
                        '<h5 class="align-self-center">Público: <span id="spn-total-label"></span></h5>'
                    );
                    $('.buttons-csv').removeClass('dt-button buttons-csv');
                    $('.buttons-excel').removeClass('dt-button buttons-excel');
                    $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                    $('.create-button').html(
                        '<button onclick="location.href=\'{{route('audience.create')}}\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo público </button>'
                    );
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
                                                <div class="col-sm-12 col-md-6 mt-1">
                                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                                        <input type="text" value="" class="form-control" id="ipt-created-range"
                                                            style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
                                                            autocomplete="off">
                                                        <label for="ipt-created-range">Data de criação</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`);

                    $('.xgrow-datepicker').datepicker({
                        format: 'dd/mm/yyyy',
                    });
                    $('#ipt-global-filter').on('keyup', function() {
                        datatable.search(this.value).draw();
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

                            datatable.columns(2).search('').draw();

                            // const createdAtStart = picker.startDate.format('YYYY-MM-DD');
                            // const createdAtEnd = picker.endDate.format('YYYY-MM-DD');
                            // datatable.columns(5).search(`${createdAtStart}/${createdAtEnd}`).draw();
                        })
                        .on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                            datatable.columns(2).search('').draw();
                        });

                    // total when component did mount
                    evaluateTotal(datatable);
                },
                drawCallback: function(settings) {
                    evaluateTotal(datatable);
                }
            });
        });

        function evaluateTotal(datatable) {
            const total = datatable.page.info().recordsDisplay || 0;
            setTotalLabel(total);
        }

        function setTotalLabel(total = 0) {
            let label = 'público';
            if (total > 1) label = 'públicos';
            $('#spn-total-label').text(`${total} ${label}`);
        }


    </script>

@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Engajamento</li>
{{--            <li class="breadcrumb-item">Campanhas</li>--}}
            <li class="breadcrumb-item active"><a href="{{route('audience.index')}}">Públicos</a></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
                <div class="table-responsive m-t-30">
                    @if ($errors->any())
                        @include('elements.alert')
                    @endif

                    <table id="audience-table"
                        class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                        style="width:100%">
                        <thead>
                            <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                                <th>Id</th>
                                <th>Nome</th>
                                <th>Data de criação</th>
                                <th>Condições</th>
                                <th>Descrição</th>
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
