@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
    <script>
        $(function () {
            (function jumpToTab() {
                $(document.location.hash).each( function () {
                    $(this).tab('show');
                });
            })();
        })
    </script>
@endpush

@push('after-styles')
    <link href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-to-pdfmake/browser.js"></script>
    <!-- end - This is for export functionality only -->
@endpush

@section('content')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Vendas</span></li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
{{--        <a class="xgrow-tab-item nav-item nav-link active" id="nav-boleto-tab" data-bs-toggle="tab"--}}
{{--           href="#nav-boleto" role="tab" aria-controls="nav-boleto" aria-selected="true">Boleto</a>--}}

        <a class="xgrow-tab-item nav-item nav-link active" id="nav-subscription-tab" data-bs-toggle="tab"
           href="#nav-subscription" role="tab" aria-controls="nav-subscription" aria-selected="true">Assinatura</a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-nolimit-tab" data-bs-toggle="tab"
           href="#nav-nolimit" role="tab" aria-controls="nav-nolimit" aria-selected="false">Sem limite</a>

{{--        <a class="xgrow-tab-item nav-item nav-link" id="nav-access-tab" data-bs-toggle="tab"--}}
{{--           href="#nav-access" role="tab" aria-controls="nav-access" aria-selected="false">Acessos</a>--}}

    </div>

    <div class="tab-content" id="nav-tabContent">
{{--        @include('ruler.tabs._tab-boleto')--}}
        @include('ruler.tabs._tab-subscription')
        @include('ruler.tabs._tab-nolimit')
{{--        @include('ruler.tabs._tab-access')--}}
    </div>


    @include('elements.alert')
@endsection
