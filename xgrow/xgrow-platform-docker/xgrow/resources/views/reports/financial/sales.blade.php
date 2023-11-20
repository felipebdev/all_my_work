@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-to-pdfmake/browser.js"></script>
    <script>
        const getTransactionsURL = @json(route('reports.financial.transactions'));
        const getTransactionsDetailsURL = @json(route('reports.financial.transactions.details', ':paymentId'));
        const getNoLimitURL = @json(route('reports.financial.nolimit.transactions'));
        const getProductsList = @json(route('products.list'));
        const postRefundURL = @json(route('api.checkout.refund'));
        const getBankListURL = @json(route('default.get.banks'));
        const generateReportURL = @json(route('financial.report.sales.export.data'));
        const sendRefundProofURL = @json(route('api.send.refund', ':paymentId'));
        const getRefundProofDocumentURL = @json(route('api.get.refund.proof', ':paymentId'));
        const sendBuyedProofURL = @json(route('api.send.buyed.proof', ':paymentId'));
        const resendBoletoURL = @json(route('api.resend.boleto', ':paymentId'));
        const cancelRecurrenceURL = @json(route('subscriptions.cancel.order_number', ':orderNumber'));
        const retrievePayment = @json(route('reports.financial.retry.payment', ':paymentId'));
        const plansUrl = @json(route('product.links.list.plans', ':productId'))
    </script>
    <script src="{{ asset('js/bundle/sales.js') }}"></script>
@endpush

@section('content')
    <div id="salesPage">
        <router-view></router-view>
    </div>
    @include('elements.toast')
@endsection
