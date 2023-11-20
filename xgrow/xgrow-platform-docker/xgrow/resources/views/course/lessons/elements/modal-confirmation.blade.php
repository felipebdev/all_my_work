<div class="modal-sections modal fade" tabindex="-1" id="modalConfirmation" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click.prevent="confirmationModal.cancelFunction"></button>
            </div>
            <div class="modal-body d-block">
                <div class="modal-body-content d-flex flex-column align-items-center">
                    <i class="fas custom-alert-symbol" :class=[confirmationModal.symbol]></i>
                    <p class="custom-alert-title">[[ confirmationModal.title ]]</p>
                    <p class="custom-alert-text">[[ confirmationModal.text ]]</p>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center mt-2 mb-3">
                <button type="button" class="course-button btn btn-outline-success"
                        data-bs-dismiss="modal" id="modalConfirmationCancel" @click.prevent="confirmationModal.cancelFunction">
                    [[ confirmationModal.cancelText ]]
                </button>
                <button type="button" class="xgrow-button course-button border-light" id="modalConfirmationSave"
                        @click.prevent="confirmationModal.confirmFunction" data-bs-dismiss="modal">
                    [[ confirmationModal.confirmText ]]
                </button>
            </div>
        </div>
    </div>
</div>
