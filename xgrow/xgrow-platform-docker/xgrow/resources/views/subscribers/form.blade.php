@push('after-styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
    <link rel="stylesheet" href="{{asset('xgrow-vendor/plugins/password-validator/password-validator.css')}}">
    <style>
        .xgrow-floating-input input {
            min-width: auto !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="//cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
    <script src="{{asset('xgrow-vendor/plugins/password-validator/password-validator.js')}}"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js">
    </script>
    <script>
        $(".xgrow-datepicker").datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });
    </script>

    <script>
        $(document).ready(function () {
            // trigger initial person type
            let type = @json(($subscriber->document_type ?? 'cpf'));
            let country = @json(($subscriber->address_country ?? 'BRA'));
            changeType(type);
            if (country == "BRA" || country == "Brasil") {
                $(".foreign-document").hide();
                $(".brazilian-document").show();
            } else {
                $(".brazilian-document").hide();
                $(".foreign-document").show();
                $("#type").val("legal_person");
            }

            $("#address_zipcode")
                .mask("00000-000")
                .change(function () {
                    searchAddress();
                });

            $("#address_country").change(function () {
                applyPhoneMask();
            });

            applyPhoneMask();
        });

        function applyPhoneMask() {
            const country = $("#address_country").val();

            if (country !== "BRA") {
                $("#main_phone").unmask();
                $("#main_phone").attr("maxlength", "20");
                $("#main_phone").attr("minlength", "5");
                $("#cel_phone").unmask();
                $("#cel_phone").attr("maxlength", "20");
                $("#cel_phone").attr("minlength", "5");
            } else {
                $("#main_phone")
                    .mask("(00) 0000-0000")
                    .change(function (e) {
                        const target = e.target;
                        const country = $("#address_country").val();

                        if (country !== "BRA") return;

                        target.setCustomValidity("");
                        if (validator.onlyDigits(target.value) === "") {
                            return false;
                        }
                        if (!validator.isValidPhoneNumber(target.value)) {
                            let message = "Telefone inválido!";
                            e.target.setCustomValidity(message);
                            errorToast("Algum erro aconteceu!", `Veja mais em: ${message}`);
                        }
                    });

                $("#cel_phone")
                    .mask("(00) 00000-0000")
                    .change(function (e) {
                        const target = e.target;
                        const country = $("#address_country").val();

                        if (country !== "BRA") return;

                        target.setCustomValidity("");
                        if (validator.onlyDigits(target.value) === "") {
                            return false;
                        }
                        if (!validator.isValidCelNumber(target.value)) {
                            let message = "Celular inválido!";
                            target.setCustomValidity(message);
                            errorToast("Algum erro aconteceu!", `Veja mais em: ${message}`);
                        }
                    });
            }
        }
    </script>
    <script>
        function limpa_formulário_cep() {
            // Limpa valores do formulário de cep.
            $("#address_street").val("");
            $("#address_district").val("");
            $("#address_city").val("");
            $("#address_state").val("");
        }

        function searchAddress() {
            let cep_ = $("#address_zipcode").val();
            const cep = cep_.replace("-", "");

            $("#address").show();

            if (cep == "") {
                $("#address").hide();
                limpa_formulário_cep();
                return false;
            }

            let validacep = /^[0-9]{8}$/;

            if (!validacep.test(cep)) {
                limpa_formulário_cep();
                errorToast("Algum erro aconteceu!", "Formato de CEP inválido.");
                return false;
            }

            $("#address_street").removeClass("mui--is-empty mui--is-untouched mui--is-pristine");
            $("#address_street").addClass("mui--is-not-empty mui--is-touched mui--is-dirty");
            $("#address_street").val("...");
            $("#address_district").val("...");
            $("#address_district").removeClass("mui--is-empty mui--is-untouched mui--is-pristine");
            $("#address_district").addClass("mui--is-not-empty mui--is-touched mui--is-dirty");
            $("#adress_city").removeClass("mui--is-empty mui--is-untouched mui--is-pristine");
            $("#address_city").addClass("mui--is-not-empty mui--is-touched mui--is-dirty");
            $("#address_city").val("...");
            $("#address_state").val("...");
            $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                if ("erro" in dados) {
                    limpa_formulário_cep();
                    errorToast("Algum erro aconteceu!", "CEP não encontrado.");
                    return false;
                }

                $("#address_street").val(dados.logradouro);
                $("#address_district").val(dados.bairro);
                $("#address_city").val(dados.localidade);
                $("#address_state").val(dados.uf);
                $("#address_number").removeClass("mui--is-empty mui--is-untouched mui--is-pristine");
                $("#address_number").addClass("mui--is-not-empty mui--is-touched mui--is-dirty");
                $("#address_number").focus();
            });
        }

        function changeType(type) {
            function validateCnpj(target) {
                target.setCustomValidity("");
                if (validator.onlyDigits(target.value) === "") {
                    return false;
                }
                if (!validator.isValidCnpj(target.value)) {
                    let message = "CNPJ inválido!";
                    target.setCustomValidity(message);
                    errorToast("Algum erro aconteceu!", `Veja mais em: ${message}`);
                }
            }

            function validateCpf(target) {
                target.setCustomValidity("");
                if (validator.onlyDigits(target.value) === "") {
                    return false;
                }
                if (!validator.isValidCpf(target.value)) {
                    let message = "CPF inválido!";
                    target.setCustomValidity(message);
                    errorToast("Algum erro aconteceu!", `Veja mais em: ${message}`);
                }
            }

            const countrySelected = $("#address_country").val() ?? "BRA";
            if (countrySelected === "BRA") {
                if (type == "CNPJ") {
                    $("#type").val("legal_person");
                    $("#document_number_label").html("CNPJ");
                    $("#document_number").mask("00.000.000/0000-00", {
                        reverse: true
                    })
                        .unbind("change") // MUST remove previous defined CPF onchange
                        .change(function (e) {
                            validateCnpj(e.target);
                        });

                    validateCnpj($("#document_number").get(0));

                    $("#company_data").show();
                } else {
                    $("#type").val("natural_person");
                    $("#document_number_label").html("CPF");
                    $("#document_number").mask("000.000.000-00", {
                        reverse: true
                    })
                        .unbind("change") // MUST remove previous defined CNPJ onchange
                        .change(function (e) {
                            validateCpf(e.target);
                        });

                    // validateCpf($('#document_number').get(0));
                    $("#company_data").hide();
                }
            } else if (countrySelected !== "") {
                $("#div-address-state-foreign").show();
                $("#div-address-zipcode-foreign").show();
                $("#div-address-state").hide();
                $("#div-address-zipcode").hide();
                $("#div-address-state-foreign input").removeAttr("disabled");
                $("#div-address-zipcode-foreign input").removeAttr("disabled");
                $("#div-address-state input").attr("disabled", true);
                $("#div-address-zipcode input").attr("disabled", true);
            }
        }

        function changeCountry() {
            const address_country = $("#address_country").val();
            if (address_country == "BRA" || address_country == "Brasil") {
                $(".foreign-document").hide();
                $(".brazilian-document").show();
                $("#tax_id_number").prop("disabled", true);
                $("#document_number").prop("disabled", false);

                if ($("#natural_person").prop("checked")) {
                    $("#type").val("natural_person");
                } else {
                    $("#type").val("legal_person");
                }

                $("#div-address-state-foreign").hide();
                $("#div-address-state").show();
                $("#div-address-zipcode-foreign").hide();
                $("#div-address-zipcode").show();
                $("#div-address-state input").removeAttr("disabled");
                $("#div-address-zipcode input").removeAttr("disabled");
                $("#div-address-state-foreign input").attr("disabled", true);
                $("#div-address-zipcode-foreign input").attr("disabled", true);

                // $('#address_country_BRA').show();
                // const type_person = ($('#legal_person').prop('checked')) ? 'legal_person' : 'natural_person';
                // changeType(type_person);
            } else {
                $(".brazilian-document").hide();
                $(".foreign-document").show();
                $("#type").val("legal_person");
                $("#tax_id_number").prop("disabled", false);
                $("#document_number").prop("disabled", true);
                $("#div-address-state-foreign").show();
                $("#div-address-state").hide();
                $("#div-address-zipcode-foreign").show();
                $("#div-address-zipcode").hide();
                $("#div-address-state-foreign input").removeAttr("disabled");
                $("#div-address-zipcode-foreign input").removeAttr("disabled");
                $("#div-address-state input").attr("disabled", true);
                $("#div-address-zipcode input").attr("disabled", true);
                // $('#address_country_BRA, #company_data').hide();
            }
        }

        function resendAccessData() {
            if (!confirm(`Confirma o reenvio dos dados de acesso para o assinante?`)) {
                return false;
            }

            successToast("Message", "Enviando dados...");
            $.ajax({
                type: "GET",
                url: "{{ route('subscribers.resend-data', $subscriber->id ?? 0) }}",
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                },
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (data) {
                    successToast("Enviado", data.message);
                },
                error: function (data) {
                    errorToast("Erro", data.responseJSON.message);
                }
            });
        }

    </script>
    <script>
        const elements = document.getElementsByTagName("input");
        for (let element of elements) {
            element.setAttribute("data-lpignore", "true");
            element.setAttribute("autocomplete", "off");
        }
    </script>
