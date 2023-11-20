@extends('templates.xgrow.main')

@push('after-styles')
<link href="{{ asset('xgrow-vendor/assets/css/verify-alert.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const getAllURL = @json(route('coupons.get-all'));
        const getAllPlansURL = @json(route('coupons.get-all-plans'));
        const deleteUrl = @json(route('coupons.destroy', ':id'));
        const verifyDocument = @json($verifyDocument);
        const modelLink = @json(asset('/xgrow-vendor/assets/files/ModeloArquivoEnvioCupons.csv'));
        const recipientStatusMessage = @json($recipientStatusMessage);
    </script>
    <script src="{{ asset('js/bundle/coupons.js') }}"></script>
@endpush

@section('content')
    <div id="couponsPage">
        <router-view></router-view>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
