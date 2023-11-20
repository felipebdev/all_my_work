<div id="div-subscription" class="row">

    <hr class="my-4">

    <div class="col-lg-6 col-md-6 col-sm-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="form-check form-switch">
                    {!! Form::checkbox('charge_until', null, $plan->charge_until, ['id' => 'chk-charge-until', 'class' => 'form-check-input', 'value' => '0']) !!}
                    {!! Form::label('chk-charge-until', 'Cobrança ilimitada', ['class' => 'form-check-label']) !!}
                </div>
                <small class="xgrow-medium-light-italic">Cobrar até o cancelamento</small>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 mt-4" id="div-recurrence">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12" id="divChargeUntil">

                        @if(isset($plan->charge_until) && $plan->charge_until>0)
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::number('charge_until', $plan->charge_until, ['id' => 'charge_until', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                            {!! Form::label('charge_until', 'Limite de cobranças') !!}
                        </div>
                        @else
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::number('charge_until', 0, ['id' => 'charge_until', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                                {!! Form::label('charge_until', 'Limite de cobranças') !!}
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div
                            class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            @if (app()->environment('local', 'testing', 'develop'))
                            {!! Form::select('recurrence', ['1' => 'Diária', '7' => 'Semanal', '30' => 'Mensal', '60' => 'Bimestral', '90' => 'Trimestral', '180' => 'Semestral', 360 => 'Anual'], $plan->recurrence, ['id' => 'recurrence', 'class' => 'xgrow-select slc-recurrence']) !!}
                            @else
                            {!! Form::select('recurrence', ['7' => 'Semanal', '30' => 'Mensal', '60' => 'Bimestral', '90' => 'Trimestral', '180' => 'Semestral', 360 => 'Anual'], $plan->recurrence, ['id' => 'recurrence', 'class' => 'xgrow-select slc-recurrence']) !!}
                            @endif
                            {!! Form::label('recurrence', 'Periodicidade:') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12" style="height: 57px">
                <div class="form-check form-switch">
                    {!! Form::checkbox('use_promotional_price', true, $plan->use_promotional_price, ['id' => 'use_promotional_price', 'class' => 'form-check-input']) !!}
                    {!! Form::label('use_promotional_price', 'Utilizar valor diferenciado', ['class' => 'form-check-label']) !!}
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12" id="div-promotional_price">
                <div class="row promotional_price">
                    <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::text('promotional_price', $plan->promotional_price, ['id' => 'promotional_price', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-not-empty mui--is-untouched mui--is-pristine', 'maxlength' => 10]) !!}
                            {!! Form::label('promotional_price', 'Valor diferenciado') !!}
                        </div>
                        <p class="xgrow-medium-italic d-none" id="lbl_promocional_price" style="margin-top:-15px">
                            O valor mínimo do produto é de R$4,00.
                        </p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::number('promotional_periods', $plan->promotional_periods, ['id' => 'promotional_periods', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                            {!! Form::label('promotional_periods', 'Períodos diferenciados') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4">
</div>
