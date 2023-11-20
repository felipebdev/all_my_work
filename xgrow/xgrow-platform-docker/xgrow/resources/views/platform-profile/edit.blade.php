@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script type="text/javascript">
        function searchDns() {
            const url = $("#url_official").val();
            $.ajax({
                type: 'GET',
                url: "{{ URL::route('platform-config.valid-url') }}",
                dataType: 'json',
                data: {
                    'url_official': url
                },
                success: function(data) {
                    if (data.status === 'success') {
                        successToast("DNS verificado!", `${data.message}`);
                    }
                    if (data.status === 'error') {
                        errorToast("Algum erro aconteceu!", `Veja mais em: ${data.message}`);
                    }
                },
                error: function(data) {
                    if (data.status === 'error') {
                        errorToast("Algum erro aconteceu!", `Veja mais em: ${data.message}`);
                    }
                }
            });
        }

        $("#exampleCheck1").on('click', function() {
            const checked = $(this).is(":checked");
            const url = $("#url_official").val();
            if (checked == false) {
                if (!confirm(`Deseja realmente tirar a plataforma do ar?`)) {
                    return false
                }
            }

            $.ajax({
                type: 'GET',
                url: "{{ URL::route('platform-config.on-off') }}",
                dataType: 'json',
                data: {
                    'checked': checked,
                    'url_official': url
                },
                success: function(data) {
                    console.log(data);
                    if (data.status === 'success') {
                        successToast("Plataforma removida!", `${data.message}`);
                    }
                    if (data.status === 'error') {
                        errorToast("Algum erro aconteceu!", `Veja mais em: ${data.message}`);
                    }
                },
                error: function(data) {
                    console.log(data);
                    if (data.status === 'error') {
                        errorToast("Algum erro aconteceu!", `Veja mais em: ${data.message}`);
                    }
                }
            });
        });

        $('#btn_save').on('click', function() {
            document.querySelector('#form').submit();
        });

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item active mx-2"><span>Perfil Plataforma</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark mt-2">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Perfil plataforma</p>
        </div>
        <div class="xgrow-card-body">

            @if ($errors->any())
                @include('elements.alert')
            @endif

            <form id="form" class="mui-form tab-content" method="POST" action="{{ route('platform-profile.store') }}">
                @include('platform-profile.profile')

                {{ csrf_field() }}
            </form>
        </div>
        <div class="xgrow-card-footer">
            <button id="btnDns" onclick="searchDns()" class="xgrow-button-secondary me-2">Verificar DNS</button>
            <button id="btn_save" class="xgrow-button">Salvar alterações</button>
        </div>
    </div>
    @include('elements.toast')
@endsection
