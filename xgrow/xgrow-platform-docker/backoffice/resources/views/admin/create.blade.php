@extends('templates.horizontal.main')

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>

    <!--
    <script src="{{ asset('vendor/toast/toast-config.js') }}"></script>
    <script type="module" src="{{ asset("js/admin.js") }}"></script>
    -->

@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Administradores</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Administradores</a></li>
                <li class="breadcrumb-item active">Novo</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Novo</h4>

            <form id="create-admin-form" class="mt-4" action="{{url('/admin/store')}}" method="POST" autocomplete="off">
                @csrf
                @include('admin._form')
                <button type="submit" class="btn btn-primary">Criar Administrador</button>
            </form>


        </div>
    </div>

@endsection
