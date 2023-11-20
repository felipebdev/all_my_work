@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script>
        const subscriberRoute = @json(route('subscribers.next.user.index'));
        const blockedSubscriberRoute = @json(route('subscribers.next.blocked.user.index'));
        const updateStatusURL = @json(route('subscribers.next.blocked.user.update'));
        const exportRoute = @json(route('report.download.subscribers.user'));
        const productsRoute = @json(route('products.list'));
        const plansRoute = @json(route('plans.getAllPlans'));
        const getAllPlansURL = @json(route('coupons.get-all-plans'));
        const resendUserDataURL = @json(route('subscribers.next.resend-data', ':id'));
        const deleteUserURL = @json(route('subscribers.next.destroy', ':id'));
        const storeUserURL = @json(route('subscribers.next.store'));

        const getUserDataURL = @json(route('subscribers.next.show',':id'));
        const updateUserDataURL = @json(route('subscribers.next.update',':id'));
        const sendAccessDataURL = @json(route('subscribers.next.resend.access.data',':id'));

        const getUserProductsURL = @json(route('subscribers.next.subscriptions.products', ':id'));
        const updateUserProductStatusURL = @json(route('subscribers.next.subscription.change.product'));

        const cancelRevertURL = @json(route('subscribers.next.subscriptions.refund'));
        const resendProofPurchaseURL = @json(route('subscribers.next.subscriptions.send.buyed.proof', ':paymentId'));
        const resendBilletURL = @json(route('subscribers.next.subscriptions.resend.boleto', ':id'));
        const resendReversalReceiptURL = @json(route('subscribers.next.subscriptions.send.refund', ':id'));
        const downloadReversalReceiptURL = @json(route('subscribers.next.subscriptions.refund.proof', ':id'));
        const cancelURL = @json(route('subscribers.next.subscriptions.cancel.not-refund', ':id'));

        const getUserPaymentHistoryURL = @json(route('subscribers.next.subscriber.payments.index', ':id'));
    </script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('js/bundle/subscribers.js') }}"></script>
@endpush

@section('content')
    <div id="subscribersPage">
        <router-view></router-view>
    </div>
    @include('elements.code-action-modal')
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
