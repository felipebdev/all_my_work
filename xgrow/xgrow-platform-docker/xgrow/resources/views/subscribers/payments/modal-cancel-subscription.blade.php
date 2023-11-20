{{-- <-- MODAL CANCELAR ASSINATURA --> --}}
<div class="modal-sections modal fade" tabindex="-1" id="modal-subscription-cancel-sub" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="column-first" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="d-flex w-100 justify-content-end p-3 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title">Confirmar cancelamento de assinatura</p>
                </div>
                <div class="modal-body" style="display:unset;padding:30px">
                    <div class="row">
                        <p>Você tem certeza que deseja cancelar a assinatura <strong><span
                                    class="spn-cancel-product"></span></strong> do aluno <strong><span
                                    class="spn-cancel-subscriber"></span></strong>?</p>
                    </div>
                    <br>
                    <div class="row" style="text-align:left;padding:0 30px">
                        <div class="xgrow-form-control mb-3">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input type="text" name="canceled_at" class="custom-datepicker xgrow-datepicker">
                                <label>Data de cancelamento</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Sim, cancelar</button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Não cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
