@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/learning-area.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const uploadImageURL = @json(route('learning.area.upload.image'));
        const platform_id = @json(Auth::user()->platform_id);
        const contentAPI = @json(config('learningarea.url'));
        const uploadFileURL = @json(config('learningarea.file_url'));
        const getSubscriberInfoURL = @json(route('learning.area.get.subscriber.info'));
    </script>
    <script src="{{ asset('js/bundle/learning-area.js') }}"></script>
@endpush

@section('content')
    <div id="learningAreaApp">
        <div id="learningAreaMenu">
            <menu-component platform-name="{{ Auth::user()->platform->name }}" />
        </div>
        <div id="learningAreaMobileMenu">
            <mobile-menu-component platform-name="{{ Auth::user()->platform->name }}" />
        </div>
        <div id="learningAreaContent">
            <router-view></router-view>
        </div>
    </div>
    @include('elements.toast')
@endsection
