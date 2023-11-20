@extends('templates.xgrow.main')

@php
    use App\Constants;
@endphp

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/verify-alert.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script>
        const productsAllURL = @json(route('products.get-all'));
        const productsUpdateStatusURL = @json(route('products.update.status', ':id'));
        const productsEditURL = @json(route('products.edit-plan', ':id'));
        const productsDeleteURL = @json(route('products.delete', ':id'));
        const productsDuplicateURL = @json(route('products.replicate', ':id'));
        const csrfToken = @json(csrf_token());
        const verifyDocument = @json($verifyDocument);
        const recipientStatusMessage = @json($recipientStatusMessage);
        const contentAPI = @json(config('learningarea.url'));
    </script>
    <script src="{{ asset('js/bundle/products.js') }}"></script>
@endpush

@section('content')
    <div id="productsPage">
        <router-view></router-view>
    </div>
    @include('elements.alert')
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
