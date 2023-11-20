@section('jquery')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery/jquery.min.js"></script>
    <script>
        $(function() {
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

            $('input:radio[name="radioDocumentNumber"]').change(function() {

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
    </script>
@endsection

@push('after-scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endpush

<form class="" id="formSubscriber" method="POST" action="{{ route('mundipagg.store') }}">
    @csrf
    <div class="form-group">
        <label for="name">Nome completo</label>
{{--        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Seu nome completo" required>--}}
        <input type="text" class="form-control" id="name" name="name" value="" placeholder="Seu nome completo" required>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">E-mail</label>
{{--                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Seu email" required>--}}
                <input type="email" class="form-control" id="email" name="email" value="" placeholder="Seu email" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="emailConfirm">Confirme seu e-mail</label>
{{--                <input type="email" class="form-control" id="emailConfirm" name="emailConfirm" value="{{ old('email') }}" placeholder="Confirme seu email" required>--}}
                <input type="email" class="form-control" id="emailConfirm" name="emailConfirm" value="" placeholder="Confirme seu email" required>
            </div>
        </div>
    </div>
    <div class="row @if($plan->trigger_email === 1) d-none @endif">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">Senha</label>
{{--                <input type="password" class="form-control" id="password" name="password" value="" placeholder="Digite uma senha" @if($plan->trigger_email === 0) required @endif>--}}
                <input type="password" class="form-control" id="password" name="password" value="" placeholder="Digite uma senha" @if($plan->trigger_email === 0) required @endif>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="passwordConfirm">Repita a senha</label>
{{--                <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm" value="" placeholder="Digite novamente sua senha" @if($plan->trigger_email === 0) required @endif>--}}
                <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm" value="" placeholder="Digite novamente sua senha" @if($plan->trigger_email === 0) required @endif>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <div class="form-check form-check-inline" >
                    <input class="form-check-input" type="radio" name="radioDocumentNumber" id="radioCpf" value="cpf" @if(old('radioDocumentNumber') === 'cpf') checked @endif>
                    <label class="form-check-label" for="radioCpf">CPF</label>
                </div>

                <div class="form-check form-check-inline" >
                    <input class="form-check-input" type="radio" name="radioDocumentNumber" id="radioCNPJ" value="cnpj" @if(old('radioDocumentNumber') === 'cnpj') checked @endif>
                    <label class="form-check-label" for="radioCNPJ">CNPJ</label>
                </div>
                <input type="text" class="form-control" name="cpf_number" id="cpf_number" value="{{ old('cpf_number') }}" id="cpf" placeholder="Digite seu CPF">
                <input type="text" class="form-control" name="cnpj_number" id="cnpj_number" value="{{ old('cnpj_number') }}" id="cnpj" placeholder="Digite seu CNPJ">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="cel_phone">Celular</label>
                <div class="input-group">
{{--                    <input type="text" class="form-control" id="cel_phone" name="cel_phone" value="{{ old('cel_phone') }}" placeholder="(00) 00000-0000" required>--}}
                    <input type="text" class="form-control" id="cel_phone" name="cel_phone" value="" placeholder="(00) 00000-0000" required>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="address_zipcode">CEP</label>
                <div class="input-group">
{{--                    <input type="text" class="form-control" id="address_zipcode" name="address_zipcode" value="{{ old('address_zipcode') }}" placeholder="Digite aqui seu CEP" required>--}}
                    <input type="text" class="form-control" id="address_zipcode" name="address_zipcode" value="" placeholder="Digite aqui seu CEP" required>
                    <div class="input-group-append" onclick="searchAddress()">
                        <span style="background-color: #FFF;cursor: pointer;" class="input-group-text fa fa-search"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="address">
        <div class="row">
            <div class="col-md-2">
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
                    <input class="form-control" id="address_city" type="text" name="address_city" maxlength="255" value="{{ old('address_city') }}" placeholder="Onde voce mora?" readonly >
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="address_district">Bairro</label>
                    <input class="form-control" id="address_district" type="text" name="address_district" maxlength="50" value="{{ old('address_district') }}" placeholder="Digite aqui o seu bairro" readonly >
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address_street">Endereço</label>
                    <input class="form-control" type="text" maxlength="255" id="address_street" name="address_street" value="{{ old('address_street') }}" placeholder="Qual seu endereço?" readonly >
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="address_number">Número</label>
                    <input class="form-control" id="address_number" type="text" name="address_number" maxlength="8" value="{{ old('address_number') }}" placeholder="" required>
                    <div class="input-element__invalid-feedback"><!----></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="address_comp">Complemento</label>
                    <input class="form-control" id="address_comp" type="text" name="address_comp" maxlength="70" placeholder="" value="{{ old('address_comp') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <input type="hidden" class="form-control" id="platform_id" name="platform_id" value="{{ $platform_id }}" >
        <input type="hidden" class="form-control" id="plan_id" name="plan_id" value="{{ $plan->id }}" >
        <input type="hidden" class="form-control" id="course_id" name="course_id" value="{{ $course_id ?? 0 }}" >
    </div>
    <div class="row">
        <div class="col-md-12" style="text-align: right;">
            <button type="submit" class="btn btn-lg btn-success shadow p-3 mb-5">Próximo passo</button>
        </div>
    </div>
</form>
