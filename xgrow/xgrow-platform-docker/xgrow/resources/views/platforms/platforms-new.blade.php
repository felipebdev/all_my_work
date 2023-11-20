@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const searchPlatformUrl = @json(route('search.all.platforms'));
        const changePlatformThumbUrl = @json(route('change.platform.thumb'));
        const verifyDocument = @json($verifyDocument);
        const recipientStatusMessage = @json($recipientStatusMessage);
    </script>
    <script src="{{ asset('js/bundle/platforms.js') }}"></script>
@endpush
@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/dashboard_index.css') }}" rel="stylesheet">
@endpush
<div id="platforms">
    <xgrow-breadcrumb :items="breadcrumbs"></xgrow-breadcrumb>

    <verify-document v-if="verifyDocument" :description="recipientStatusMessage"></verify-document>

    <status-modal-component :is-open="loading.active" :status="loading.status"></status-modal-component>

    <form method="POST" action="{{ route('choose.platform') }}" id="formPlatform">
        <input id="iptPlatform" type="hidden" name="platform">
        <input id="iptRedirect" type="hidden" name="redirect">
        {{ csrf_field() }}
    </form>

    <xgrow-tab id="nav-tabContent">
        <template v-slot:header>
            <xgrow-tab-nav :items="tabs.items" id="nav-tab" :start-tab="activeScreen" @change-page="changePage">
            </xgrow-tab-nav>
        </template>
        <template v-slot:body>
            @include('platforms.tabs.platforms.own')
            @include('platforms.tabs.platforms.collaboration')
        </template>
    </xgrow-tab>
    @include('elements.toast')
</div>
