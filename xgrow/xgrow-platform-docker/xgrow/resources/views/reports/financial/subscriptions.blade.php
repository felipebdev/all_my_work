@extends('templates.xgrow.main')

@push('after-styles')
    <style>
        .form-group #search-field {
            height: 40px;
        }

        .form-group span {
            top: 7px !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const getSubscriptionsURL = @json(route('reports.financial.subscriptions'));
        const getProductsList = @json(route('products.list'));
        const generateReportURL = @json(route('financial.report.sales.export.data'));
        const getBankListURL = @json(route('default.get.banks'));
        const postRefundURL = @json(route('api.checkout.refund'));
        const sendBuyedProofURL = @json(route('api.send.buyed.proof', ':paymentId'));
        const sendRefundProofURL = @json(route('api.send.refund', ':paymentId'));
        const getRefundProofDocumentURL = @json(route('api.get.refund.proof', ':paymentId'));
        const resendBoletoURL = @json(route('api.resend.boleto', ':paymentId'));

    </script>
    <script src="{{ asset('js/bundle/subscriptions.js') }}"></script>
@endpush

@section('content')
    <div id="subscriptionsPage">
        <router-view></router-view>
    </div>
    @include('elements.toast')
@endsection
