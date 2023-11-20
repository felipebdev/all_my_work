@section('js-personal-data')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery/jquery.min.js"></script>
    <script>
        $(function () {
            $('#cpf_number').mask('000.000.000-00', {reverse: true});
            $('#cnpj_number').mask('99.999.999/9999-99', {reverse: true});
            $('#address_zipcode').mask('00000-000');
            $('#cel_phone').mask('(00) 00000-0000');

            $("#cpf_number").hide();
            $("#cnpj_number").hide();

            if ($("#address_zipcode").val() != '') {
                $("#address_number").attr('required', true);
            } else {
                $("#address_number").attr('required', false);
            }

            if ($('input:radio[name="radioDocumentNumber"]').val() === 'cpf') {
                $("#cpf_number").show();
                $("#cpf_number").attr('required');
                $("#radioCpf").prop('checked', true);
            } else if ($('input:radio[name="radioDocumentNumber"]').val() === 'cnpj') {
                $("#cnpj_number").show();
                $("#cnpj_number").attr('required');
                $("#radioCnpj").prop('checked', true);
            }

            $('input:radio[name="radioDocumentNumber"]').change(function () {

                $("#cpf_number").hide();
                $("#cnpj_number").hide();

                $("#cpf_number").val('');
                $("#cnpj_number").val('');

                $("#cpf_number").removeAttr('required');
                $("#cnpj_number").removeAttr('required');

                if ($("input[name='radioDocumentNumber']:checked")) {
                    $("#" + $(this).val() + "_number").show();
                    $("#" + $(this).val() + "_number").attr('required');
                    // $("#div"+$(this).val()).show();
                }
            });
        });


        $(".save-data").click(function (event) {
            event.preventDefault();

            var data = $('#form-personal-data').serializeArray().reduce(function (obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});

            if ($('#form-personal-data')[0].checkValidity()) {

                if( $("#cpf_number").val().length == 0 && $("#cnpj_number").val().length == 0 && $("#radioEstrangeiro").is(':checked') == false ) {
                    return;
                }

                $.ajax({
                    url: "{{route('mundipagg.subscriber.create')}}",
                    type: "POST",
                    data: data,
                    success: function (response) {
                        if (response) {
                            @isset($platform->pixel_id)
                                fbq('track', 'CompleteRegistration');
                            @endisset
                            $(".tab-personal-data").addClass('complete');
                            $("#tab-personal-data").hide();
                            $("#tab-payment").tab('show');
                            $(".tab-payment").addClass('active');

                            $(':input[name]', $('#form-personal-data')).each(function() {
                                $('#hidden_fields').append(this);
                            })
                            var input_subscriber = "<input type=\"hidden\" class=\"form-control\" id=\"subscriber_id\" name=\"subscriber_id\" value=\""+response.id+"\">";
                            $('#form-payment').append(input_subscriber);
                            $("#card-number").focus();
                        }
                    },
                    error: function (response) {
                        $("#error").html('');
                        $("#error").append(response.responseText);
                        $("#error").show();
                        $(window).scrollTop(0);
                    },
                });
            } else {
                $('#form-personal-data')[0].reportValidity();
            }
        });

        function changeDocumentType(documentType) {
            if( documentType == 'estrangeiro' ) {
                $(".input-group-append").hide();
                $("#address_zipcode").removeAttr('onclick');
                $('#address_zipcode').mask('0000000000000000');
                $("#address").show();
                $("#address_city").removeAttr('readonly');
                $("#address_district").removeAttr('readonly');
                $("#address_street").removeAttr('readonly');
                $("#div_country").show();
                $("#div_state").hide();
                $("#div_other_state").show();
            }
            else
            {
                $(".input-group-append").show();
                $("#address_zipcode").attr('onclick', 'searchAddress()');
                $('#address_zipcode').mask('00000-000');
                $("#address").hide();
                $("#address_city").attr('readonly');
                $("#address_district").attr('readonly');
                $("#address_street").attr('readonly');
                $("#div_country").hide();
                $("#div_state").show();
                $("#country").val("BR");
                $("#div_other_state").hide();
                $("#alt_address_state").val("");
            }
        }
    </script>
@endsection

