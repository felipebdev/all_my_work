@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const platformId = @json(Auth::user()->platform_id);
        const affiliatesActiveUrl = @json(route('affiliations.list.all', Auth::user()->platform_id));
        const timeAndFees = @json(route('documents.api.timeFees'));
        const getBankDetails = @json(route('my.data.get.bank.details'));
        const getIdentity = @json(route('my.data.get.identity'));
        const storeIdentity = @json(route('my.data.store.identity'));
        const getAddressUrl = @json(route('my.data.get.address'));
        const updateAddressUrl = @json(route('my.data.update.address'));
        const getBankListURL = @json(route('default.get.banks'));
        const sendToken = @json(route('my.data.send-authorization-token'));
        const verifyToken = @json(route('my.data.verify-authorization-token'));
        const updateDataBank = @json(route('my.data.update.bank.details'));
        const verifyDocument = @json($verifyDocument);
        const recipientStatusMessage = @json($recipientStatusMessage);
    </script>
    <script src="{{ asset('js/bundle/documents.js') }}"></script>
@endpush
@section('content')
    <div id="documentsApp">
        <verify-document v-if="verifyDocument" :description="recipientStatusMessage"></verify-document>
        <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="{{ route('documents') }}">Meus dados</a></li>
            </ol>
        </nav>
        <div class="xgrow-tabs nav nav-tabs mb-3">
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('validate')" :class="{'active': $store.state.tab === 'validate'}" v-if="$store.state.edit !== true">
                Identidade
            </a>
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('bank-data')" :class="{'active': $store.state.tab === 'bank-data'}">
                Dados bancários
            </a>
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('address')" :class="{'active': $store.state.tab === 'address'}">
                Endereço
            </a>
            <a role="button" class="xgrow-tab-item nav-item nav-link" @click="setTab('rates-and-terms')" :class="{'active': $store.state.tab === 'rates-and-terms'}">
                Taxas e prazos
            </a>
        </div>
        <router-view></router-view>
        <status-modal-component :is-open="$store.state.loading" status="loading"></status-modal-component>
    </div>
    @include('elements.toast')
@endsection
