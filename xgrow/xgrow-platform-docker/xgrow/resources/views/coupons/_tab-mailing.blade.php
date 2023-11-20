@push('after-scripts')
    <script>
        $(function() {
            let datatable;
            datatable = $('#mailing-table').DataTable({
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"m-global-search">' +
                    '<"d-flex flex-wrap"<B><"create-button mb-2">>>>>' +
                    '<"m-filter-div mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                ajax: '{{ route("coupons.mailings.index.datatables", ["coupon" => $coupon->id ?? 0]) }}',
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
                    'searchable': true,
                }],
                language: {
                    url: '{{ asset('js/datatable-translate-pt-BR.json') }}',
                },
                columns: [
                    {
                        data: 'name',
                    },
                    {
                        data: 'email',
                    },
                    {
                        data: 'notes',
                        render: function (data, type, row) {
                            return (data) ? resumeString(data, 20) : '-';
                        }
                    },
                    {
                        data: 'isSent',
                        name: 'coupon_mailings.isSent',
                        render: function (data, type, row) {
                            return (data) ? 'Sim' : 'Não';
                        }
                    },
                    {
                        data: null,
                        searchable: false,
                        render: function(data, type, row) {
                            const menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                        <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="resendMail('/coupons/${data.coupon_id}/mailings/${data.id}')">Reenviar cupom por e-mail</a></li>
                                        <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="destroyMail('/coupons/${data.coupon_id}/mailings/${data.id}')">Excluir</a></li>
                                    </ul>
                                </div>
                            `;

                            return '<div class="d-flex">' + menu + '';
                        },
                    },
                ],
                buttons: [
                    // {
                    //     extend: 'pdf',
                    //     text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">\n' +
                    //         '                  <i class="fas fa-file-pdf" style="color: red"></i>\n' +
                    //         '                </button>',
                    //     className: '',
                    //     exportOptions: {
                    //         modifier: {
                    //             selected: true,
                    //             page: 'all'
                    //         }
                    //     },
                    // },
                    // {
                    //     extend: 'csv',
                    //     text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">\n' +
                    //         '                  <i class="fas fa-file-csv" style="color: blue"></i>\n' +
                    //         '                </button>',
                    //     className: '',
                    //     exportOptions: {
                    //         modifier: {
                    //             selected: true,
                    //             page: 'all'
                    //         }
                    //     },
                    // },
                    // {
                    //     extend: 'excel',
                    //     text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">\n' +
                    //         '                  <i class="fas fa-file-excel" style="color: green"></i>\n' +
                    //         '                </button>',
                    //     className: '',
                    //     exportOptions: {
                    //         modifier: {
                    //             selected: true,
                    //             page: 'all'
                    //         }
                    //     },
                    // },
                ],
                initComplete: function(settings, json) {
                    $('.title-table').html(
                        '<h5 class="align-self-center">Emails: <span id="m-spn-total-label"></span></h5>'
                    );
                    $('.buttons-csv').removeClass('dt-button buttons-csv');
                    $('.buttons-excel').removeClass('dt-button buttons-excel');
                    $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                    // $('.create-button').html(
                    //     '<button onclick="location.href=\'/coupons/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo cupom</button>'
                    // );
                    $('.dataTables_filter input').attr('placeholder', 'Buscar');
                    $('.create-label').html(
                        '<p class="xgrow-medium-bold me-2">Exportar em</p>');
                    $('.m-filter-button').html(`
                                <div class="d-flex align-items-center py-2">
                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-bs-expanded="false" aria-bs-controls="collapseExample" class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                        <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                    </button>
                                </div>
                            `);
                    $('.m-global-search').html(`
                                <div class="xgrow-input me-1 pt-0" style="background-color: var(--input-bg); height: 40px;" >
                                    <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                    <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                                </div>
                            `);
                    $('.m-filter-div').html(`
                        <div class="mb-3 collapse" id="collapseExample">
                            <div class="filter-container">
                                <div class="p-2 px-3">
                                    <div class="row">
                                    </div>
                                </div>
                            </div>
                        </div>`
                    );

                    setTotalLabel(json.recordsTotal);

                    $('#ipt-global-filter').on('keyup', function() {
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
            let label = 'e-mail';
            if (total !== 1) label = 'e-mails';
            $('#m-spn-total-label').text(`${total} ${label}`);
        }
    </script>
@endpush

<div class="tab-pane fade show {{ Request::get('mailingtab') ? 'active' : '' }}" id="nav-mailing" role="tabpanel" aria-labelledby="nav-mailing-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            @include('elements.alert')
            <div class="xgrow-card-header">
                <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                    Adicionar aluno manualmente
                </h5>
            </div>
            <form action="{{ route('coupons.mailings.store', ['coupon' => $coupon->id ?? 0]) }}" method="POST" novalidate>
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::text('name', null, [
                                'id' => 'name',
                                'required',
                                'autocomplete' => 'off',
                                'spellcheck' => 'false',
                                'class' => 'mui--is-empty mui--is-untouched mui--is-pristine'
                            ]) !!}
                            {!! Form::label('name', 'Nome') !!}
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::text('email', null, [
                                'id' => 'email',
                                'required',
                                'autocomplete' => 'off',
                                'spellcheck' => 'false',
                                'class' => 'mui--is-empty mui--is-untouched mui--is-pristine'
                            ]) !!}
                            {!! Form::label('email', 'E-mail') !!}
                        </div>
                    </div>
                </div>
                <div clas="row">
                    <div class="col-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::text('notes', null, [
                                'id' => 'notes',
                                'required',
                                'autocomplete' => 'off',
                                'spellcheck' => 'false',
                                'class' => 'mui--is-empty mui--is-untouched mui--is-pristine'
                            ]) !!}
                            {!! Form::label('notes', 'Notas') !!}
                        </div>
                    </div>
                </div>
                <div class="xgrow-card-footer p-3 mt-4">
                    <button id="ipt-save-mailing" class="xgrow-button" type="submit">Salvar</button>
                </div>
            </form>
            <hr>

            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Adicionar lista de alunos
            </h5>
            <div class="mb-3">
                <form action="{{ route('coupons.mailings.upload', ['coupon' => $coupon->id ?? 0]) }}" method="POST" enctype="multipart/form-data" novalidate>
                    {{ csrf_field() }}
                    <p class="xgrow-large-regular">Selecione um <strong>arquivo CSV</strong> conforme este <a href="{{ asset('/xgrow-vendor/assets/files/ModeloArquivoEnvioCupons.csv') }}" target="_blank" style="color: var(--contrast-green);">modelo</a> para enviar o cupom para uma lista de e-mails.</p>
                    <p class="xgrow-large-regular mb-4">O tamanho máximo suportado do arquivo é de <strong>40MB</strong>.</p>
                    <div class="xgrow-form-control">
                        <!-- <label for="file"><i class=" fa fa-upload me-3"></i>Upload</label> -->
                        <input class="xgrow-form-control" type="file" id="file" accept=".csv" name="file">
                    </div>
                    <div class="xgrow-card-footer p-3 mt-4">
                        <button id="ipt-upload-mailing" class="xgrow-button" type="submit">Importar .CSV</button>
                    </div>
                </form>
            </div>
            <hr>
             <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Lista de alunos
            </h5>
            <div class="table-responsive m-t-30">
                <table id="mailing-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Notas</th>
                            <th>E-mail enviado</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
