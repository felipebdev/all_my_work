@push('after-scripts')
    <script>
        $(function () {
            $(document).ready(function () {
                $('#default-email-table').DataTable({
                    dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                        '<"title-table-default d-flex align-self-center justify-content-center me-1">' +
                        '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                        '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"filter-button">' +
                        '<"d-flex flex-wrap mt-2"<B><"create-button mb-2">>>>>' +
                        '<"filter-div mt-2"><"mt-2" rt>' +
                        '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                    ajax: '/emails/ajax/default',

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
                            data: 'subject'
                        },
                        {
                            data: 'subjectUser',
                            render: function (data, type, row) {
                                return data ? data : row.subject;
                            }
                        },
                        {
                            data: 'from'
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                const route = @json(route('emails.customize', ':id'));
                                const url = route.replace(/:id/g, row.id);

                                const menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                        <li><a class="dropdown-item table-menu-item" href="${url}">Customizar</a></li>
                                    </ul>
                                </div>
                            `;

                                return '<div class="d-flex">' + menu + '';
                            },
                        },
                    ],
                    buttons: [],
                    initComplete: function (settings, json) {
                        $('.title-table-default').html(
                            '<h5 class="align-self-center">Emails: <span id="spn-total-label">{{ $default->total() }} emails</span></h5>'
                        );
                    },
                });
            });
        });
    </script>
@endpush

<div class="tab-pane fade show" id="nav-default" role="tabpanel" aria-labelledby="nav-default-tab">

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            <div class="table-responsive m-t-30">
                @if ($errors->any())
                    @include('elements.alert')
                @endif

                <table id="default-email-table"
                       class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                       style="width:100%">
                    <thead>
                    <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                        <th>Tipo do Email</th>
                        <th>Assunto</th>
                        <th>Remetente</th>
                        <th class="no-export"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>