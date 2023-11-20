<div class="modal-sections modal fade" id="modal-integration-delete" tabindex="-1"
        aria-labelledby="modal-integration-delete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-header">
                <p class="modal-title">Excluir integração</p>
            </div>
            <div class="modal-body">
                Você tem certeza que deseja excluir esta integração?
            </div>
            <div class="modal-footer">
                <form id="frm-modal-delete" action="" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-success" aria-label="Close">
                        Sim, excluir
                    </button>
                </form>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                    Não, manter
                </button>
            </div>
        </div>
    </div>
</div>