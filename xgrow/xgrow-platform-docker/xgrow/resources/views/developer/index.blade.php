@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        // const platformId = @json(Auth::user()->platform_id);
        // const affiliatesActiveUrl = @json(route('affiliations.list.all', Auth::user()->platform_id));
        const oauthURL = @json(config('learningarea.url_oauth'));
    </script>
    <script src="{{ asset('js/bundle/developer.js') }}"></script>
@endpush
@section('content')
    <div id="developerApp">
        <router-view></router-view>
        <status-modal-component :is-open="$store.state.loading" status="loading"></status-modal-component>
    </div>
    @include('elements.toast')
@endsection
