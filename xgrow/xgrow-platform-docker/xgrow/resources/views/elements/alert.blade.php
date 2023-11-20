@if ($message = Session::get('success'))
    <div class="toast xgrow-toast fade show" role="alert" id="success-toast">
        <div class="xgrow-toast-header xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle">Ação feita com sucesso!</strong>
            <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                    data-bs-dismiss="toast" aria-label="Close"
                    onclick="document.getElementById('success-toast').remove()"></button>
        </div>
        <div class="xgrow-toast-body xgrow-toast-body toast-body mt-2">
            <span id="toastMessage">
                <p style="color: white;">{{ $message }}</p>
            </span>
        </div>
    </div>

    {{-- <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="d-sm-flex align-items-center justify-content-start">
            <div class="mg-t-20 mg-sm-t-0">
                <p class="m-0 tx-gray">{{ $message }}</p>
            </div>
        </div>
    </div> --}}
@endif

@if ($message = Session::get('error'))
    <div class="toast xgrow-toast-error fade show" role="alert" id="error-toast">
        <div class="xgrow-toast-header-error xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle">Oops! Um erro aconteceu.</strong>
            <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                    data-bs-dismiss="toast" aria-label="Close"
                    onclick="document.getElementById('error-toast').remove()"></button>
        </div>
        <div class="xgrow-toast-body-error xgrow-toast-body toast-body mt-2">
            <span id="toastMessage">
                <p style="color: white;">{{ $message }}</p>
            </span>
        </div>
    </div>
@endif

@if ($message = Session::get('warning'))
    <div class="toast xgrow-toast-warning fade show" role="alert" id="warning-toast">
        <div class="xgrow-toast-header-warning xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle">Atenção!</strong>
            <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                    data-bs-dismiss="toast" aria-label="Close"
                    onclick="document.getElementById('warning-toast').remove()"></button>
        </div>
        <div class="xgrow-toast-body-warning xgrow-toast-body toast-body mt-2">
            <span id="toastMessage">
                <p style="color: white;">{{ $message }}</p>
            </span>
        </div>
    </div>

    {{-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="d-sm-flex align-items-center justify-content-start">
            <i class="icon ion-alert-circled alert-icon tx-52 tx-warning mg-r-20"></i>
            <div class="mg-t-20 mg-sm-t-0">
                <p class="m-0 tx-gray">{{ $message }}</p>
            </div>
        </div>
    </div> --}}
@endif

@if ($message = Session::get('info'))
    <div class="toast xgrow-toast-info fade show" role="alert" id="info-toast">
        <div class="xgrow-toast-header-info xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle">Atenção!</strong>
            <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                    data-bs-dismiss="toast" aria-label="Close"
                    onclick="document.getElementById('info-toast').remove()"></button>
        </div>
        <div class="xgrow-toast-body-info xgrow-toast-body toast-body mt-2">
            <span id="toastMessage">
                <p style="color: white;">{{ $message }}</p>
            </span>
        </div>
    </div>


    {{-- <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="d-sm-flex align-items-center justify-content-start">
            <div class="mg-t-20 mg-sm-t-0">
                <p class="m-0 tx-gray">{{ $message }}</p>
            </div>
        </div>
    </div> --}}
@endif

@if ($errors->any())
    <div class="toast xgrow-toast-error fade show" role="alert" id="any-toast">
        <div class="xgrow-toast-header-error xgrow-toast-header toast-header text-white border-0">
            <strong class="me-auto" id="toastTitle">Por favor, verifique os erros abaixo.</strong>
            <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                    data-bs-dismiss="toast" aria-label="Close"
                    onclick="document.getElementById('any-toast').remove()"></button>
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
