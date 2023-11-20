@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const getChallengesURL = @json(route('gamification.get.challenges.datatable'));
        const saveChallengeURL = @json(route('gamification.save.challenges'));
        const deleteChallengeURL = @json(route('gamification.delete.challenges', ':id'));
        const updateChallengeURL = @json(route('gamification.update.challenges', ':id'));
        const saveChallengeSettingsURL = @json(route('gamification.save.challenges.settings'));
        const updateChallengeSettingsURL = @json(route('gamification.update.challenges.settings', ':id'));
        const getChallengeSettingsURL = @json(route('gamification.get.challenges.settings'));
    </script>
    <script src="{{ asset('js/bundle/gamification-challenges.js') }}"></script>
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/gamification.css') }}" rel="stylesheet">
    <style>
        .xgrow-dropdown-menu.show {
            transform: translate(-123px, 30px) !important;
        }

    </style>
@endpush

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@section('content')
    <div id="challenges">

        <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <!-- <li class="breadcrumb-item"><a href="/gamification">Gamificação</a></li> -->
                <li class="breadcrumb-item active mx-2"><span>Desafios</span></li>
            </ol>
        </nav>

        <template v-if="activeContentScreen === ''">
            <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab">
                <a class="xgrow-tab-item nav-item nav-link" id="nav-configurations-config-tab"
                    @click="activeScreen = 'challenges.challenges'"
                    :class="{'active': ['challenges.challenges', 'challenges.new'].includes(activeScreen)}">
                    Desafios
                </a>

                <a class="xgrow-tab-item nav-item nav-link" id="nav-configurations-score-tab"
                    @click="activeScreen = 'challenges.config'"
                    :class="{'active': ['challenges.config'].includes(activeScreen)}">
                    Configurações
                </a>
            </div>
        </template>

        <xgrow-modal-component :is-open="showDeleteModal" v-on:close="closeDeleteModal">
            <template v-slot:title>
                Excluir Desafio
            </template>

            <template v-slot:content>
                <div class="align-self-center w-100 d-flex flex-column align-items-center">
                    <h5>Deseja mesmo excluir o desafio "[[ deleteModalChallenge ]]" ?</h5>
                    <small class="mt-3">Não terá como desfazer essa ação no futuro</small>
                </div>
            </template>

            <template v-slot:footer>
                <button type="button" class="btn btn-success"
                    @click.prevent="deleteModalConfirmation">Sim, excluir</button>
                <button type="button" class="btn btn-outline-success"
                    @click.prevent="() => showDeleteModal = false">Não, manter</button>
            </template>
        </xgrow-modal-component>

        <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>

        <div class="xgrow-card card-dark" id="challenge-content" style="min-height: 500px">
            <div class="tab-content" id="nav-tabContent">
                @include('gamification.tabs.challenges-challenges')
                @include('gamification.tabs.challenges-config')
                @include('gamification.tabs.challenges-new-challenge')
            </div>
        </div>
        @include('elements.confirmation-modal')
        @include('elements.toast')
    </div>
@endsection
