@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const checkbox = document.getElementById('chk-active-forum');
        const forumText = document.getElementById('chk-active-forum-label');

        const checktheme = document.getElementById('chk-theme-forum');
        const themeText = document.getElementById('type_theme');

        checkbox.addEventListener('change', (event) => {
            if (event.target.checked) {
                checkbox.value = 1;
                forumText.innerText = "Fórum ativado"
                successToast('Fórum ativado com sucesso!', 'Clique em salvar para visualizar as alterações.');
            } else {
                checkbox.value = 0;
                forumText.innerText = "Fórum desativado"
            }
        });

        checktheme.addEventListener('change', (event) => {
            if (event.target.checked) {
                checktheme.value = 1;
                themeText.innerText = "Tema claro"
            } else {
                checktheme.value = 0;
                themeText.innerText = "Tema escuro"
            }
        });

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        function verifySwitchs() {
            forumText.innerText = checkbox.checked ? "Fórum ativado" : "Fórum desativado";
            themeText.innerText = checktheme.checked ? "Tema claro" : "Tema escuro";
        }

        verifySwitchs();

    </script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css"
        href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/section_index.css') }}" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/forum_index.css') }}" rel="stylesheet">
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Fórum</span></li>
        </ol>
    </nav>

    <nav class="xgrow-tabs-wrapper">
        <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">

            <a class="xgrow-tab-item nav-item nav-link active" id="nav-forum-tab" data-bs-toggle="tab" href="#nav-forum"
                role="tab" aria-controls="nav-forum" aria-selected="true">Fórum</a>

            <a class="xgrow-tab-item nav-item nav-link" id="nav-topics-tab" data-bs-toggle="tab" href="#nav-topics"
                role="tab" aria-controls="nav-topics" aria-selected="false">Tópicos</a>

            <a class="xgrow-tab-item nav-item nav-link" href="{{ route('forum.moderation.pending') }}">
                Posts
            </a>
        </div>
    </nav>

    <div class="tab-content" id="nav-tabContent">
        <!-- Tab Fórum-->
        @include('forum._form')
        <!-- Tab Tópicos-->
        @include('forum._tab-topics')
    </div>
    @include('elements.toast')
@endsection
