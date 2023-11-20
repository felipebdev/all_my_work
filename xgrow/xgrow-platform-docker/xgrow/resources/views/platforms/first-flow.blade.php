@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const userURL = @json(route('user.info'));
        const clientURL = @json(route('first.access'));
    </script>
    <script src="{{ asset('js/bundle/start-flow.js') }}"></script>
@endpush

@push('after-styles')
@endpush



@section('content')
    <div id="startFlow" class="mt-5">
        <router-view></router-view>
        @include('elements.toast')
    </div>
@endsection
