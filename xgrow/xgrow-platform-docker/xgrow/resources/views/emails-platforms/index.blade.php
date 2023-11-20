@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css"/>
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <!-- end - This is for export functionality only -->

    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item mx-2"><span>Configurações</span></li>
            <li class="breadcrumb-item mx-2"><span>E-mails</span></li>
            <li class="breadcrumb-item active mx-2"><span>Mensagens</span></li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link active" id="nav-custom-tab" data-bs-toggle="tab"
           href="#nav-custom" role="tab" aria-controls="nav-email" aria-selected="true">
           E-mails customizados
        </a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-default-tab" data-bs-toggle="tab"
           href="#nav-default" role="tab" aria-controls="nav-email" aria-selected="false">
           E-mails padrão Xgrow
        </a>
    </div>

    <div class="tab-content" id="nav-tabContent">
        <!-- Tab Custom -->
        @include('emails-platforms.tabs.custom')
        <!-- Tab Default -->
        @include('emails-platforms.tabs.default')
    </div>

    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
