@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const getSettingsURL = @json(route('gamification.get.settings'));
        const getScoreURL = @json(route('gamification.get.actions'));
        const saveScoreURL = @json(route('gamification.save.actions'));
        const saveSettingsURL = @json(route('gamification.save.settings'));
        const getPhasesURL = @json(route('gamification.get.phases'));
        const savePhasesURL = @json(route('gamification.save.phases'));
        const deletePhaseURL = @json(route('gamification.delete.phases', ':id'));
        const updatePhaseURL = @json(route('gamification.update.phases', ':id'));

    </script>
    <script src="{{ asset('js/bundle/gamification-config.js') }}"></script>
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/gamification.css') }}" rel="stylesheet">
    <style>
        .xgrow-floating-input input {
            min-width: 100%;
        }

    </style>
@endpush

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@section('content')
    <div id="configurations">

        <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <!-- <li class="breadcrumb-item"><a href="/gamification">Gamificação</a></li> -->
                <li class="breadcrumb-item active mx-2"><span>Configurações</span></li>
            </ol>
        </nav>

        <template v-if="activeContentScreen === ''">
            <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab">
                <a class="xgrow-tab-item nav-item nav-link" id="nav-configurations-config-tab"
                   @click="activeScreen = 'configurations.config'"
                   :class="{'active': activeScreen.toString() === 'configurations.config'}">
                    Configurações
                </a>

                <a class="xgrow-tab-item nav-item nav-link" id="nav-configurations-score-tab"
                   @click="activeScreen = 'configurations.score'"
                   :class="{'active': activeScreen.toString() === 'configurations.score'}">
                    Pontuação
                </a>

                <a class="xgrow-tab-item nav-item nav-link" id="nav-configurations-level-tab"
                   @click="activeScreen = 'configurations.level'"
                   :class="{'active': activeScreen.toString() === 'configurations.level'}">
                    Fases
                </a>
            </div>
        </template>

        <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>

        <div class="xgrow-card card-dark">
            <div class="tab-content" id="nav-tabContent">
                @include('gamification.tabs.configurations-config')
                @include('gamification.tabs.configurations-score')
                @include('gamification.tabs.configurations-level')
            </div>
        </div>
        @include('elements.confirmation-modal')
        @include('elements.toast')
    </div>
@endsection
