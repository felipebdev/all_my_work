@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
@endpush

@push('after-styles')
    <style>
        #customLoading {
            background: rgba(0, 0, 0, .9);
            height: 50%;
            width: 50%;
            top: 25%;
            left: 25%;
            bottom: 25%;
            right: 25%;
            position: fixed;
            z-index: 2;
            color: #FFFFFF;
        }

        #customLoading > p {
            font-size: 1.25rem;
        }
    </style>
@endpush

<div class="modal-sections modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModal" aria-hidden="true"
     data-bs-backdrop="static">
    <span id="spnUrl" class="d-none"></span>
    <span id="spnFile" class="d-none"></span>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="exportModalTitle">Exportar dados</p>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <p>Enviamos um código de confirmação para o email <span id="exportEmail"></span>. Digite o código para concluir a
                            exportação.</p>
                    </div>
                    <div class="col-sm-12 mt-3" style="text-align: left">
                        <div
                            class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="codeReceive" autocomplete="off" spellcheck="false"
                                   class="mui--is-empty mui--is-pristine mui--is-touched" name="codeReceive" required
                                   type="number">
                            <label for="codeReceive">Código</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="submit" class="btn btn-success" id="btnExportModal" onclick="sendCode()">
                    Exportar
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="customLoading" class="d-flex align-items-center justify-content-center flex-column d-none">
    <div class="fa-6x">
        <i class="fas fa-circle-notch fa-spin"></i>
    </div>
    <p>Enviando o código para seu email.</p>
</div>
