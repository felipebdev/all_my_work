@extends('templates.xgrow.main')
@inject('platform_user', 'App\Platform')
@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/platforms.css') }}">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/sidebar.css') }}">
    <style>
        .xgrow-button {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .xgrow-button:hover {
            text-decoration: none;
            color: #FFFFFF;
        }
    </style>
@endpush

@push('after-scripts')
    {{-- Colocar a data de inicio e fim (ano, mes, dia, hora) --}}
    @if(\Carbon\Carbon::now() > \Carbon\Carbon::create(2022,9,21,13) && \Carbon\Carbon::now() < \Carbon\Carbon::create(2022,9,26,1))
        <script>
            // showInfoModal(show: boolean, cookie: uuid4, clearCookie: boolean)
            showInfoModal(true, '8ca48a02-3655-45c8-8367-82d60b3b9afe', false)
        </script>
    @endif
@endpush

@section('content')
    @include('elements.alert')
    @include('elements.information-modal')

    @if (Auth::user()->accepted_terms)
        @if (!$isClient && !count($platforms))
            {{-- First Flow for nex platform --}}
            @include('platforms.first-flow')
        @else
            {{-- List Platforms --}}
            @include('platforms.platforms-new')
        @endif
    @else
        {{-- New Platform not accepted terms --}}
        @include('platforms.terms')
    @endif
@endsection