@endpush
@include('elements.alert')
<div class="row @if ($type=='edit' ) mt-3 @endif">
    <h5 class="my-2 mb-4">Dados pessoais</h5>
</div>
<input autocomplete="false" name="hidden" type="text" style="display:none;">
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-4">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            {!! Form::text('name', null, ['required', 'id' => 'name']) !!}
            {!! Form::label('name', '*Nome') !!}
        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-4">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            {!! Form::text('birthday', null, ['class' => 'custom-datepicker xgrow-datepicker', 'id' => 'birthday']) !!}
            {!! Form::label('birthday', 'Data de nascimento') !!}
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-4">
        <div class="xgrow-form-control mui-textfield mui-textfield--float-label">
            {!! Form::select('gender', $genders, null, ['class' => 'xgrow-select']) !!}
            {!! Form::label('gender', 'Gênero') !!}
        </div>
    </div>
</div>

<div class="row" id="address_country_BRA">
    <div class="col-sm-12 col-md-12 col-lg-4 mb-3 brazilian-document">
        <div class="subs-input-type-person d-flex flex-column">
            <p class="xgrow-medium-bold mb-2">Tipo de pessoa</p>
            <div class="xgrow-btn-group btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="type" id="natural_person" value="cpf"
                       onclick="changeType('CPF')"
                       {{ $type == 'create' && old('document_type') == 'CPF' ? 'checked' : '' }}
                       {{ isset($subscriber->document_type) && $subscriber->document_type == 'CPF' ? 'checked' : '' }}
                       checked>
                <label class="btn btn-outline-primary" for="natural_person">Física</label>

                <input type="radio" class="btn-check" name="type" id="legal_person" value="cnpj"
                       onclick="changeType('CNPJ')"
                    {{ $type == 'create' && old('document_type') == 'CNPJ' ? 'checked' : '' }}
                    {{ isset($subscriber->document_type) && $subscriber->document_type == 'CNPJ' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="legal_person">Jurídica</label>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-4 brazilian-document">
        <div class="xgrow-form-control mb-3">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('document_number', old('document_number', $subscriber->document_number ?? ''), ['id' => 'document_number']) !!}
                {!! Form::label('document_number', 'CPF', ['id' => 'document_number_label']) !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-12 col-lg-4 foreign-document">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {{ Form::hidden('type', null, ['id' => 'type']) }}
                {!! Form::text('document_number', old('document_number', $subscriber->document_number ?? ''), ['id' => 'tax_id_number', 'disabled' => true]) !!}
                {!! Form::label('document_number', 'Número do documento') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-12 col-lg-4">
        <div class="xgrow-form-control mui-textfield mui-textfield--float-label" style="margin-bottom: 16px"
             onchange="changeCountry()">
            {!! Form::select('address_country', array("" => "") + $countrys, $address_country ?? null, ['id' => 'address_country', 'class' => 'xgrow-select']) !!}
            {!! Form::label('address_country', 'País') !!}
        </div>
    </div>
</div>

<div class="row" id="company_data" style="display:{{ !empty(old('company_name')) ? '' : 'none' }} ">
    <div class="col-md-12 col-lg-6">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('company_name', null) !!}
                {!! Form::label('company_name', 'Nome da empresa') !!}
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('tax_id_br_ie', null) !!}
                {!! Form::label('tax_id_br_ie', 'Inscrição estadual') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('tax_id_br_im', null) !!}
                {!! Form::label('tax_id_br_im', 'Inscrição municipal') !!}
            </div>
        </div>
    </div>
</div>

<hr class="mt-0" style="border-color: var(--border-color)"/>

<div class="row">
    <h5 class="my-2 mb-4">Dados da conta</h5>
    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="xgrow-form-control mb-3">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::tel('main_phone', null, ['id' => 'main_phone']) !!}
                {!! Form::label('main_phone', 'Telefone principal') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::tel('cel_phone', null, ['id' => 'cel_phone']) !!}
                {!! Form::label('cel_phone', 'Celular') !!}
            </div>
        </div>
    </div>

    @if ($type == 'edit')
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
            <div class="xgrow-form-control mb-3">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('r_data', $register_data, ['readonly' => true, 'disabled' => true]) !!}
                    {!! Form::label('r_data', 'Data de cadastro') !!}
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
            <div class="xgrow-form-control mb-3">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('r_integration', $integration, ['readonly' => true, 'disabled' => true]) !!}
                    {!! Form::label('r_integration', 'Integração') !!}
                </div>
            </div>
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-12">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::email('email', null, ['required' => true, 'autocomplete' => 'off', 'data-lpignore' => 'true']) !!}
                {!! Form::label('email', '*Email') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::password('password', null, ['id' => 'password', 'autocomplete' => 'false', 'pattern' => '(?=.*\d)(?=.*[a-z]).{5,}', 'title'=>'Obrigatório no mínimo 5 caracteres incluindo: letras, números e pelo menos um caractere especial.']) !!}
                {!! Form::label('password', '*Senha') !!}
            </div>
            <div class="password-policies">
                <div class="policy-length">
                    5 caracteres.
                </div>
                <div class="policy-number">
                    Contém números.
                </div>
                <div class="policy-letter">
                    Contém letras.
                </div>
                <div class="policy-special">
                    Contém caracteres especiais.
                </div>
            </div>
            @if ($type == 'edit')
                <p class='xgrow-card-subtitle'>Para manter a mesma senha, deixe estes dois campos vazios!</p>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::password('re_password', null, ['id' => 're_password', 'autocomplete' => 'off', 'pattern' => '(?=.*\d)(?=.*[a-z]).{5,}', 'title'=>'Obrigatório no mínimo 5 caracteres incluindo: letras, números e pelo menos um caractere especial.']) !!}
                {!! Form::label('re_password', '*Repita sua senha') !!}
            </div>
        </div>
        @isset($subscriber->id)
            <div class="row">
                <div class="col-md-12 d-flex mb-3" style="justify-content: flex-end;">
                    <button class="xgrow-button w-auto px-2 d-none" type="button" onclick="resendAccessData()">Reenviar
                        dados de
                        acesso
                    </button>
                </div>
            </div>
        @endisset
    </div>
