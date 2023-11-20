<style>
    .xgrow-toast-error, .xgrow-toast-header-error {
        background: #e22222;
    }

    .xgrow-toast-header-error {
        background: #e22222;
        display: flex;
        justify-content: space-between;
    }

    .toast {
        width: 250px !important;
    }
</style>

<div aria-live="polite" aria-atomic="true" class="w-100">
    <div class="xgrow-toast toast text-white border-0" id="dialogToast" role="alert"
         aria-live="assertive" aria-atomic="true" style="position: absolute; top: 20px; right: 20px;">
        <div class="xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle"></strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="xgrow-toast-body toast-body"><span id="toastMessage"></span></div>
    </div>
</div>
