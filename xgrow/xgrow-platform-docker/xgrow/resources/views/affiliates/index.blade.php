@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const platformId = @json(Auth::user()->platform_id);
        const affiliatesActiveUrl = @json(route('affiliations.list.all', Auth::user()->platform_id));
        const affiliatesCancelUrl = @json(route('affiliations.contracts.cancel', [
            'platformId' => Auth::user()->platform_id,
            'producer_product_id' => 'producer_product_id'
        ]));
        const affiliatesBlockUrl = @json(route('affiliations.contracts.block', [
            'platformId' => Auth::user()->platform_id,
            'producer_product_id' => 'producer_product_id'
        ]));
        const affiliatesUnblockUrl = @json(route('affiliations.contracts.unblock', [
            'platformId' => Auth::user()->platform_id,
            'producer_product_id' => 'producer_product_id'
        ]));
        const affiliatesFilters = @json(route('affiliations.get.filters', Auth::user()->platform_id));
        const affiliatesShowUrl = @json(route('affiliate.detail', [
            'platformId' => Auth::user()->platform_id,
            'producerProductId' => 'producer_product_id'
        ]));
        const affiliatesUserShowUrl = @json(route('affiliate.user.data', [
            'platformId' => Auth::user()->platform_id,
            'producerId' => 'producer_product_id'
        ]));
        const affiliatesEditUrl = @json(route('update.commission.producer.products', [
            'producerProductId' => 'producer_product_id'
        ]));
        const urlAcceptOrResufe = @json(route('affiliate.change.status', [
            'platformId' => Auth::user()->platform_id,
            'producerProductId' => 'producer_product_id'
        ]));
        const urlRanking = @json(route('affiliate.ranking'));
        const affiliateEventsList = @json(route('affiliations.list.events', Auth::user()->platform_id));
        const affiliateEventsFilters = @json(route('affiliations.events.filters', Auth::user()->platform_id));
        const buyerInfo = @json(route('affiliations.buyer', 'order_number'));
    </script>
    <script src="{{ asset('js/bundle/affiliates.js') }}"></script>
@endpush
@section('content')
    <div id="affiliatesApp">
        <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item active"><a href="/affiliates">Afiliados</a></li>
            </ol>
        </nav>
        <div class="xgrow-tabs nav nav-tabs mb-3">
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('active')" :class="{'active': $store.state.tab === 'active'}">
                Afiliados Ativos
            </a>
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('pending')" :class="{'active': $store.state.tab === 'pending'}">
                Solicitações pendentes
            </a>
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('different-status')" :class="{'active': $store.state.tab === 'different-status'}">
                Solicitações recusadas, bloqueadas ou canceladas
            </a>
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('ranking')" :class="{'active': $store.state.tab === 'ranking'}">
                Ranking dos afiliados
            </a>

            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('events')" :class="{'active': $store.state.tab === 'events'}">
                Eventos
            </a>

        </div>
        <router-view></router-view>
        <status-modal-component :is-open="$store.state.loading" status="loading"></status-modal-component>
    </div>
    @include('elements.toast')
@endsection
