@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/integret_add.css') }}">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/new-integrations.css') }}">

    <style>
        .x-dropdown {
            position: unset !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('js/bundle/integrations.js') }}"></script>
@endpush

@section('content')
    <div id="integrations">

        <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item active mx-2"><span>Integrações</span></li>
            </ol>
        </nav>

        <status-modal-component :is-open="loading.active" :status="loading.status"></status-modal-component>

        <div class="xgrow-card card-dark" id="integrations-content" style="min-height: 500px">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show" id="integrationsIndex"
                    :class="{ 'active': activeScreen.toString() === 'integrations.index' }">
                    <integrations-index-page />
                </div>
            </div>
        </div>

    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
