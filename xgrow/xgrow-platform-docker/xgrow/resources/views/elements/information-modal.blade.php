@push('jquery')
    <script>
        function showInfoModal(show = false, cookie = '', clearCookie = false) {
            if (show && cookie !== '' && !clearCookie) {
                /** Quando executa o modal pela primeira vez */
                const firstTime = localStorage.getItem(cookie)
                if (!!(!firstTime)) {
                    show ? $('#bgStatusModal').show() : $('#bgStatusModal').hide();
                    show ? $('#modalStatusModal').show() : $('#modalStatusModal').hide();
                }
            }

            if (!show && cookie !== '' && clearCookie) {
                /** Quando clica em não mostrar novamente */
                localStorage.setItem(cookie, 'true');
                show ? $('#bgStatusModal').show() : $('#bgStatusModal').hide();
                show ? $('#modalStatusModal').show() : $('#modalStatusModal').hide();
            }

            if (!show && cookie !== '' && !clearCookie) {
                /** Quando clica em fechar e quer que exiba novamente */
                show ? $('#bgStatusModal').show() : $('#bgStatusModal').hide();
                show ? $('#modalStatusModal').show() : $('#modalStatusModal').hide();
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

        .btn-modal-success {
            background-color: var(--green1);
            border-color: var(--green1);
            font-size: .875rem;
            font-weight: 700;
            padding: 0.625rem 1.75rem;
            width: -moz-fit-content;
            width: fit-content;
            cursor: pointer;
        }

        .btn-modal-success:hover {
            background: #c4cf00 !important;
            border-color: #c4cf00;
            outline: none !important;
            cursor: pointer;
        }

        .modal-content {
            padding-bottom: 2.5rem !important;
        }

        .btn-modal-link, .btn-modal-link:hover {
            background-color: transparent;
            border: none;
            color: #909090;
            outline: none;
            cursor: pointer;
        }

    </style>
@endpush

<div class="bg" id="bgStatusModal">
    <div class="modal-sections modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
         id="modalStatusModal">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="max-width:100% !important;padding:0 0 65px 0;">
                <div class="modal-header pt-5">
                </div>
                <div class="modal-body d-block">
                    <div class="align-self-center">
                        <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" height="46px">
                    </div>
                    <h5 class="mt-4">Atenção</h5>
                    <p class="mb-3">Neste sábado (24/09) realizaremos uma atualização na Xgrow das 00:30 às 3:00am e
                        durante esse
                        período a plataforma, checkout e learning área poderão sofrer instabilidades de acesso.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn btn-modal-link"
                            onclick="showInfoModal(false, '8ca48a02-3655-45c8-8367-82d60b3b9afe', true)">
                        Não mostrar novamente
                    </button>
                    <button class="btn btn-success btn-modal-success"
                            onclick="showInfoModal(false, '8ca48a02-3655-45c8-8367-82d60b3b9afe', false)">Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
