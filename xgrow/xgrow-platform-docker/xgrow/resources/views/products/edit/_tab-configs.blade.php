@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/i18n/pt-BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
    <script>
        const hasSuppport = @json($hasSuppport);
        const hasCheckout = @json($hasCheckout);

        function chkTrigger(chkId, divId) {
            const el = $('#' + divId);
            $('#' + chkId).is(':checked') ? el.removeClass('d-none').fadeIn() : el.addClass('d-none').fadeOut();
        }

        $('#chk-has-support, #chk-has-checkout').change(function() {
            chkTrigger('chk-has-support', 'hasSupport');
            chkTrigger('chk-has-checkout', 'hasCheckout');
            $('#chk-has-checkout').is(':checked') ? $('#checkout_layout').val('step') : '';
        });

        function switchStatus() {
            if (hasSuppport) $('#chk-has-checkout').prop('checked', true);
            if (hasCheckout) $('#chk-has-support').prop('checked', true);
            chkTrigger('chk-has-support', 'hasSupport');
            chkTrigger('chk-has-checkout', 'hasCheckout');
        }

        switchStatus();

        $('#keywords').select2({
            tags: true,
            tokenSeparators: [';'],
            placeholder: 'Palavras-chave (Opcional)',
            language: 'pt-BR',
            minimumInputLength: 3
        });

        $(function() {
            const SPMaskBehavior = function(val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $('.ipt-phone').mask(SPMaskBehavior, spOptions);
            $('.ipt-country-code').mask('+9000');

            $('#slc-checkout-support-platform').on('change', function() {
                setCheckoutPlatform(this.value);
            });
        });

        function setCheckoutPlatform(checkoutSupportPlatform, checkoutSupport = null) {
            if (checkoutSupportPlatform === 'jivochat') {
                $('#lbl-checkout-support').html('ID do Jivochat').removeClass('d-none');
                $('#divCheckoutSupport').removeClass('d-none');
                $('#checkout_support').val('');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
                clearFields();
            } else if (checkoutSupportPlatform === 'octadesk') {
                $('#lbl-checkout-support').html('ID do Octadesk').removeClass('d-none');
                $('#divCheckoutSupport').removeClass('d-none');
                $('#checkout_support').val('');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').removeClass('d-none');
            } else if (checkoutSupportPlatform === 'mandeumzap') {
                $('#lbl-checkout-support').html('Link do Mande Um Zap').removeClass('d-none');
                $('#divCheckoutSupport').removeClass('d-none');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
            } else if (checkoutSupportPlatform === 'whatsapplink') {
                $('#lbl-checkout-support').html('Link do Whatsapp').removeClass('d-none');
                $('#divCheckoutSupport').removeClass('d-none');
                $('#div-checkout-whatsapp').removeClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
                splitWhatsappLink(checkoutSupport);
            } else if (checkoutSupportPlatform === '') {
                clearFields();
                $('#divCheckoutSupport').addClass('d-none');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
            }
        }

        function clearFields() {
            $('#checkout_support').val('');
            $('#ipt-checkout-whatsapp-country-code').val('');
            $('#ipt-checkout-whatsapp-phone').val('');
            $('#ipt-checkout-whatsapp-message').val('');
        }

        function splitWhatsappLink(link) {
            if (!link) return;
            const url = new URL(link);
            const params = new URLSearchParams(url.search);
            const [fullPhone = '', message = ''] = [...params.values()];
            const [countryCode = '', phone = ''] = (fullPhone.includes('-')) ? fullPhone.split('-'): ['', fullPhone];
            $('#ipt-checkout-whatsapp-country-code').val(countryCode).addClass('mui--is-not-empty');
            $('#ipt-checkout-whatsapp-phone').val(phone).addClass('mui--is-not-empty');
            $('#ipt-checkout-whatsapp-message').val(message).addClass('mui--is-not-empty');
        }

        if (Boolean(@json($product->checkout_support_platform))) {
            setCheckoutPlatform(@json($product->checkout_support_platform), @json($product->checkout_support));
        }

        function generateWhatsappLink() {
            let countryCode = $('#ipt-checkout-whatsapp-country-code').val();
            let phone = $('#ipt-checkout-whatsapp-phone').val();
            let message = $('#ipt-checkout-whatsapp-message').val();
            if (!countryCode) {
                $('#ipt-checkout-whatsapp-country-code').focus();
                return;
            }

            if (!phone) {
                $('#ipt-checkout-whatsapp-phone').focus();
                return;
            }

            if (!message) {
                $('#ipt-checkout-whatsapp-message').focus();
                return;
            }

            phone = phone.replace(/\D/g, '');
            message = encodeURIComponent(message);
            const link = `https://api.whatsapp.com/send?phone=${countryCode}-${phone}&text=${message}`;
            $('#checkout_support').val(link);
        }


        function splitWhatsappLink(link) {
            if (!link) return;
            const url = new URL(link);
            const params = new URLSearchParams(url.search);
            const [fullPhone = '', message = ''] = [...params.values()];
            const [countryCode = '', phone = ''] = (fullPhone.includes('-')) ? fullPhone.split('-'): ['', fullPhone];
            $('#ipt-checkout-whatsapp-country-code').val(countryCode).addClass('mui--is-not-empty');
            $('#ipt-checkout-whatsapp-phone').val(phone).addClass('mui--is-not-empty');
            $('#ipt-checkout-whatsapp-message').val(message).addClass('mui--is-not-empty');
        }
    </script>
@endpush

<div class="tab-pane fade navConfigs" id="nav-configs" role="tabpanel" aria-labelledby="nav-configs-tab">
    {!! Form::model($product, [
        'route' => ['products.page.config', $product->id],
        'enctype' => 'multipart/form-data',
    ]) !!}
    <p class="xgrow-card-title mb-2">Página do Produto</p>

    <div class="row mt-4">

        <div class="col-sm-12 col-md-12 col-lg-8">
            <div class="row">

                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('name', null, [
                            'id' => 'name',
                            'autocomplete' => 'off',
                            'spellcheck' => 'false',
                            'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
                            'required',
                        ]) !!}
                        {!! Form::label('name', 'Nome') !!}
                    </div>
                </div>

                {!! Form::hidden('type', null, ['id' => 'type']) !!}

                <div id="div_category" class="col-sm-12 col-md-6 col-lg-6">
                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                        {!! Form::select('category_id', $categories, null, ['name' => 'category_id', 'class' => 'xgrow-select']) !!}
                        {!! Form::label('category_id', 'Categoria:') !!}
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                        {!! Form::textarea('description', null, [
                            'id' => 'description',
                            'autocomplete' => 'off',
                            'spellcheck' => 'false',
                            'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
                            'rows' => 7,
                            'cols' => 54,
                            'style' => 'resize:none',
                            'maxlength' => '250',
                        ]) !!}
                        {!! Form::label('description', 'Descreva aqui a descrição do produto principal...') !!}
                    </div>
                    <p class="xgrow-medium-italic ms-2 my-2 mb-3">Utilizar de 0 - 250 caracteres.</p>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="xgrow-form-control">
                        <select name="keywords[]" id="keywords" class="xgrow-select" multiple="multiple"
                            style="width: 100%">
                            @if ($keywordToSelect)
                                @foreach ($keywordToSelect as $kw)
                                    @if ($kw !== '')
                                        <option value="{{ $kw }}" selected="selected">{{ $kw }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <small>Adicione a tag e pressione enter para confirmar.</small>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('support_email', null, [
                            'id' => 'support_email',
                            'autocomplete' => 'off',
                            'spellcheck' => 'false',
                            'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
                            'required',
                        ]) !!}
                        {!! Form::label('support_email', 'Contato para suporte (obrigatório)') !!}
                    </div>
                </div>

                <div class="col-sm-12 mt-4">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('chk-has-support', null, null, ['id' => 'chk-has-support', 'class' => 'form-check-input']) !!}
                        {!! Form::label('chk-has-support', 'Integrar chat de suporte', ['class' => 'form-check-label']) !!}
                    </div>
                    <div class="row d-none mt-3" id="hasSupport">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                {!! Form::select('checkout_support_platform', $platforms_support, null, [
                                    'id' => 'slc-checkout-support-platform',
                                    'name' => 'checkout_support_platform',
                                    'class' => 'xgrow-select',
                                ]) !!}
                                {!! Form::label('checkout_support_platform', 'Plataforma de suporte:') !!}
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div id="div-checkout-whatsapp" class="row d-none">
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        <input id="ipt-checkout-whatsapp-country-code"
                                            class="mui--is-empty mui--is-untouched mui--is-pristine ipt-country-code"
                                            type="text" maxlength="5" onblur="generateWhatsappLink()">
                                        <label for="ipt-checkout-whatsapp-country-code">Código do país</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        <input id="ipt-checkout-whatsapp-phone"
                                            class="mui--is-empty mui--is-untouched mui--is-pristine ipt-phone"
                                            type="text" onblur="generateWhatsappLink()">
                                        <label for="ipt-checkout-whatsapp-phone">Celular</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        <input id="ipt-checkout-whatsapp-message"
                                            class="mui--is-empty mui--is-untouched mui--is-pristine" type="text"
                                            maxlength="100" onblur="generateWhatsappLink()">
                                        <label for="ipt-checkout-whatsapp-message">Mensagem</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 d-none" id="divCheckoutSupport">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    {!! Form::text('checkout_support', null, [
                                        'id' => 'checkout_support',
                                        'autocomplete' => 'off',
                                        'spellcheck' => 'false',
                                        'class' => 'mui--is-not-empty mui--is-untouched mui--is-pristine',
                                    ]) !!}
                                    {!! Form::label('checkout_support', 'Link para suporte', ['id' => 'lbl-checkout-support']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 d-none" id="div-checkout-octadesk">
                            <p class="xgrow-medium-italic my-2">O ID pode ser copiado conforme a imagem abaixo. Obs:
                                Copiar sem as aspas.</p>
                            <p class="xgrow-medium-italic my-2">Caso não encontre o ID, basta ir em Configurações > Chat
                                > Aparência e instalação e acessar o Item 3.</p>
                            <img src="{{ asset('images/octadesk.jpg') }}" alt="octadesk" class="img-fluid"
                                style="max-width: 100% !important;">
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 mt-4">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('chk-has-checkout', null, null, ['id' => 'chk-has-checkout', 'class' => 'form-check-input']) !!}
                        {!! Form::label('chk-has-checkout', 'Checkout', ['class' => 'form-check-label']) !!}
                    </div>
                    <div class="row d-none" id="hasCheckout">
                        <div class="col-sm-12 col-md-6 col-lg-6 mt-3">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                {!! Form::select('checkout_layout', $layout_checkout, null, [
                                    'id' => 'checkout_layout',
                                    'name' => 'checkout_layout',
                                    'class' => 'xgrow-select',
                                ]) !!}
                                {!! Form::label('checkout_layout', 'Layout do checkout:') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 mt-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="form-check form-switch">
                                    {!! Form::checkbox('checkout_address', null, $product->checkout_address, [
                                        'id' => 'checkout_address',
                                        'class' => 'form-check-input',
                                    ]) !!}
                                    {!! Form::label('checkout_address', 'Solicitar preenchimento de endereço', ['class' => 'form-check-label']) !!}
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="form-check form-switch">
                                    {!! Form::checkbox('double_email', null, $product->double_email, [
                                        'id' => 'chk-double-email',
                                        'class' => 'form-check-input',
                                    ]) !!}
                                    {!! Form::label('chk-double-email', 'Solicitar preenchimento duplo de endereço de e-mail', [
                                        'class' => 'form-check-label',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-sm-12 col-md-12 col-lg-4">
            <h6>Imagem do Produto</h6>
            <p class="xgrow-medium-italic">
                Imagem localizada na parte superior que identifica o produto. Tamanho 500x500.
            </p>
            <div class="w-50">
                {!! UpImage::getImageTag($product, 'image', 'image', 'my-3 w-100') !!}
            </div>
            <br>
            {!! UpImage::getUploadButton('image', 'btn btn-themecolor') !!}
            <button type="button" class="btn xgrow-upload-btn-lg my-2" id="removeImage">
                <i class="fa fa-trash" aria-hidden="true"></i> Remover imagem
            </button>
        </div>
    </div>


    <div class="xgrow-card-footer p-3 border-top mt-4 justify-content-end">
        <button class="xgrow-button" type="submit">
            Atualizar
        </button>
    </div>

    @include('up_image.modal-xgrow', ['restrictAcceptedFormats' => 'image/*'])
    {!! Form::close() !!}
</div>
