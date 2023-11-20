@extends('templates.xgrow.main')

@push('jquery')
    <script defer>
        const checkbox = document.getElementById('chk-active-topic');
        const topicText = document.getElementById('topic_exhibition_text');
        const ckModeration = document.getElementById('chk-active-moderation');
        const adasiuda = document.getElementById('mod_exhibition_text');
        checkbox.addEventListener('change', (event) => {
            if (event.target.checked) {
                checkbox.value = 1;
                topicText.innerText = "Tópico visível"
            } else {
                checkbox.value = 0;
                topicText.innerText = "Tópico oculto"
            }
        });
        ckModeration.addEventListener('change', (event) => {
            if (event.target.checked) {
                ckModeration.value = 1;
                adasiuda.innerText = "Moderação ligada"
            } else {
                ckModeration.value = 0;
                adasiuda.innerText = "Moderação desligada"
            }
        });

    </script>
@endpush

@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/topic_add.css') }}" rel="stylesheet">
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $('#tags, #hashtags').select2({
            tags: true,
            tokenSeparators: [';'],
        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/forum">Fórum</a></li>
            <li class="breadcrumb-item active"><span>
                    @if ($topic->id == 0) Adicionar @else Editar @endif Tópico
                </span></li>
        </ol>
    </nav>

    @include('forum.topic._form')
@endsection
