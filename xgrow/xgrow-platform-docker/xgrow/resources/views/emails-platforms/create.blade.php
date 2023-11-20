@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/emails.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{ url('/js/dist_lang_summernote-pt-BR.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#message').summernote({
                height: 300,
                minHeight: null,
                maxHeight: null,
                focus: false,
                lang: 'pt-BR',
                placeholder: "Descreva detalhadamente aqui o seu tópico.",
            });


            $("#email_id").change(function () {
                const email_id = $(this).children("option:selected").val();
                if (email_id === 'undefined' || email_id <= 0 || email_id == '') {
                    return false;
                }

                $.ajax({
                    type: 'GET',
                    url: "/emails/getMessageExample/" + email_id,
                    dataType: 'json',
                    success: function (data) {
                        // $('.note-editable').html(data.message);
                        $('#message').summernote('code', data.message);
                    },
                    error: function (data) {
                        erroToast("Erro", "Houve um erro na busca da mensagem padrão");
                    }
                });
            });

            $("#email_id").change(function () {
                const text = $(this).children("option:selected").text();
                $("#subject").removeClass('mui--is-empty mui--is-untouched mui--is-pristine');
                $("#subject").addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
                $("#subject").val('...');
                $("#subject").val(text);
            });

            $("#email_id").trigger('change');
        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/emails">E-mails</a></li>
            <li class="breadcrumb-item"><a href="/emails">Mensagens</a></li>
            <li class="breadcrumb-item active mx-2"><span>Nova</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-header px-3">
            <p class="xgrow-card-title">Criar mensagem</p>
        </div>
        <form action="{{ url("/emails/store/") }}" method="post">
            <div class="xgrow-card-body px-3 py-3">
                @include('emails-platforms.form')
            </div>

            <div class="xgrow-card-footer p-3 border-top">
                @csrf
                @method('POST')
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
    @include('elements.toast')
@endsection
