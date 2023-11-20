@extends('templates.xgrow.main')

@push('after-scripts')
<script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
<script>
    const getSubscribersRoute = @json(route('reports.get.progress.subscribers.api'));
    const getSubscriberSimplifiedProgress = @json(route('reports.get.subscriber.simplified.progress'));
    const getCoursesRoute = @json(route('reports.get.progress.courses.api'));
</script>
<script src="{{ asset('js/bundle/simplifiedProgressReport.js') }}"></script>
@endpush

@push('after-styles')
<link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/simplified-progress.css') }}">
@endpush

@section('content')
    <div id="simplifiedProgress">
        <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item"><span>Relatórios</span></li>
                <li class="breadcrumb-item"
                    :class="{'active mx-2': activeScreen.toString() === 'progress.all'}">
                    <span>Progresso</span>
                </li>
                <li class="breadcrumb-item active mx-2" v-if="activeScreen.toString() === 'progress.summary'">
                    <span>Detalhes</span>
                </li>
            </ol>
        </nav>
    
        <div class="xgrow-card card-dark">
            <div class="xgrow-card-body pt-2 pb-3">
    
                <div class="tab-content" id="nav-tabContent">
                    @include('reports.simplified-progress.tabs.all')
                    @include('reports.simplified-progress.tabs.summary')
                </div>
    
                <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>
            </div>
        </div>
        
        @include('elements.toast')
    </div>
@endsection