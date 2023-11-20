<div x-data="upsell()">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex align-items-center">
                <div class="form-check form-switch">
                    {!! Form::checkbox('chk-greeting-exists', null, null, ['id' => 'chk-greeting-exists', 'class' => 'form-check-input', '@change' => 'checkGretting()']) !!}
                    {!! Form::label('chk-greeting-exists', 'Página de agradecimento', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        </div>
    </div>

    <div id="divGretting" class="row mt-4">
        <div class="col-sm-12 col-md-12 col-lg-12 mt-2">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="xgrow-form-control">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                             style="margin-top:-3px;">
                            {!! Form::text('url_checkout_confirm', null, ['id' => 'url_checkout_confirm', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                            {!! Form::label('url_checkout_confirm', 'URL de confirmação (checkout)') !!}
                        </div>
                    </div>
                    <p class="xgrow-medium-italic" style="margin-top:-15px">
                        Caso preenchido redireciona o usuário para a url ao finalizar com sucesso o checkout
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
