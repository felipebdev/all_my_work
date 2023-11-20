@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
@endpush

@push('after-scripts')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="../script/select-tags.js"></script>

    <script>
        $(".xgrow-select-tag").select2({
            width: 'resolve'
        });
    </script>

@endpush


<div class="xgrow-card card-dark p-0">
    <div class="xgrow-card-body px-3 pt-4">
        <div class="row mb-2">
            <div class="row mb-4">
                <div class="col-xl-6 col-md-12">
                    <div class="d-flex flex-row">
                        <div class="col-xl-6 col-md-12">
                            <div class="d-flex flex-row">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="ckbx-style-1-1" name="status"
                                           value="1" @if (isset($plan->status))
                                           {{$plan->status == 1 ? 'checked' : ''}}
                                           @else
                                           checked
                                        @endif
                                    >
                                </div>
                                <p for="me-2" class="mr-3">Status</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-12 d-flex">
                    <div class="d-flex flex-row">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="ckbx-style-1-2" name="trigger_email"
                                   value="1" @if (isset($plan->trigger_email)) {{$plan->trigger_email == 1 ? 'checked' : ''}} @endif >
                        </div>
                        <p for="trigger_email" class="mr-2">Enviar e-mail quando um novo assinante for incluído</p>
                    </div>
                </div>
                <div class="row">
                    <p style="font: var(--text-medium-regular);">*Caso o link de checkout seja preenchido,
                        o usuário será redirecionado para essa URL ao finalizar, com sucesso, o checkout.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('image','Imagem(1074x350):') !!}
                            {!! UpImage::getImageTag( (isset($plan->id) ? $plan : new \App\Plan()), 'image') !!}
                        </div>
                        <div class="col-md-9" style="padding-top: 15px">
                            {!! UpImage::getUploadButton('thumb', 'btn btn-themecolor btn-upload-image') !!}
                        </div>
                    </div>
                </div>
                <div>
                    <p class="font-weight-bold">Mais opções:</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" style="padding-top: 15px;">
                <div class="xgrow-floating-input mui-textfield mui-textfield-float-label">
                    <label for="name">Nome</label>
                    <input type="text" autocomplete="off" spellcheck="false" id="name" name="name"
                           value="{{ $plan->name ?? '' }}" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="xgrow-form-control" style="margin-bottom: 16px;">
                            <label for="type_plan">Tipo de plano</label>
                            <select class="xgrow-select" id="type_plan" name="type_plan">
                                <option value="R"
                                        @if($plan->type_plan !== 'P' || $plan->type_plan === '') selected @endif>
                                    Assinatura
                                </option>
                                <option value="P" @if($plan->type_plan !== 'R') selected @endif>Venda única
                                    (parcelada)
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="xgrow-form-control @if($plan->type_plan !== 'P') d-none @endif"
                             id="div_installment">
                            <label for="installment">Quantidade parcelas</label>
                            <select class="xgrow-select" id="installment" name="installment">
                                <option value="0">Selecione</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" @if($plan->installment === $i) selected @endif>{{ $i }}x
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="xgrow-form-control @if($plan->type_plan !== 'R') d-none @endif" id="div_period">
                            <label for="recurrence">Periodicidade</label>
                            <select class="xgrow-select" id="recurrence" name="recurrence">
                                @foreach ($recurrence as $key => $value)
                                    <option
                                        value="{{$key}}" {{isset($plan->recurrence) && $plan->recurrence == $key ? 'selected' : ''}}>
                                        {{$value}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="xgrow-floating-input mui-textfield mui-textfield-float-label">
                    <label for="charge_until">Forma de cobrança</label>
                    <input type="text" min="0" step="any" id="charge_until" name="charge_until" spellcheck="false"
                           value="{{$plan->charge_until ?? 0}}" required>
                    <!-- <small>Deixe '0' caso queira cobrar até cancelar</small> -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="xgrow-form-control">
                    <label for="currency">Moeda</label>
                    <select class="xgrow-select" id="currency" name="currency">
                        @foreach ($currency as $key => $value)
                            <option
                                value="{{$key}}" {{isset($plan->currency) && $plan->currency == $key ? 'selected' : ''}}>
                                {{$value}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="xgrow-floating-input mui-textfield mui-textfield-float-label">
                    <label for="price">Valor</label>
                    <input type="text" min="0" step="any" id="price" name="price"
                           value="{{ (isset($plan->price)) ?  number_format($plan->price, 2, ',', '.') : '' }}"
                           onkeypress="$(this).mask('#.##0,00', {reverse: true})" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-md-12">
                <div class="xgrow-form-control">
                    <div class="d-flex flex-row">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="use_promotional_price"
                                   name="use_promotional_price"
                                   value="1" @if (isset($plan->use_promotional_price)) {{$plan->use_promotional_price == 1 ? 'checked' : ''}} @endif >
                            <!-- <label for="use_promotional_price"></label> -->
                        </div>
                        <p for="use_promotional_price" class="mr-2">Utilizar valor promocional</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="padding-top: 15px;">
            <div class="col-md-3">
                <div
                    class="xgrow-floating-input mui-textfield mui-textfield-float-label promotional_price @if (isset($plan->use_promotional_price)) {{$plan->use_promotional_price == 1 ? '' : 'd-none'}} @endif ">
                    <label for="price">Valor promocional</label>
                    <input type="text" min="0" step="any" id="promotional_price" name="promotional_price"
                           value="{{ (isset($plan->promotional_price)) ?  number_format($plan->promotional_price, 2, ',', '.') : '' }}"
                           onkeypress="$(this).mask('#.##0,00', {reverse: true})" @if (isset($plan->use_promotional_price)) {{$plan->use_promotional_price == 1 ? 'required' : ''}} @endif >
                </div>
            </div>
            <div class="col-md-3">
                <div
                    class="xgrow-floating-input mui-textfield mui-textfield-float-label promotional_price @if (isset($plan->use_promotional_price)) {{$plan->use_promotional_price == 1 ? '' : 'd-none'}} @endif ">
                    <label for="charge_until">Períodos promocionais</label>
                    <input type="number" min="0" step="any" id="promotional_periods" name="promotional_periods"
                           value="{{$plan->promotional_periods ?? 1}}" @if (isset($plan->use_promotional_price)) {{$plan->use_promotional_price == 1 ? 'required' : ''}} @endif >
                </div>
            </div>
        </div>

        <div class="row">
            <div class="d-flex flex-column">
                <p class="xgrow-medium-bold" style="color: var(--font-color);">Tipos de pagamento</p>
                <select class="xgrow-select-tag tags" name="states[]" multiple="multiple">
                    <option value="AL">Cartão de crédito</option>
                    <option value="AL">Cartão de débito</option>
                    <option value="AL">Boleto</option>
                    <option value="AL">PIX</option>
                    <option value="AL">PayPal</option>
                    <option value="AL">Bitcoin</option>
                    <option value="AL">TED/DOC</option>
                    <option value="WY">Cortesia</option>
                </select>
            </div>
        </div>

        <div class="row" style="padding-top: 15px;">
            <div class="col-md-6">
                <div class="xgrow-form-control">
                    <label for="freedays_type">Tipo de teste</label>
                    <select class="xgrow-select" id="freedays_type" name="freedays_type">
                        @foreach ($freedays_type as $key => $value)
                            <option
                                value="{{$key}}" {{isset($plan->freedays_type) && $plan->freedays_type == $key ? 'selected' : ''}}>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6" style="padding-top: 20px;">
                <div class="xgrow-floating-input mui-textfield mui-textfield-float-label">
                    <label for="freedays">Dias de teste</label>
                    <input type="number" min="0" step="any" id="freedays" name="freedays"
                           value="{{$plan->freedays ?? 7}}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="xgrow-form-control">
                    <label for="freedays_type">Gateway</label>
                    <select class="xgrow-select" id="integration_id"
                            name="integration_id" {{ isset($plan->integration->integration_id) ? "disabled='disabled'" : '' }}>
                        <option value="">Selecione um gateway</option>
                        @foreach ($gateways as $gateway)
                            <option
                                value="{{$gateway->id}}" {{isset($plan->integration->integration_id) && $plan->integration->integration_id == $gateway->id ? 'selected' : '' }}>{{$gateway->name_integration}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="xgrow-floating-input mui-textfield mui-textfield-float-label" id="div_link">
                    <label for="url_checkout">Link do checkout</label>
                    <!-- <div class="xgrow-floating-input mui-textfield mui-textfield-float-label"> -->
                    <input type="text" id="url_checkout" name="url_checkout"
                           value="{{ isset($urlCheckout) ? $urlCheckout : '' }}" readonly>
                    <!-- <div class="input-group-append" onclick="copyUrlCheckout()">
                            <span style="background-color: #FFF;cursor: pointer;" class="input-group-text fa fa-copy"></span>
                        </div> -->
                    <span onclick="document.getElementById('checkout_link_confirm').value = ''"></span>
                    <!-- </div> -->
                </div>
            </div>

        </div>

        <div id="div_custom" class="row @if(!$showDivCustom) d-none @endif">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Descrição</label>
                    {!! Form::textarea('content_html',
                    $plan->description ?? '',
                    [
                    'name' => 'description',
                    'id'=>'description',
                    'class' => 'content_html form-control',
                    'rows' => '10'
                    ]) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="message_success_checkout">Mensagem de confirmação (checkout)</label>
                    {!! Form::textarea('content_html',
                    $plan->message_success_checkout ?? '',
                    [
                    'name' => 'message_success_checkout',
                    'id'=>'message_success_checkout',
                    'class' => 'content_html form-control',
                    'rows' => '10'
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="xgrow-form-control">
                    <label for="order_bump_plan_id">Order Bump</label>
                    <select class="xgrow-select" id="order_bump_plan_id" name="order_bump_plan_id">
                        <option value="" {{!isset($plan->order_bump_plan_id) ? 'selected' : ''}}></option>
                        @foreach (\App\Plan::where('platform_id', Auth::user()->platform_id)->get() as $key => $value)
                            <option
                                value="{{$value->id}}" {{isset($plan->order_bump_plan_id) && $plan->order_bump_plan_id == $value->id ? 'selected' : ''}}>{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">

                    <div class="xgrow-floating-input mui-textfield mui-textfield-float-label">
                        <label for="url_checkout">URL de confirmaçao (checkout)</label>
                        <input type="text" id="url_checkout_confirm" autocomplete="off" name="url_checkout_confirm"
                               value="{{ $plan->url_checkout_confirm ?? '' }}">
                        <span onclick="document.getElementById('url_checkout_confirm').value = ''"></span>
                    </div>

                    <!-- <small>Caso preenchido redireciona o usuário para a url ao finalizar com sucesso o checkout</small> -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="xgrow-form-control">
                    <label for="upsell_plan_id">Upsell</label>
                    <select class="xgrow-select" id="upsell_plan_id" name="upsell_plan_id">
                        <option value="" {{!isset($plan->upsell_plan_id) ? 'selected' : ''}}></option>
                        @foreach (\App\Plan::where('platform_id', Auth::user()->platform_id)->get() as $key => $value)
                            <option
                                value="{{$value->id}}" {{isset($plan->upsell_plan_id) && $plan->upsell_plan_id == $value->id ? 'selected' : ''}}>{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="divUnlimitedSale"
                 class="col-md-6 d-flex flex-row align-items-center {{ ( isset($plan->type_plan) && $plan->type_plan == 'R' ) ? 'd-none' : 'd-flex' }}">

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="ckbx-unlimited_sale" name="unlimited_sale"
                           value="1" @if (isset($plan->unlimited_sale))
                        {{$plan->unlimited_sale == 1 ? 'checked' : ''}}
                        @endif
                    >
                    <!-- <label for="ckbx-unlimited_sale"></label> -->
                </div>
                <p for="unlimited_sale" class="mr-3">Venda sem limite</p>
            </div>
        </div>

        <input type="hidden" id="plan_id" name="plan_id" value="{{ $plan->id ?? 0 }}">
        <input type="hidden" id="platform_id" name="platform_id" value="{{Auth::user()->platform_id}}">

    </div>
</div>
