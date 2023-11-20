@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css"/>
    <link href="{{asset('xgrow-vendor/assets/css/pages/report_search.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .buttons-html5 {
            background: transparent !important;
            border: none !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="{{asset('xgrow-vendor/assets/js/toast-config.js')}}"></script>

    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            let datatable = $('#content-table').DataTable({
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                    '<"d-flex flex-wrap">>>>' +
                    '<"filter-div mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                columnDefs: [{
                    'searchable': true, 'targets': [0]
                }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página', "Todos os registros"]
                ],
                scrollX: false,
                orderCellsTop: true,
                fixedHeader: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('reports.datatable.contents-search') !!}',
                columns: [
                    {
                        data: 'access_date',
                        name: 'content_logs.created_at',
                        render: function (data, type, row, meta) {
                            return (data == null) ? 'Indefinida' : moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {data: 'content_title', name: 'contents.title'},
                    {data: 'section_name', name: 'sections.name'},
                    {data: 'subscriber_name', name: 'subscribers.name'},
                    {data: 'ip', name: 'content_logs.ip'},
                    {data: 'minutes', name: 'minutes', searchable: false}
                ],
                buttons: [
                    {
                        extend: 'csv',
                        text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">\n' +
                            '      <i class="fas fa-file-csv" style="color: blue"></i>\n' +
                            '  </button>',
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
                        text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">\n' +
                            '      <i class="fas fa-file-excel" style="color: green"></i>\n' +
                            '  </button>',
                        className: '',
                        exportOptions: {
                            modifier: {
                                selected: true,
                                page: 'all'
                            }
                        },
                    },
                ],
                language: {
                    'url': "{{ asset("js/datatable-translate-pt-BR.json")}}"
                },
                initComplete: function (settings, json) {
                    $('.title-table').html('<h5 class="align-self-center"> Pesquisa de conteúdos </h5>')
                    $('.buttons-csv').removeClass('dt-button buttons-csv');
                    $('.buttons-excel').removeClass('dt-button buttons-excel');
                    $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                    $('.create-label').html('<p class="xgrow-medium-bold me-2">Exportar em</p>');
                    $('.create-length-items').html('<p class="xgrow-medium-bold mx-2">{{ $total_label }}</p>');
                    $('.dataTables_filter input').attr('placeholder', 'Pesquisar');
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
                                    <div class="col-sm-12 col-md-6 mt-1 mb-sm-2 mb-0">
                                        <div class="xgrow-form-control">
                                            <select class="xgrow-select" name="options" id="contentSelect" multiple></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 mt-1 mb-sm-2 mb-0">
                                        <div class="xgrow-form-control">
                                            <select class="xgrow-select w-100" name="options" id="sectionSelect" multiple></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `);

                    // debounce
                    function debounce(func, wait, immediate) {
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

                    // Pesquisa automática
                    $('#ipt-global-filter').keyup(debounce((e) => {
                        datatable.search(e.target.value).draw();
                    }, 500));

                    //Cria os options para o conteúdo
                    const contentOption = this.api().column(1).data().unique().sort().map((value) => {
                        return `<option value="${value}">${value}</option>`;
                    });

                    $('#contentSelect')
                        .prepend(contentOption.join(''))
                        .select2({
                            allowClear: true,
                            placeholder: 'Conteúdo'
                        })
                        .on('change', function (e) {
                            const selected = $(this).val()
                            const filter = selected.join('|');
                            datatable.columns(1).search(filter, true, false).draw();
                        });

                    //Cria os options para o seções
                    const sectionOptions = this.api().column(2).data().unique().sort().map((value) => {
                        return `<option value="${value}">${value}</option>`;
                    });

                    $('#sectionSelect')
                        .prepend(sectionOptions.join(''))
                        .select2({
                            allowClear: true,
                            placeholder: 'Seção'
                        })
                        .on('change', function (e) {
                            const selected = $(this).val()
                            const filter = selected.join('|');
                            datatable.columns(2).search(filter, true, false).draw();
                        });
                }
            });
        });
    </script>

@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Relatórios</span></li>
            <li class="breadcrumb-item active mx-2"><span>Pesquisa</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
                @include('elements.alert')
                <div class="table-responsive m-t-30">
                    @if ($errors->any())
                        @include('elements.alert')
                    @endif
                    <table id="content-table"
                           class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                           style="width:100%">
                        <thead>
                        <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                            <th>Data de acesso</th>
                            <th>Conteúdo</th>
                            <th>Seção</th>
                            <th>Assinante</th>
                            <th>Endereço IP</th>
                            <th>Tempo de acesso (min)</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
