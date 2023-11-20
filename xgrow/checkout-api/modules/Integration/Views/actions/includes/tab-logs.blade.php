@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script>
        $(function() {
            let datatable;
            datatable = $('#webhooks-logs-table').DataTable({
                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                    '<"d-flex flex-wrap"<B>>>>>' +
                    '<"filter-div mt-2"><"mt-2" rt>' +
                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                ajax: {
                    url: '{{ route('apps.integrations.logs.index', ['integration' => $integration->id]) }}',
                    data: function (d) {
                    }
                },

                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página', "Todos os registros"]
                ],
                "order": [],
                language: {
                    url: '{{ asset('js/datatable-translate-pt-BR.json') }}',
                },
                columns: [
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            return (data === 'success') 
                                ? '<i class="fa fa-check-circle" title="Sucesso"></i>'
                                : '<i class="fa fa-times-circle" title="Falha"></i>';
                        },
                    },
                    {
                        data: 'createdAt.milliseconds',
                        render: function(data, type, row) {
                            const date = (data) ? new Date(Number(data)) : undefined;
                            return (date) ? formatDateTimePTBR(date) : '-';
                        },
                    },
                    {
                        data: 'metadata.event',
                        render: function(data, type, row) {
                            return (data) ? eventsLang[data] || '-' : '-';
                        }
                    },
                    {
                        data: 'request.url',
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            const reprocessMenu = (data.status !== 'success')
                                ? `<li>
                                        <a class="dropdown-item table-menu-item btn-reprocess-log" 
                                            href="javascript:void(0)" 
                                            data-app="${data.metadata.app_id}"
                                            data-id="${data._id.oid}">
                                            Reprocessar
                                        </a>
                                    </li>`
                                : '';

                            const menu = `
                                <div class="dropdown">
                                    <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item table-menu-item btn-open-log-modal" 
                                                href="javascript:void(0)"
                                                data-app="${data.metadata.app_id}"
                                                data-id="${data._id.oid}">
                                                Ver detalhes
                                            </a>
                                        </li>
                                        ${reprocessMenu}
                                    </ul>
                                </div>
                            `;

                            return '<div class="d-flex">' + menu + '';
                        },
                    },
                ],
                buttons: [
                ],
                initComplete: function(settings, json) {
                    $('.btn-open-log-modal').click(function() {
                        const id = $(this).data('id');
                        const app = $(this).data('app');
                        const headers = { headers: { 'X-Requested-With': 'XMLHttpRequest' } };
                        axios.get(`/apps/integrations/${app}/logs/${id}`, headers)
                            .then(({ data }) => {
                                const status = (data.status === 'success') ? 'Sucesso' : 'Falha';
                                const event = (data.metadata.event) ? eventsLang[data.metadata.event] : 'Desconhecido';
                                const date = (data.createdAt.$date.$numberLong) 
                                    ? formatDateTimePTBR(new Date(Number(data.createdAt.$date.$numberLong))) 
                                    : undefined;

                                $('#txt-log-id').text(data._id.$oid);
                                $('#txt-log-status').text(status);
                                $('#txt-log-date').text(date);
                                $('#txt-log-event').text(event);
                                $('#txt-log-req-url').text(data.request.url);
                                $('#txt-log-req-method').text(data.request.method);
                                $('#txt-log-req-header').text(JSON.stringify(data.request.headers, null, "\t"));
                                $('#txt-log-req-payload').text(JSON.stringify(data.request.payload, null, "\t"));
                                $('#txt-log-res-code').text(data.response.code);
                                $('#txt-log-res-message').text(data.response.message);
                                $('#txt-log-res-payload').text(JSON.stringify(data.response.payload, null, "\t"));

                                $('#modal-webhook-logs-info').modal('show');
                            })
                            .catch(error => {
                                errorToast('Erro', 'Erro ao carregar detalhes do log.');
                            });
                    });

                    $('.btn-reprocess-log').click(function(e) {
                        e.preventDefault();
                        const id = $(this).data('id');
                        const app = $(this).data('app');
                        const headers = { headers: { 'X-Requested-With': 'XMLHttpRequest' } };
                        axios.post(`/apps/integrations/${app}/logs/${id}`, {}, headers)
                            .then(({ data }) => {
                                successToast('Sucesso', 'O webhook foi enviado para a fila para ser reprocessado.');
                            })
                            .catch(error => {
                                errorToast('Erro', 'Não foi possível reprocessar o webhook.');
                            });
                    });
                },
                drawCallback: function(settings) {
                }
            });
        });
    </script>
@endpush

<div class="xgrow-card card-dark">
    <div class="xgrow-card-header align-items-center justify-content-between flex-wrap">
        <div>
            <h5><strong>{{ Str::ucfirst($integration->type) }}</strong></h5>
            <small>{{ $integration->description }}</small>
        </div>
    </div>
    <div class="xgrow-card-body">
        <div class="table-responsive m-t-30">
            <table id="webhooks-logs-table"
                class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                style="width:100%">
                <thead>
                    <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                        <th>Status</th>
                        <th>Data</th>
                        <th>Evento</th>
                        <th>URL</th>
                        <th class="no-export"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>