</div>

<hr class="mt-0" style="border-color: var(--border-color)"/>

<div class="row">
    <h5 class="my-2 mb-4">Endereço</h5>
</div>

<div class="row mb-3">
    <div class="col-sm-12 col-md-4 col-lg-4">
        <div id="div-address-zipcode">
            <div class="xgrow-form-control">
                <div class="xgrow-floating-input input-search mui-textfield mui-textfield--float-label">
                    {!! Form::text('address_zipcode', null, ['id' => 'address_zipcode']) !!}
                    {!! Form::label('address_zipcode', 'CEP') !!}
                </div>
            </div>
        </div>
        <div id="div-address-zipcode-foreign" style="display:none">
            <div class="xgrow-form-control">
                <div class="xgrow-floating-input input-search mui-textfield mui-textfield--float-label">
                    {!! Form::text('address_zipcode', null, ['id' => 'address_zipcode_foreign', 'disabled' => 'disabled']) !!}
                    {!! Form::label('address_zipcode', 'Postcode') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('address_district', null, ['id' => 'address_district']) !!}
                {!! Form::label('address_district', 'Bairro') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('address_city', null, ['id' => 'address_city']) !!}
                {!! Form::label('address_city', 'Cidade') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-2 col-lg-2">
        <div id="div-address-state">
            <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                {!! Form::select('address_state', $states, null, ['class' => 'xgrow-select', 'id' => 'address_state', 'placeholder' => '']) !!}
                {!! Form::label('address_state', 'Estado') !!}
            </div>
        </div>
        <div id="div-address-state-foreign" style="display:none">
            <div class="xgrow-form-control">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('address_state', null, ['id' => 'address_state_foreign', 'disabled' => 'disabled']) !!}
                    {!! Form::label('address_state', 'Estado') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('address_street', null, ['id' => 'address_street']) !!}
                {!! Form::label('address_street', 'Rua') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('address_number', null, ['id' => 'address_number']) !!}
                {!! Form::label('address_number', 'Nº') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="xgrow-form-control">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('address_comp', null) !!}
                {!! Form::label('address_comp', 'Complemento') !!}
            </div>
        </div>
    </div>
</div>
