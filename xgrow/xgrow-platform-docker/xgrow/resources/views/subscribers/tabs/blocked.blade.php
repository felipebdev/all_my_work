@push('after-scripts')
    <script>
        function listBlocked() {
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
                },
            };

            let createdAtRange = '';
            let lastAccessRange = '';
            let datatable;

            datatable = $('#blocked-table').DataTable({
                destroy: true,
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table blocked d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"filter-button subs-blocked">' +
                    '<"d-flex flex-wrap mt-2"<B>>>>>' +
                    '<"filter-div subs-blocked mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',

                ajax: {
                    url: '{{ route('subscribers.blocked.user.index') }}',
                    data: function (d) {
                        d.nameFilter = $('#ipt-name-blocked').val();
                        d.situationFilter = $('#slc-situation-filter-blocked option:selected').val();
                    }
                },
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página', 'Todos os registros']
                ],
                language: {
                    'url': "{{ asset('js/datatable-translate-pt-BR.json') }}",
                },
                'columnDefs': [{
                    'visible': false,
                    'searchable': false,
                }],
                order: [],
                columns: [
                    {
                        data: 'userName',
                    },
                    {
                        data: 'userEmail'
                    },
                    {
                        data: 'totalTimesBlocked',
                        render: function (data, type, row) {
                            return `${row.totalTimesBlocked}/${row.blockedLimit}`;
                        }
                    },
                    {
                        data: 'createdAt',
                        type: 'date',
                        render: function (data, type) {
                            return (data != null) ? formatDateTimePTBR(data) : '';
                        },
                    },
                    {
                        data: 'isLocked',
                        render: function (data, type) {
                            return (Boolean(data)) ? 'Bloqueado' : 'Liberado';
                        },
                    },
                    {
                        data: null,
                        searchable: false,
                        render: function (data, type, row) {
                            const modalData = btoa(JSON.stringify(row.accesses) || []);
                            let menu = `
                                    <div class="dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">`;

                            if (row.isLocked) {
                                menu += `<li><a class="dropdown-item table-menu-item" href="javascript:modalFreeAccess('${row.userName}', ${row.userId})">Liberar acesso</a></li>`;
                            } else {
                                menu += `<li><a class="dropdown-item table-menu-item" href="javascript:modalBanAccess('${row.userName}', ${row.userId})">Banir acesso</a></li>`;
                            }

                            menu += `<li><a class="dropdown-item table-menu-item" href="javascript:modalBlockList('${modalData}')">Ver lista de bloqueios</a></li>
                                        </ul>
                                    </div>`;

                            return '<div class="d-flex">' + menu + '';
                        },
                    },
                ],
                buttons: [
                ],
                initComplete: function (settings, json) {
                    $('.title-table.blocked').html(
                        '<h5 class="align-self-center">Alunos bloqueados: <span id="spn-total-label-blocked"></span></h5>'
                    );
                    $('.filter-button.subs-blocked').html(`
                        <div class="d-flex align-items-center py-2">
                            <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilterBlocked"
                                aria-bs-expanded="false" aria-bs-controls="collapseFilterBlocked"
                                class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                            <p>Filtros avançados! <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                            </button>
                        </div>
                    `);
                    $('.filter-div.subs-blocked').html(`
                        <div class="mb-3 collapse" id="collapseFilterBlocked">
                            <div class="filter-container">
                                <div class="p-2 px-3">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                                                <input type="text" class="form-control" id="ipt-name-blocked"
                                                    style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; min-width: 230px; color: var(--contrast-green)"
                                                    autocomplete="off">
                                                <label for="ipt-name-blocked">Nome</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="xgrow-form-control mb-2">
                                                <select id="slc-situation-filter-blocked" class="xgrow-select w-100" name="situation-filter-blocked">
                                                    <option value="">Status</option>
                                                    <option value="false">Liberado</option>
                                                    <option value="true">Bloqueado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`);

                    $('#ipt-name-blocked').on('keyup', function () {
                        datatable.ajax.reload();
                    });

                    $('#slc-situation-filter-blocked').on('change', function () {
                        datatable.ajax.reload();
                    });
                },
                drawCallback: function (settings) {
                    const total = datatable.page.info().recordsDisplay || 0;
                    setTotalLabel(total, "#spn-total-label-blocked");
                }
            });
        };
    </script>
@endpush

<div class="tab-pane fade show" id="nav-blocked" role="tabpanel" aria-labelledby="nav-blocked-tab">
    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            @if (count($plans) > 0)
                <div class="table-responsive m-t-30">
                    @if ($errors->any())
                        @include('elements.alert')
                    @endif

                    <table id="blocked-table"
                           class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                           style="width:100%">
                        <thead>
                        <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Nº de bloqueios</th>
                            <th>Data do bloqueio</th>
                            <th>Situação</th>
                            <th class="no-export"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    Antes de criar um aluno é necessário criar um plano. Acesse o menu "Alunos > Planos"
                    ou clique <a href="/plans/create">aqui</a>.
                </div>
            @endif
        </div>
    </div>
</div>

@include('subscribers.modal.block-list')
@include('subscribers.modal.confirm')
