@push('before-styles')
    <style>
        .datatable-ctx {
            display: flex;
        }
    </style>
@endpush
@push('after-scripts')
    <script>
        let tblSemCurso = $('#without-course-table').DataTable({
            dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                '<"title-table-3 d-flex align-self-center justify-content-center me-1">' +
                '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search-3"><"filter-button-3">' +
                '<"d-flex flex-wrap"<B3>>>>>' +
                '<"filter-div-3 mt-2"><"mt-2" rt>' +
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
            serverSide: false,
            ajax: '/api/reports/subscriber-without-course',
            columns: [
                {data: 'name'},
                {data: 'email'},
                {
                    data: 'created_at',
                    render: function (data) {
                        return (data == null) ? '' : moment(data).format('DD/MM/YYYY');
                    },
                },
                {
                    data: 'status',
                    render: function (data, type, row) {
                        return ('Não iniciou nenhum curso');
                    },
                }
            ],
            buttons: [
                // {
                //     extend: 'pdf',
                //     text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">' +
                //         '<i class="fas fa-file-pdf" style="color: red"></i>' +
                //         '</button>',
                //     className: '',
                //     exportOptions: {
                //         modifier: {
                //             selected: true,
                //             page: 'all'
                //         }
                //     },
                // },
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
            language: {
                'url': "{{ asset("js/datatable-translate-pt-BR.json")}}"
            },
            initComplete: function (settings, json) {
                $('.buttons-csv').removeClass('dt-button buttons-csv');
                $('.buttons-excel').removeClass('dt-button buttons-excel');
                $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                $('.buttons-print').removeClass('dt-button buttons-print');
                $('.dataTables_filter input').attr('placeholder', 'Pesquisar');
                $('.global-search-3').html(`
                    <div class="xgrow-input me-1" style="background-color: var(--input-bg); height: 40px;" >
                        <input id="ipt-global-filter-3" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                        <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                `);

                $('#ipt-global-filter-3').keyup((e) => {
                    tblSemCurso.search(e.target.value).draw();
                });
            },
        });
    </script>
@endpush


<div class="table-responsive m-t-30">
    <table id="without-course-table"
           class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
           style="width:100%">
        <thead>
        <tr class="card-black" style="border: 2px solid var(--black1)">
            <th>Nome</th>
            <th>E-mail</th>
            <th>Cadastro</th>
            <th>Status</th>
        </tr>
        </thead>
    </table>
</div>
