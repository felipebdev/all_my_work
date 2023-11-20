@extends('templates.xgrow.main')

@push('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>

        $(document).ready(function () {
            $("#phone_number").mask("(00) 00000-0000");

            $('#zipcode')
                .mask('00000-000')
                .change(function () {
                    searchAddress();
                });

            $('#bank').select2({
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    noResults: function () {
                        return "Nenhum banco encontrado";
                    }
                },
                placeholder: 'Banco',
                tags: false
            });

            $('#branch').mask('0000')
            $('#branch_check_digit').mask('0')
            $('#account').mask('000000000000')
            $('#account_check_digit').mask('0')
        });

        function resetZipCodeForm() {
            // Limpa valores do formulário de cep.
            $('#address').val('');
            $('#district').val('');
            $('#city').val('');
            $('#state').val('');
        }

        function searchAddress() {
            let cep_ = $('#zipcode').val();
            const cep = cep_.replace('-', '');

            $('#address').show();

            let validacep = /^[0-9]{8}$/;

            if (!validacep.test(cep)) {
                resetZipCodeForm();
                errorToast('Algum erro aconteceu!', 'Formato de CEP inválido.');
                return false;
            }

            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/?callback=?', function (dados) {
                if ('erro' in dados) {
                    resetZipCodeForm();
                    errorToast('Algum erro aconteceu!', 'CEP não encontrado.');
                    return false;
                }

                $('#address').val(dados.logradouro);
                $('#district').val(dados.bairro);
                $('#city').val(dados.localidade);
                $('#state').val(dados.uf);
                $('#number').focus();
            });
        }

        $("#type_person").on("change", function () {
            if ($("#type_person").val() == "F") {
                $("#document_label").text("CPF");
                $("#document").val("");
                $("#document").mask("000.000.000-00");
            } else if ($("#type_person").val() == "J") {
                $("#document_label").text("CNPJ");
                $("#document").val("");
                $("#document").mask("00.000.000/0000-00");
            }
        });
    </script>
@endpush

@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .select2-container--default .select2-selection--multiple {
            border-bottom: none !important;
            background-color: #1E2025 !important;
        }

    </style>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Usuário</a></li>
            <li class="breadcrumb-item active mx-2"><span>Financeiro</span></li>
        </ol>
    </nav>

    @include('elements.alert')
    <div class="xgrow-card card-dark">
        <div class="xgrow-card-header">
            <h3>Dados bancários</h3>
        </div>
        <form action="{{ route('user.financial.edit', $client->id) }}" method="post" enctype="multipart/form-data">
            <div class="xgrow-card-body">
                @include('platforms-users.financial._form')
            </div>
            <div class="xgrow-card-footer">
                @if ($isOwner)
                    <button type="submit" class="xgrow-button">Salvar alterações</button>
                @endif
            </div>
            @include('up_image.modal-xgrow')
        </form>
    </div>
@endsection
