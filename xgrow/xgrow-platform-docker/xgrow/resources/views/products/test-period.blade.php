@if(false)
    <div id="div-test-period" class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-check form-switch">
                {!! Form::checkbox('chk-freedays', null, isset($plan->freedays), ['id' => 'chk-freedays', 'class' => 'form-check-input']) !!}
                {!! Form::label('chk-freedays', 'Período de teste', ['class' => 'form-check-label']) !!}
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 mt-4" id="div-freedays">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                        {!! Form::select('freedays_type', ['trial' => 'Experiência', 'free' => 'Grátis'], $plan->freedays_type, ['class' => 'xgrow-select']) !!}
                        {!! Form::label('freedays_type', 'Tipo de teste:') !!}
                    </div>
                    <ul class="px-0 xgrow-medium-italic">
                        <li class="mb-2">
                            <span style="color: var(--contrast-green3)">Experiência</span> - Após o primeiro ciclo de
                            assinatura, o aluno será cobrado
                            na data de início da experiência.
                        </li>
                        <li class="my-2">
                            <span style="color: var(--contrast-green3)">Grátis</span> - a cobrança da recorrência é
                            feita sempre na data de
                            finalização do período grátis.
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::number('freedays', $plan->freedays, ['id' => 'freedays', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'min'=> 1 , 'max' => 5]) !!}
                            {!! Form::label('freedays', 'Períodos promocionais') !!}
                        </div>
                        <span class="px-0 xgrow-medium-italic">Máx 5 dias.</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mt-4">
    </div>
@endif
