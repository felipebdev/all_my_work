@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/platform-config/users">Usuários</a></li>
            <li class="breadcrumb-item active mx-2"><span>Editar Usuário</span></li>
        </ol>
    </nav>

    @include('usersPlatform.form')
    @include('elements.toast')
@endsection
