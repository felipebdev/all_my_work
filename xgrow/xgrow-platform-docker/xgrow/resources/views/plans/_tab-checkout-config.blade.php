<div class="tab-pane fade show" id="nav-checkout-config" role="tabpanel" aria-labelledby="nav-checkout-config-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Configuração checkout
            </h5>
            <div class="row">
                <p class="xgrow-medium mb-3">Integração com widget de suporte</p>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::select('checkout_support_platform', ['jivochat' => 'JivoChat', 'octadesk' => 'Octadesk', 'mandeumzap' => 'Mande um Zap', 'whatsapplink' => 'Gerador de link para WhatsApp'], null, ['id' => 'slc-checkout-support-platform', 'class' => 'xgrow-select slc-recurrence', 'placeholder' => '']) !!}
                        {!! Form::label('checkout_support_platform', 'Plataforma de suporte') !!}
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div id="div-checkout-whatsapp" class="row d-none">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-checkout-whatsapp-country-code"
                                    class="mui--is-empty mui--is-untouched mui--is-pristine ipt-country-code" type="text" maxlength="5"
                                    onblur="generateWhatsappLink()">
                                <label for="ipt-checkout-whatsapp-country-code">Código do país</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-checkout-whatsapp-phone"
                                    class="mui--is-empty mui--is-untouched mui--is-pristine ipt-phone" type="text"
                                    onblur="generateWhatsappLink()">
                                <label for="ipt-checkout-whatsapp-phone">Celular</label>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-checkout-whatsapp-message"
                                    class="mui--is-empty mui--is-untouched mui--is-pristine" type="text" maxlength="100"
                                    onblur="generateWhatsappLink()">
                                <label for="ipt-checkout-whatsapp-message">Mensagem</label>
                            </div>
                        </div>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('checkout_support', null, ['id' => 'checkout_support', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                        {!! Form::label('checkout_support', 'Link para suporte', ['id' => 'lbl-checkout-support']) !!}
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 d-none" id="div-checkout-octadesk">
                    <p class="xgrow-medium-italic my-2">O ID pode ser copiado conforme a imagem abaixo. Obs: Copiar sem as aspas.</p>
                    <p class="xgrow-medium-italic my-2">Caso não encontre o ID, basta ir em Configurações > Chat > Aparência e instalação e acessar o Item 3.</p>
                    <img src="{{asset('images/octadesk.jpg')}}" alt="octadesk" class="img-fluid" style="max-width: 100% !important;">
                </div>
            </div>
            <div class="row mt-4">
                <p class="xgrow-medium mb-3">Suporte</p>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::email('checkout_email', null, ['id' => 'checkout_email', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                        {!! Form::label('checkout_email', 'Email') !!}
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <!-- <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('checkout_whatsapp', null, ['id' => 'checkout_whatsapp', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine ipt-phone']) !!}
                        {!! Form::label('checkout_whatsapp', 'Whatsapp') !!}
                    </div> -->
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('checkout_facebook_pixel', null, ['id' => 'checkout_facebook_pixel', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                        {!! Form::label('checkout_facebook_pixel', 'Facebook pixel') !!}
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('checkout_google_tag', null, ['id' => 'checkout_google_tag', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                        {!! Form::label('checkout_google_tag', 'Google tag manager') !!}
                    </div>
                </div>
            </div> -->
            <div class="row mt-4">
                <p class="xgrow-medium mb-3">Layout do checkout</p>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    {{-- <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::select('checkout_layout', ['step' => '3 passos', 'page' => 'Página única'], null, ['class' => 'xgrow-select slc-recurrence']) !!}
                        {!! Form::label('checkout_layout', 'Layout') !!}
                    </div> --}}
                    <div class="row">
                        <div class="p-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-radio">
                                <div class="my-2 d-flex align-items-center">
                                    {{ Form::radio('checkout_layout', 'step', null, ['id' => 'step', 'checked' => 'checked']) }}
                                    <label for="step" class="mx-2">3 passos</label>
                                </div>
                                {{-- <p class="mb-3">step</p> --}}
                                <img src="/xgrow-vendor/assets/img/step.svg" width="200" height="126" alt="step" />
                            </div>
                        </div>
                        <div class="p-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-radio">
                                <div class="my-2 d-flex align-items-center">
                                    {{ Form::radio('checkout_layout', 'page', null, ['id' => 'page']) }}
                                    <label for="page" class="mx-2">Página unica</label>
                                </div>
                                {{-- <p class="mb-3">page</p> --}}
                                <img src="/xgrow-vendor/assets/img/page.svg" width="200" height="126" alt="page" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('checkout_address', true, old('checkout_address', $plan->checkout_address ?? true), ['id' => 'checkout_address', 'class' => 'form-check-input']) !!}
                        {!! Form::label('checkout_address', 'Deseja solicitar o preenchimento do endereço no Checkout?', ['class' => 'form-check-label']) !!}
                    </div>
                    <p class="xgrow-medium-italic mt-2">Caso não habilitado, esses dados serão solicitados no primeiro
                        acesso a LA da XGROW para geração da nota fiscal. Se não for entregar pela XGROW, sugerimos
                        manter habilitado.</p>
                </div>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">
            <input class="xgrow-button" type="submit" value="Salvar">
        </div>
    </div>
</div>
