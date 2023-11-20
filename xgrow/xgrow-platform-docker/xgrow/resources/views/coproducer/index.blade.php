@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const banksURL = @json(route('default.get.banks'));
        const clientURL = @json(route('first.access.get.client.data'));
        const coproducerOwnerUrl = @json(route('active.platforms.coproducer'));
        const coproducerPendingUrl = @json(route('pending.platforms.coproducer'));
        const validateDocumentURL = @json(route('validade.document.coproducer', ':platformId'));
        const updateProducerURL = @json(route('accept.co.production.request', [':idProducerProducts', ':producerId']));
        const updateInviteURL = @json(route('update.status.producer.products.coproducer', [':idProducerProducts', ':producerId']));
        const updateBankProducerURL = @json(route('update.bank.information.coproducer', [':platformId']));
        const coproducerBankInformation = @json(route('get.bank.information.coproducer', ':platformId'));
        const withdrawListURL = @json(route('list.withdrawals.coproducer', ':platformId'));
        const coproducerWithdrawURL = @json(route('coproducer.withdraw.value', ':platformId'));
        const transactionListURL = @json(route('financial.report.sales.coproducer', ':platformId'));
        const transactionDetailsURL = @json(route('sale.details.coproducer', ':platformId'));
        const balanceDetailsURL = @json(route('coproducer.balance', ':platformId'));
    </script>
    <script src="{{ asset('js/bundle/coproducer.js') }}"></script>
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/coproducer.css') }}" rel="stylesheet">
@endpush

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@section('content')
    <div id="coproducer">
        <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" @click="goToProducerArea" style="cursor: pointer"
                    :class="{ 'active': ['coproducer.my', 'coproducer.pending'].includes(activeScreen) }">
                    <span>Área do coprodutor</span>
                </li>
                <template
                    v-if="['sales.withdraw', 'transaction.transaction', 'transaction.no-limit'].includes(activeScreen)">
                    <li class="breadcrumb-item"><a href="#">Vendas</a></li>
                    <li class="breadcrumb-item mx-2" v-if="activeScreen.toString() === 'sales.withdraw'"
                        :class="{ 'active': activeScreen.toString() === 'sales.withdraw' }">
                        <span>Saques</span>
                    </li>
                    <li class="breadcrumb-item mx-2" v-if="activeScreen.toString() === 'transaction.transaction'"
                        :class="{ 'active': activeScreen.toString() === 'transaction.transaction' }">
                        <span>Transações</span>
                    </li>
                    <li class="breadcrumb-item mx-2" v-if="activeScreen.toString() === 'transaction.no-limit'"
                        :class="{ 'active': activeScreen.toString() === 'transaction.no-limit' }">
                        <span>Sem limite</span>
                    </li>
                </template>
            </ol>
        </nav>
        <template v-if="['coproducer.my', 'coproducer.pending'].includes(activeScreen)">
            <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab">
                <a class="xgrow-tab-item nav-item nav-link" id="nav-coproducer-my-tab"
                    @click="activeScreen = 'coproducer.my'"
                    :class="{ 'active': activeScreen.toString() === 'coproducer.my' }">
                    Minhas coproduções
                </a>

                <a class="xgrow-tab-item nav-item nav-link" id="nav-coproducer-pending-tab"
                    @click="activeScreen = 'coproducer.pending'"
                    :class="{ 'active': activeScreen.toString() === 'coproducer.pending' }">
                    Pedidos Pendentes
                </a>
            </div>
        </template>

        {{-- <template v-if="['transaction.transaction', 'transaction.no-limit'].includes(activeScreen)"> --}}
        <template v-if="false">
            <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab">
                <a class="xgrow-tab-item nav-item nav-link" id="nav-transaction-transaction-tab"
                    @click="activeScreen = 'transaction.transaction'"
                    :class="{ 'active': activeScreen.toString() === 'transaction.transaction' }">
                    Transações
                </a>

                <a class="xgrow-tab-item nav-item nav-link" id="nav-transaction-no-limit-tab"
                    @click="activeScreen = 'transaction.no-limit'"
                    :class="{ 'active': activeScreen.toString() === 'transaction.no-limit' }">
                    Sem limite
                </a>
            </div>
        </template>

        <status-modal-component :is-open="loading" :status="status"></status-modal-component>

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show" id="coproducerMy" :class="{'active': activeScreen.toString() === 'coproducer.my'}">
                <coproductions-owner-component @get-id="getId" ref="coproductionsOwner" :env="false"/>
            </div>
            <div class="tab-pane fade show" id="coproducerPending" :class="{'active': activeScreen.toString() === 'coproducer.pending'}">
                <div class="xgrow-card card-dark">
                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-12 col-lg-12">
                            <coproductions-pending-component
                                @change-page="changePage"
                                @charge-data-flow="chargeDataFlow"
                                @reload-coproduction="reloadCoproductionsOwner"
                                ref="coproductionsPending" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="salesWithdraw" :class="{'active': activeScreen.toString() === 'sales.withdraw'}">
                <sales-withdrawn-component :platform-id="platformId" ref="salesWithdraw"/>
            </div>
            <div class="tab-pane fade show" id="transactionTransaction" :class="{'active': activeScreen.toString() === 'transaction.transaction'}">
                <transaction-transaction-component :platform-id="platformId" ref="transactionTransaction"/>
            </div>
            <div class="tab-pane fade show" id="transactionNoLimit" :class="{'active': activeScreen.toString() === 'transaction.no-limit'}">
                <div class="xgrow-card card-dark">
                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-12 col-lg-12">
                            <transaction-no-limit-component/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="coproducerFlow" :class="{'active': activeScreen.toString() === 'coproducer.flow'}">
                <div class="xgrow-card card-dark">
                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-12 col-lg-12">
                            <coproducer-flow
                                @return-coproducer="changePage"
                                :coproducer-data="dataFlow"
                                @update-pending-list="reloadCoproductionsPending"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('elements.confirmation-modal')
        @include('elements.toast')
    </div>
@endsection
