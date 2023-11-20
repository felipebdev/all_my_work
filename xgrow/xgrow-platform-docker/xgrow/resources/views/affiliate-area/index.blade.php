@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const affiliatePlatformsUrl = @json(route('active.platforms.affiliate'));
        const affiliateProductsUrl = @json(route('affiliations.products.list', 'platform_id'));
        const affiliateProductsResumeUrl = @json(route('affiliations.list.links', 'platform_id'));
        const affiliateReportSales = @json(route('financial.report.sales.affiliate', 'platform_id'));
        const affiliateBallance = @json(route('affiliate.balance', 'platform_id'));
        const affiliateWithdraws = @json(route('list.withdrawals', 'platform_id'));
        const affiliateDoWithdraw = @json(route('affiliate.withdraw.create', 'platform_id'));
        const affiliationsByStatus = @json(route('affiliations.by.status'));
        const affiliateEventsList = @json(route('affiliations.list.events', 'platform_id'));
        const urlBankInformation = @json(route('get.bank.information.affiliate', ':platformId'));

        const idUser = @json(Auth::user()->id);
        const env = @json(env('APP_ENV'));
        const buyerInfo = @json(route('affiliations.buyer', 'order_number'));
    </script>
    <script src="{{ asset('js/bundle/affiliate-area.js') }}"></script>
@endpush
@section('content')
    <div id="affiliateAreaApp">
        <router-view></router-view>
    </div>
    @include('elements.toast')
@endsection