@push('after-scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endpush

<form id="form-personal-data">
    @csrf
    <div class="form-group">
        <label for="name">Nome completo</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
               placeholder="Seu nome completo" required>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                       placeholder="Seu email" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="emailConfirm">Confirme seu e-mail</label>
                <input type="email" class="form-control" id="emailConfirm" name="emailConfirm"
                       value="{{ old('email') }}" placeholder="Confirme seu email" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radioDocumentNumber" id="radioCpf" value="cpf"
                           @if(old('radioDocumentNumber') === 'cpf') checked @endif onclick="changeDocumentType(this.value);">
                    <label class="form-check-label" for="radioCpf">CPF</label>

                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radioDocumentNumber" id="radioCNPJ" value="cnpj"
                           @if(old('radioDocumentNumber') === 'cnpj') checked @endif onclick="changeDocumentType(this.value);">
                    <label class="form-check-label" for="radioCNPJ">CNPJ</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radioDocumentNumber" id="radioEstrangeiro" value="estrangeiro"
                           @if(old('radioDocumentNumber') === 'estrangeiro') checked @endif onclick="changeDocumentType(this.value);">
                    <label class="form-check-label" for="radioEstrangeiro">Sou estrangeiro</label>
                </div>
                <input type="text" class="form-control" name="cpf_number" id="cpf_number" required
                       value="{{ old('cpf_number') }}" id="cpf" placeholder="Digite seu CPF">
                <input type="text" class="form-control" name="cnpj_number" id="cnpj_number"
                       value="{{ old('cnpj_number') }}" id="cnpj" placeholder="Digite seu CNPJ">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="cel_phone">Celular</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="cel_phone" name="cel_phone"
                           value="{{ old('cel_phone') }}" placeholder="(00) 00000-0000" required>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="address_zipcode">CEP</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="address_zipcode" name="address_zipcode"
                           value="{{ old('address_zipcode') }}" placeholder="Digite aqui seu CEP" required>
                    <div class="input-group-append" onclick="searchAddress()">
                        <span style="background-color: #FFF;cursor: pointer;"
                              class="input-group-text fa fa-search"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="address">
        <div class="row">
            <div class="col-md-2" id="div_country" style="display: none;">
                <div class="form-group">
                    <label for="address_state">País</label>
                    <select class="form-control" size="1" id="country" name="country">
                        <option value="DE">Alemanha</option>
                        <option value="AR">Argentina</option>
                        <option value="AU">Austrália</option>
                        <option value="BO">Bolívia</option>
                        <option value="BR" selected>Brasil</option>
                        <option value="CA">Canada</option>
                        <option value="CL">Chile</option>
                        <option value="CO">Colômbia</option>
                        <option value="CR">Costa Rica</option>
                        <option value="CU">Cuba</option>
                        <option value="ES">Espanha</option>
                        <option value="US">Estados Unidos</option>
                        <option value="SV">El Salvador</option>
                        <option value="EC">Equador</option>
                        <option value="FR">França</option>
                        <option value="GT">Guatemala</option>
                        <option value="HT">Haiti</option>
                        <option value="HN">Honduras</option>
                        <option value="MX">México</option>
                        <option value="NI">Nicarágua</option>
                        <option value="PA">Panamá</option>
                        <option value="PY">Paraguai</option>
                        <option value="PE">Peru</option>
                        <option value="PT">Portugal</option>
                        <option value="DO">República Dominicana</option>
                        <option value="UY">Uruguai</option>
                        <option value="VE">Venezuela</option>
                        <option value="IE">Irlanda</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2" id="div_other_state" style="display: none">
                <div class="form-group">
                    <label for="address_state">Estado</label>
                    <input class="form-control" id="alt_address_state" type="text" name="alt_address_state" maxlength="2"
                           value="{{ old('address_number') }}" placeholder="">
                </div>
            </div>
            <div class="col-md-2" id="div_state">
                <div class="form-group">
                    <label for="address_state">Estado</label>
                    <select class="form-control" readonly size="1" id="address_state" name="address_state">
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AP">AP</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MT">MT</option>
                        <option value="MS">MS</option>
                        <option value="MG">MG</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PR">PR</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RS">RS</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="SC">SC</option>
                        <option value="SP">SP</option>
                        <option value="SE">SE</option>
                        <option value="TO">TO</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="address_city">Cidade</label>
                    <input class="form-control" id="address_city" type="text" name="address_city" maxlength="255"
                           value="{{ old('address_city') }}" placeholder="Onde voce mora?" readonly required>
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="address_district">Bairro</label>
                    <input class="form-control" id="address_district" type="text" name="address_district" maxlength="50"
                           value="{{ old('address_district') }}" placeholder="Digite aqui o seu bairro" readonly required>
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address_street">Endereço</label>
                    <input class="form-control" type="text" maxlength="255" id="address_street" name="address_street"
                           value="{{ old('address_street') }}" placeholder="Qual seu endereço?" readonly required>
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="address_number">Número</label>
                    <input class="form-control" id="address_number" type="text" name="address_number" maxlength="8"
                           value="{{ old('address_number') }}" placeholder="" required>
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="address_comp">Complemento</label>
                    <input class="form-control" id="address_comp" type="text" name="address_comp" maxlength="70"
                           placeholder="" value="{{ old('address_comp') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <input type="hidden" class="form-control" id="platform_id" name="platform_id" value="{{ $platform_id }}">
        <input type="hidden" class="form-control" id="plan_id" name="plan_id" value="{{ base64_encode($plan->id) }}">
        <input type="hidden" class="form-control" id="course_id" name="course_id" value="{{ $course_id ?? 0 }}">
        <input type="hidden" id="installmentSelected" name="installmentSelected">
    </div>
    <div class="row">
        <div class="col-md-12" style="text-align: right;">
            <button class="btn btn-lg btn-success shadow p-3 mb-5 save-data">Próximo passo</button>
        </div>
    </div>
</form>
