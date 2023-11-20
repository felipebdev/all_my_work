@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css"/>
    <style>
        .status-item {
            border-radius: .75rem;
            padding: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .status-item.available {
            border: 1px solid var(--sm-card-green);
            color: var(--sm-card-green);
        }

        .status-item.pending {
            border: 1px solid var(--sm-card-yellow);
            color: var(--sm-card-yellow);
        }

        .status-item.error {
            border: 1px solid var(--sm-card-red);
            color: var(--sm-card-red);
        }

        .text-right {
            text-align: right !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>

    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

    <script>
        datatable = $('#downloads-table').DataTable({
            dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                '<"title-table d-flex align-self-center justify-content-center me-1">' +
                '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                '<"d-flex flex-wrap align-items-center justify-content-center mb-2"' +
                '>>>' +
                '<"filter-div mt-2"><"mt-2" rt>' +
                '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
            ajax: {
                url: '{{ route('api.downloads.getall') }}',
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
            order: [[0, "desc"]],
            columns: [
                {
                    data: 'created_at',
                    name: 'created_at',
                    type: 'date',
                    render: function (data, type) {
                        if (type === 'sort') {
                            return data;
                        }
                        return (data != null) ? moment(data).format('DD/MM/YYYY HH:mm:ss') : '';
                    },
                },
                {
                    data: 'status',
                    render: function (data, type, row) {
                        if (data === 'pending') return '<p class="status-item pending">Processando</p>';
                        if (data === 'completed') return '<p class="status-item available">Disponível</p>';
                        if (data === 'failed') return '<p class="status-item error">Falha ao gerar arquivo</p>';
                    }
                },
                {
                    data: 'period',
                    searchable: false,
                },
                {
                    data: 'filesize',
                    searchable: false,
                    render: function (data, type, row) {
                        if (!parseInt(data)) {
                            return '0 bytes';
                        }
                        return bytesToSize(data);

                    }
                },
                {
                    data: 'url',
                    searchable: false,
                    className: 'text-right',
                    render: function (data, type, row) {
                        const fileDate = moment(row.created_at);
                        const todayDate = moment();
                        const days = todayDate.diff(fileDate, 'days');
                        const url = row.url;
                        const filename = row.filename;
                        const zero = row.filesize;
                        const txt = zero !== '0' ? `onclick="requestFile('${url}', '${filename}', ${days})"` : ``;
                        return (row.status === 'completed') ?
                            `<button style="background:transparent;border:none;color:var(--card-font-color)" ${txt}>
                                <i class="fa fa-download"></i> ${generateFilename(filename)}
                            </button>` :
                            '-';
                    }
                },
            ],
            initComplete: function (settings, json) {
                $('.title-table').html(
                    '<h5 class="align-self-center">Lista de downloads disponíveis</span></h5>'
                );
                $('.dataTables_filter input').attr('placeholder', 'Buscar');
                $('.filter-button').html();
                $('.global-search').html(`
                    <div class="xgrow-input me-1" style="background-color: var(--input-bg); height: 40px;" >
                        <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                        <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                `);
                $('.filter-div').html();
            },
        });

        function generateFilename(text) {
            text = text.split('.');
            text = text[0].split('_');
            let str = '';
            for (let i = 0; i < text.length - 1; i++) {
                if (i < 3) {
                    if (i === 2) {
                        str += text[i];
                    } else {
                        str += text[i] + '_';
                    }
                } else {
                    str += '_' + text[i];
                }
            }
            return str;
        }

        function bytesToSize(bytes) {
            let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes === 0) return '0 Byte';
            let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        }

        function makelinkDownload(url, filename) {
            let aEl = document.createElement('a');
            aEl.setAttribute('href', url);
            aEl.setAttribute('download', filename);
            aEl.style.display = 'none';
            document.body.appendChild(aEl);
            aEl.click();
            document.body.removeChild(aEl);
        }

        function requestFile(url, filename, days) {
            if (days <= 1) {
                makelinkDownload(url, filename)
                return;
            }

            const loading = $('#customLoading');
            const codeReceive = $('#codeReceive');
            const exportEmail = $('#exportEmail');
            const exportModal = $('#exportModal');
            const btnExportModal = $('#btnExportModal');
            loading.removeClass('d-none');

            axios.post('/api/action-send-code')
                .then((res) => {
                    codeReceive.val('');
                    exportEmail.text(res.data.email);
                    exportModal.modal('show');
                    loading.addClass('d-none');
                    btnExportModal.attr('disabled', 'true');

                    codeReceive.on("input", function () {
                        const inputLenght = $(this).val().length;
                        if (inputLenght > 7) {
                            btnExportModal.removeAttr('disabled');
                            $('#spnUrl').text(url);
                            $('#spnFile').text(filename);
                        } else {
                            btnExportModal.attr('disabled', 'true');
                        }
                    });
                })
                .catch((error) => {
                    errorToast('Algum erro aconteceu!', `${error.response.data.message}`);
                });
        }

        function sendCode() {
            const codeReceive = $('#codeReceive');
            const exportModal = $('#exportModal');
            const btnExportModal = $('#btnExportModal');

            if (codeReceive.val().trim() === '') {
                errorToast('Algum erro aconteceu!', 'Digite o PIN recebido em seu e-mail.');
                return false;
            } else {
                axios.post('/api/verify-pin-code', {
                    code: codeReceive.val()
                })
                    .then((sendRes) => {
                        successToast('Iniciando download!', sendRes.data.message);
                        makelinkDownload($('#spnUrl').text(), $('#spnFile').text());
                        exportModal.modal('hide');
                    })
                    .catch((sendError) => {
                        errorToast('Algum erro aconteceu!', `${sendError.response.data.message}`);
                    });
            }
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Listas exportadas</span></li>
        </ol>
    </nav>

    @include('elements.alert')

    <div class="xgrow-card card-dark mb-3">
        <div class="xgrow-card-body pt-3">
            <div class="table-responsive m-t-30">
                <table id="downloads-table"
                       class="xgrow-table table text-light table-responsive dataTable overflow-auto"
                       style="width: 100%">
                    <thead>
                    <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                        <th>Data de criação</th>
                        <th>Status</th>
                        <th>Período</th>
                        <th>Tamanho</th>
                        <th class="text-right">Arquivo (download)</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('elements.code-action-modal')
    @include('elements.toast')
@endsection
