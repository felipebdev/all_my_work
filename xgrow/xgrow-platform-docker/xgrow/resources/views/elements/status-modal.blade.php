@push('jquery')
    <script>
        function showStatusModal(show = false, type = 'loading') {
            show ? $('#bgStatusModal').show() : $('#bgStatusModal').hide();
            show ? $('#modalStatusModal').show() : $('#modalStatusModal').hide();
            switch (type) {
                case 'loading':
                    $('#statusModalIcon').html('<i class="fas fa-circle-notch fa-spin"></i>');
                    $('#statusModalText').html('Aguarde, estamos carregando as<br>informações...');
                    break;
                case 'saving':
                    $('#statusModalIcon').html('<i class="fas fa-circle-notch fa-spin"></i>');
                    $('#statusModalText').html('Aguarde, Estamos atualizando a entrega de<br>todos alunos que possuem este produto...');
                    break;
                case 'success':
                    $('#statusModalIcon').html('<i class="fas fa-check-circle" style="color:var(--green1)"></i>');
                    $('#statusModalText').html('Informações salvas com sucesso!');
                    break;
                case 'error':
                    $('#statusModalIcon').html('<i class="fas fa-info-circle" style="color:#eb5757"></i>');
                    $('#statusModalText').html('Ocorreu algum problema ao salvar as<br>informações, tente novamente mais tarde.');
                    break;
            }
        }
    </script>
@endpush

@push('after-styles')
    <style>
        .bg {
            background: rgba(0, 0, 0, .5);
            width: 100%;
            height: 100%;
            position: fixed;
            left: 0;
            top: 0;
            display: none;
            z-index: 1;
        }

        @media (min-width: 992px) {
            .modal-md {
                max-width: 600px;
            }
        }
    </style>
@endpush

<div class="bg" id="bgStatusModal">
    <div class="modal-sections modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
         id="modalStatusModal">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="max-width:100% !important;padding:0 0 65px 0;">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close" onclick="showStatusModal()">
                        <i class="fa fa-times" style="font-size:2rem;"></i>
                    </button>
                </div>
                <div class="modal-body d-block">
                    <div class="align-self-center">
                        <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" height="46px">
                    </div>
                    <h5 id="statusModalText"></h5>
                    <div class="fa-7x">
                        <span id="statusModalIcon"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
