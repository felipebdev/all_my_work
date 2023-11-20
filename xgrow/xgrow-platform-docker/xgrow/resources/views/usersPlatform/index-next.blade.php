@extends('templates.xgrow.main')

@push('after-styles')
@endpush

@push('after-scripts')
    <script>
        const getUsers = @json(route('platforms-users.get.users'));
        const editUser = @json(route('platforms-users.edit', Auth::user()->id));
        const deleteUser = @json(route('platforms-users.destroy', 'id_user'));
        const idUser = @json(Auth::user()->id);
        const owner = @json($owner->email);
    </script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script src="{{ asset('js/bundle/users-platforms.js') }}"></script>
@endpush

@section('content')
    <div id="settingsUsers">
        <router-view></router-view>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
