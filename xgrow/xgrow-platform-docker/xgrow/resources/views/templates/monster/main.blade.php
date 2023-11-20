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
    <link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/switchery/dist/switchery.min.css" rel="stylesheet" />
    <link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/summernote/dist/summernote-bs4.css" rel="stylesheet" />
    <link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" />
    <link rel="stylesheet" href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery-asColorPicker-master/dist/css/asColorPicker.css">
    <link href="/css/dropzone.css" rel="stylesheet">
    <link href="/css/toastr/toastr.css" rel="stylesheet"/>
    <link href='/css/fandone.css' rel="stylesheet">
    <link href='/css/checkboxes.css' rel="stylesheet">

@endsection


@section('template-custom-js')
<script src="/vendor/wrappixel/monster-admin/4.2.1/horizontal/js/custom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/datatables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script src="/js/toastr/toastr.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/styleswitcher/jQuery.style.switcher.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/switchery/dist/switchery.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/html5-editor/wysihtml5-0.3.0.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/summernote/dist/summernote-bs4.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery-asColor/dist/jquery-asColor.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery-asGradient/dist/jquery-asGradient.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.js"></script>
<script src="/js/dropzone.js"></script>
<script src="/js/toastr/toastr-config.js"></script>
<script src="{{ asset('js/menu.js')}}"></script>
<script src="{{ asset('js/helpers.js')}}"></script>

<!-- end - This is for export functionality only -->
@endsection


@section('layout-content')

    @include('templates.application.includes.topbar')

    @include('templates.monster.left-sidebar')

    <div class="page-wrapper">

        <div class="container-fluid">

            @yield('content')
            @include('templates.application.includes.right-sidebar')
            @include('dashboard.modal-support')

        </div>

    </div>

@endsection

