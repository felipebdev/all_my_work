@extends('templates.xgrow.main')

@push('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(function() {
            $('.xgrow-datepicker').datepicker({
                format: 'dd/mm/yyyy',
                startDate: new Date()
            });
        });

        function resendMail(urlToSend) {
            $.ajax({
                type: 'POST',
                url: urlToSend,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    successToast('Reenvio feito.', data.message);
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!', `Veja mais em: ${data.responseJSON.message}`);
                },
            });
        }

        function destroyMail(urlToSend) {
            $.ajax({
                type: 'DELETE',
                url: urlToSend,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#mailing-table').DataTable().ajax.reload();
                    successToast('E-mail apagado.', data.message);
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!', `Veja mais em: ${data.responseJSON.message}`);
                },
            });
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item mx-2"><a href="/plans">Produtos</a></li>
            <li class="breadcrumb-item mx-2"><a href="/coupons">Cupons</a></li>
            <li class="breadcrumb-item active mx-2">
                <span>Adicionar/Editar Cupom</span>
            </li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link {{ !Request::get('mailingtab') ? 'active' : '' }}" id="nav-coupon-tab"
            data-bs-toggle="tab" href="#nav-coupon" role="tab" aria-controls="nav-coupon" aria-selected="true">Cupom</a>

        @if ($coupon->id > 0)
            <a class="xgrow-tab-item nav-item nav-link {{ Request::get('mailingtab') ? 'active' : '' }}"
                id="nav-mailing-tab" data-bs-toggle="tab" href="#nav-mailing" role="tab" aria-controls="nav-coupon"
                aria-selected="false">Mailing</a>
        @endif
    </div>

    <div class="tab-content" id="nav-tabContent">
        @include('coupons._tab-coupon')

        @if ($coupon->id > 0)
            @include('coupons._tab-mailing')
        @endif
    </div>
    @include('elements.toast')
@endsection
