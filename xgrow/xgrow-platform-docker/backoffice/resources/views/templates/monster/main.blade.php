@extends('templates.application.master')

{{-- ### Attributes for Layout are added here ### --}}
{{--Possibilities:  'fix-header'  'fix-sidebar' 'boxed' 'logo-center' 'single-column' --}}
{{--You can make combinations with them--}}
@section('body-classes','')

@section('template-css')
    <!-- toast CSS -->
    <link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/toast-master/css/jquery.toast.css" rel="stylesheet">
    <link href="{{ mix('/css/monster/style.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/colors/blue.css') }}" id="theme" rel="stylesheet">
@endsection

@section('template-custom-js')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>
@endsection

@section('layout-content')

    @include('templates.application.includes.topbar')

    @include('templates.monster.left-sidebar')

    <div class="page-wrapper">

        <div class="container-fluid">

            @if(true)
                @include('templates.application.includes.breadcrumb')
            @else
                <div class="row mb-4"></div>
            @endif

            @yield('content')

            @include('templates.application.includes.right-sidebar')

        </div>

    </div>

@endsection
