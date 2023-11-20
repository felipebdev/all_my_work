<div class="tab-pane fade show" id="nav-greeting" role="tabpanel" aria-labelledby="nav-greeting-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Mensagem de agradecimento
            </h5>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('url_checkout_confirm', null, ['id' => 'url_checkout_confirm', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'pattern' => 'https://.*']) !!}
                        {!! Form::label('url_checkout_confirm', 'URL de confirmação (checkout)') !!}
                    </div>
                </div>
                <p class="xgrow-medium-italic" style="margin-top:-15px">
                    Caso preenchido redireciona o usuário para a url ao finalizar com sucesso o checkout
                </p>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::textarea('message_success_checkout', null, ['id' => 'message_success_checkout', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none']) !!}
                        {!! Form::label('message_success_checkout', 'Descreva detalhadamente aqui a sua mensagem...') !!}
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check form-switch">
                            {!! Form::checkbox(null, null, isset($plan->checkout_url_terms) || old('checkout_url_terms') !== null, ['id' => 'chk-terms-exists', 'class' => 'form-check-input']) !!}
                            {!! Form::label(null, 'Adicionar um termo personalizado', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                    <div id="div-terms"
                         class="{{ !isset($plan->checkout_url_terms) && old('terms') === null ? 'd-none' : '' }}">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::text('checkout_url_terms', null, ['id' => 'checkout_url_terms', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'pattern' => 'www.*']) !!}
                            {!! Form::label('checkout_url_terms', 'Link dos termos de uso') !!}
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">
            <input class="xgrow-button" type="submit" value="Salvar">
        </div>
    </div>
</div>
