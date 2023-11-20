@if (count($errors) > 0)
    <!-- Form Error List -->

    <div class="toast xgrow-toast-error fade show" role="alert" id="form-error-toast">
        <div class="xgrow-toast-header-error xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle">Oops! Um erro aconteceu.</strong>
            <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                data-bs-dismiss="toast" aria-label="Close"
                onclick="document.getElementById('form-error-toast').classList.remove('show')"></button>
        </div>
        <div class="xgrow-toast-body-error xgrow-toast-body toast-body mt-2">
            <span id="toastMessage">
                <ul style="margin-left: -30px; color: white;">
                    @foreach ($errors->all() as $error)
                        <li class="text-left">{{ $error }}</li>
                    @endforeach
                </ul>
            </span>

        </div>
    </div>
@endif
