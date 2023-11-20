@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script>
        function resendAccessData(url_to_send) {
            if (!confirm(`Confirma o reenvio dos dados de acesso para o assinante?`)) {
                return false;
            }

            successToast('Reenviando os dados.', 'Estamos reenviando os dados para o assinante.');

            $.ajax({
                type: 'GET',
                url: url_to_send,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    successToast('Dados reenviados!', `${data.message}`);
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!', `${data.responseJSON.message}`);
                },
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
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- end - This is for export functionality only -->

    <script>
        function requestFile(type) {
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

                    codeReceive.on("input", function() {
                        const inputLenght = $(this).val().length;
                        if (inputLenght > 7) {
                            btnExportModal.removeAttr('disabled');
                        } else {
                            btnExportModal.attr('disabled', 'true');
                        }
                    });
                    btnExportModal.attr('data-type', type);
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
                        if (btnExportModal.data('type') === 'pdf') {
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(btnTypeX, eX, dtX, nodeX, configX);
                        }
                        if (btnExportModal.data('type') === 'csv') {
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(btnTypeX, eX, dtX, nodeX, configX);
                        }
                        if (btnExportModal.data('type') === 'excel') {
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(btnTypeX, eX, dtX, nodeX, configX);
                        }
                        exportModal.modal('hide');
                    })
                    .catch((sendError) => {
                        errorToast('Algum erro aconteceu!', `${sendError.response.data.message}`);
                    });
            }
        }

        function setTotalLabel(total = 0, idLabel) {
            let label = 'alunos';
            if (total === 1) label = 'aluno';
            $(idLabel).text(`${total} ${label}`);
        }

        function changeStatus(id, component) {
            const status = (component[0].checked) ? 'active' : 'canceled';
            $.ajax({
                url: `/subscribers/${id}/status`,
                type: 'PUT',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'status': status
                },
                success: function(data) {
                    successToast('Registro alterado!', 'Registro feito com sucesso.');
                },
                error: function(data) {
                    errorToast('Erro', `Houve um erro ao alterar o registro: ${data.responseJSON.message}`);
                }
            });
        }
    </script>

@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active"><a href="/subscribers">Alunos</a></li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link active" id="nav-subscriber-tab" data-bs-toggle="tab"
           href="#nav-subscriber" role="tab" aria-controls="nav-subscriber-tables" aria-selected="true">
            Alunos
        </a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-blocked-tab" data-bs-toggle="tab" href="#nav-blocked" role="tab"
           aria-controls="nav-subscriber-tables" aria-selected="false" onclick="listBlocked()">
            Alunos bloqueados
        </a>
    </div>

    <div class="tab-content" id="nav-tabContent">
        {{-- REGULAR SUBSCRIBERS TAB --}}
        @include('subscribers.tabs.subscribers')

        {{-- BLOCKED SUBSCRIBERS TAB --}}
        @include('subscribers.tabs.blocked')
    </div>
    @include('elements.code-action-modal')
    @include('elements.confirmation-modal')
    @include('elements.toast')

@endsection
