@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('js/bundle/gamification-dashboard.js') }}"></script>
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/gamification.css') }}" rel="stylesheet">
@endpush

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
@endpush

@section('content')
    <div id="dashboard">

        <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <!-- <li class="breadcrumb-item"><a href="/gamification">Gamificação</a></li> -->
                <li class="breadcrumb-item active mx-2"><span>Dashboard</span></li>
            </ol>
        </nav>

        <div class="container-fluid p-0">
            <div id="gamification-status" class="row">
                <pill-component
                    icon="/xgrow-vendor/assets/img/gamification/coin.svg"
                    :icon-is-custom="true"
                    :value="coinsEarned"
                    title="Xcoins conquistadas"
                    xxl="3" xl="3" lg="3" md="6" sm="12" xs="12">
                </pill-component>

                <pill-component
                    icon="/xgrow-vendor/assets/img/gamification/user-coin.svg"
                    :icon-is-custom="true"
                    :value="coinsAverage"
                    title="Xcoins por aluno (média)"
                    xxl="3" xl="3" lg="3" md="6" sm="12" xs="12">
                </pill-component>

                <pill-component
                    icon="fas fa-chart-bar"
                    icon-color="#DFBD45"
                    :value="engagementFormated"
                    title="Alunos engajados"
                    xxl="3" xl="3" lg="3" md="6" sm="12" xs="12">
                </pill-component>
            </div>

            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                    <card-component
                        id="leaderboard-1"
                        title="Alunos em destaque"
                        subtitle="Veja em detalhes o ranking de seus melhores alunos."
                        :padding-bottom="2">
                        <podium :winners="winners"></podium>
                        <leaderboard :list="leaderboard"></leaderboard>
                    </card-component>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                    <card-component
                        id="leaderboard-2"
                        title="Alunos sem engajamento"
                        subtitle="Veja em detalhes o ranking de seus alunos com pouco ou nenhum engajamento."
                        :padding-bottom="2">
                        <leaderboard
                            :list="noengagement"
                            :max-height="339">
                        </leaderboard>
                    </card-component>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <card-component
                        id="phase-chart"
                        title="Alunos por fase"
                        subtitle="Veja em detalhes a quantidade de alunos por fase.">
                        <phase-chart-component></phase-chart-component>
                    </card-component>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                    <card-component
                        id="challenge-chart-1"
                        title="Desafios mais completados"
                        subtitle="Veja em detalhes quais são seus desafios mais completados.">
                        <challenge-component
                            v-if="
                                charts.mostCompleted.labels.length > 0 &&
                                charts.mostCompleted.values.length > 0 &&
                                charts.mostCompleted.colors.length > 0
                            "
                            :labels="[...charts.mostCompleted.labels]"
                            :values="[...charts.mostCompleted.values]"
                            :colors="[...charts.mostCompleted.colors]">
                        </challenge-component>
                        <div v-else class="charts-no-content">
                            <p>Sem dados para mostrar</p>
                        </div>
                    </card-component>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                    <card-component
                        id="challenge-chart-2"
                        title="Desafios menos completados"
                        subtitle="Veja em detalhes quais são seus desafios menos completados.">
                        <challenge-component
                            v-if="
                                charts.leastCompleted.labels.length > 0 &&
                                charts.leastCompleted.values.length > 0 &&
                                charts.leastCompleted.colors.length > 0
                            "
                            :labels="[...charts.leastCompleted.labels]"
                            :values="[...charts.leastCompleted.values]"
                            :colors="[...charts.leastCompleted.colors]">
                        </challenge-component>
                        <div v-else class="charts-no-content">
                            <p>Sem dados para mostrar</p>
                        </div>
                    </card-component>
                </div>
            </div>
        </div>

        <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>

        @include('elements.confirmation-modal')
        @include('elements.toast')
    </div>
@endsection
