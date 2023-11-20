@extends('templates.xgrow.main')

@push('after-styles')
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const getNotifications = @json(route('mobile.notification.index'));
        const storeNotifications = @json(route('mobile.notification.store'));
        const updateNotifications = @json(route('mobile.notification.update', 'notification_id'));
        const deleteNotifications = @json(route('mobile.notification.destroy', 'notification_id'));
    </script>
    <script src="{{ asset('js/bundle/resources.js') }}"></script>
@endpush

@section('content')
    <div id="resourcesApp">
        <div id="resourceContent">
            <router-view></router-view>
        </div>
    </div>
    @include('elements.toast')
@endsection
