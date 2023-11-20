@push('after-scripts')
    <script>
        $('#removeImage').on('click', function () {
            $('#image').attr('src', '/xgrow-vendor/assets/img/big-file.png');
            $('#image_upimage_file_id').val(0);
            $('#image_upimage_url').val('');
        });
    </script>
@endpush

<div class="tab-pane fade show {{ !Request::get('delivery') ? 'active' : '' }}" id="nav-plan" role="tabpanel" aria-labelledby="nav-plan-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-header pb-3">
            <div class="d-flex align-items-center px-3">
                <div class="form-check form-switch">
                    {!! Form::checkbox('status', true, (old('status', $plan->status ?? true)), ['id' => 'status', 'class' => 'form-check-input']) !!}
                    {!! Form::label('status', 'Ativar produto', ['class' => 'form-check-label']) !!}
                </div>
            </div>

        </div>
        <hr class="mt-0" style="border-color: var(--border-color)"/>
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Informações do produto
            </h5>
            <div class="row">
                <div class="col-lg-7 col-md-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('name', null, ['id' => 'name', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'required']) !!}
                                {!! Form::label('name', 'Nome') !!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                {!! Form::select('type_plan', ['P' => 'Venda única', 'R' => 'Assinatura'], null, ['id' => 'type_plan', 'required', 'class' => 'xgrow-select', 'placeholder' => '']) !!}
                                {!! Form::label('type_plan', 'Tipo de pagamento:') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                {!! Form::select('currency', $currency, null, ['id' => 'currency', 'required', 'class' => 'xgrow-select form-check-input', 'readonly' => true]) !!}
                                {!! Form::label('currency', 'Tipo da moeda:') !!}

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('price', null, ['id' => 'price', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-not-empty mui--is-untouched mui--is-pristine', 'required', 'maxlength' => 10]) !!}
                                {!! Form::label('price', 'Valor') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="div_category" class="col-lg-12">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                <select class="xgrow-select" id="category_id" name="category_id">
                                    <option value="" selected disabled></option>
                                    @foreach ($categories as $key => $value)
                                        <option value="{{ $value->id }}"
                                            {{ isset($plan->category_id) && $plan->category_id == $value->id ? 'selected' : '' }}>
                                            {{ $value->name }}</option>
                                    @endforeach
                                </select>
                                {!! Form::label('category_id', 'Categoria:') !!}
                            </div>
                        </div>
                        <div id="div_installment" class="col-lg-6 col-md-6 col-sm-12 d-none">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::select('installment', ['1' => '1x', '2' => '2x', '3' => '3x', '4' => '4x', '5' => '5x', '6' => '6x', '7' => '7x', '8' => '8x', '9' => '9x', '10' => '10x', '11' => '11x', '12' => '12x'], null, ['id' => 'installment', 'class' => 'xgrow-select']) !!}
                                {!! Form::label('installment', 'Nº máximo de parcelas:') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 mb-4">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-check form-switch">
                                {!! Form::checkbox('trigger_email', true, true, ['id' => 'trigger_email', 'class' => 'form-check-input']) !!}

                                {!! Form::label('trigger_email', 'Enviar e-mail com dados de acesso assim que pagamento for efetuado?', ['class' => 'form-check-label']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-lg-12 col-md-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::textarea('description', null, ['id' => 'description', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none']) !!}
                                {!! Form::label('description', 'Descreva aqui a descrição do produto principal...') !!}
                            </div>
                        </div>
                    </div>
                    <div id="div-subscription-fields" style="display:none">
                        <div class="row mb-3">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-check form-switch">
                                    {!! Form::checkbox('charge_until', 0, isset($plan->charge_until) && old('charge_until', $plan->charge_until) == 0, ['id' => 'chk-charge-until', 'class' => 'form-check-input']) !!}
                                    {!! Form::label('charge_until', 'Cobrança ilimitada', ['class' => 'form-check-label']) !!}
                                </div>
                                <small class="xgrow-medium-light-italic">Quando habilitado o aluno será cobrado até
                                    cancelar</small>
                            </div>
                        </div>
                        <div id="div-unlimited-billing" class="row d-none">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div
                                    class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                    {!! Form::select('recurrence', ['7' => 'Semanal', '30' => 'Mensal', '60' => 'Bimestral', '90' => 'Trimestral', '180' => 'Semestral', 360 => 'Anual'], null, ['class' => 'xgrow-select slc-recurrence']) !!}
                                    {!! Form::label('recurrence', 'Periodicidade:') !!}
                                </div>
                            </div>
                        </div>
                        <div id="div-billing" class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    {!! Form::number('charge_until', null, ['autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'min' => 1]) !!}
                                    {!! Form::label('charge_until', 'Limite de cobranças') !!}
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div
                                    class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    {!! Form::select('recurrence', ['7' => 'Semanal', '30' => 'Mensal', '60' => 'Bimestral', '90' => 'Trimestral', '180' => 'Semestral', '360' => 'Anual'], null, ['class' => 'xgrow-select slc-recurrence']) !!}
                                    {!! Form::label('recurrence', 'Periodicidade:') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-12">
                            <h6>Forma de pagamento</h6>
                            <div id="div-payment-type-subscription" style="display:none">
                                <div class="form-check form-switch">
                                    {!! Form::checkbox('payment_method_credit_card', true, true, ['class' => 'form-check-input', 'onclick' => 'return false', 'disabled' => true]) !!}
                                    {!! Form::label('payment_method_credit_card', 'Cartão', ['class' => 'form-check-label']) !!}
                                </div>
                            </div>
                            <div id="div-payment-type-sell">
                                <div class="d-flex flex-wrap">
                                    <div class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_boleto', true, old('payment_method_boleto', $plan->payment_method_boleto ?? false), ['class' => 'form-check-input']) !!}
                                        {!! Form::label('payment_method_boleto', 'Boleto', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_credit_card', true, old('payment_method_credit_card', $plan->payment_method_credit_card ?? false), ['class' => 'form-check-input']) !!}
                                        {!! Form::label('payment_method_credit_card', 'Cartão', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_pix', true, old('payment_method_pix', $plan->payment_method_pix ?? false), ['class' => 'form-check-input']) !!}
                                        {!! Form::label('payment_method_pix', 'Pix', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_multiple_cards', true, old('payment_method_multiple_cards', $plan->payment_method_multiple_cards ?? false), ['class' => 'form-check-input']) !!}
                                        {!! Form::label('payment_method_multiple_cards', 'Múltiplos cartões', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3">
                                        {!! Form::checkbox('unlimited_sale', true, old('unlimited_sale', $plan->unlimited_sale ?? false), ['class' => 'form-check-input']) !!}
                                        {!! Form::label('unlimited_sale', 'Parcelamento sem saldo', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3">
                                        <input class="form-check-input" id="switch-all-payment-type" type="checkbox"
                                            {{ (old('payment_method_boleto', $plan->payment_method_boleto) &&
                                                old('payment_method_credit_card', $plan->payment_method_credit_card) &&
                                                old('payment_method_pix', $plan->payment_method_pix) &&
                                                old('payment_method_multiple_cards', $plan->payment_method_multiple_cards) &&
                                                old('unlimited_sale', $plan->unlimited_sale))
                                                ? 'checked' : ''
                                            }}
                                        >
                                        <label class="form-check-label" for="switch-all-payment-type">Todos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="div-promotional-price" style="display:none">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="form-check form-switch mt-4">
                                {!! Form::checkbox('use_promotional_price', true, null, ['id' => 'use_promotional_price', 'class' => 'form-check-input']) !!}
                                {!! Form::label('use_promotional_price', 'Utilizar valor promocional', ['class' => 'form-check-label']) !!}
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row promotional_price d-none">
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        {!! Form::text('promotional_price', null, ['id' => 'promotional_price', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-not-empty mui--is-untouched mui--is-pristine', 'maxlength' => 10]) !!}
                                        {!! Form::label('promotional_price', 'Valor promocional') !!}
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        {!! Form::number('promotional_periods', null, ['id' => 'promotional_periods', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                                        {!! Form::label('promotional_periods', 'Períodos promocionais') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12">
                    <h6>Imagem do Produto</h6>
                    <p class="xgrow-medium-italic">É a imagem localizada na parte superior que identifica o produto</p>
                    <p class="xgrow-medium-italic">Tamanho 500 x 500</p>
                    {!! UpImage::getImageTag( (isset($plan->id) ? $plan : new \App\Plan()), 'image', 'image', 'img-fluid my-3') !!}
                    <br>
                    {!! UpImage::getUploadButton('image', 'btn btn-themecolor') !!}
                    <button type="button" class="btn xgrow-upload-btn-lg my-2" id="removeImage">
                        <i class="fa fa-trash" aria-hidden="true"></i> Remover imagem
                    </button>
                </div>
            </div>

            <input type="hidden" id="freedays_type" name="freedays_type" value="trial">

            <?php
            /*
            <hr class="mt-5" style="border-color: var(--border-color)"/>
            <div class="row">
                <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">Período de teste</h5>
                <div class="col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-lg-7 col-md-12">
                            <div class="row">
                                <div class="col-lg-8 col-md-12">
                                    <div
                                        class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        <select class="xgrow-select" name="freedays_type" id="freedays_type">
                                            <option value="" selected disabled></option>
                                            @foreach ($freedays_type as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ (isset($plan->freedays_type) && $plan->freedays_type == $key) || old('freedays_type') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for='freedays_type'>Tipo de teste</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        {!! Form::number('freedays', null, ['id' => 'freedays', 'min' => 1, 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                                        {!! Form::label('freedays', 'Dias de teste') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12"></div>
                    </div>
                </div>
            </div>
            */
            ?>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">
            <input class="xgrow-button" type="submit" value="Salvar">
        </div>
    </div>
</div>
