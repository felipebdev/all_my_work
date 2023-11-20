<div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
    {!! Form::text('value2', $value ?? '', [
        'class' => 'custom-datepicker xgrow-datepicker value-condition',
        'data-provide' => 'datepicker',
        'data-date-format' => "dd/mm/yyyy",
        'autocomplete' => 'off',
    ]) !!}
    {!! Form::label('value2', 'Especifique uma data') !!}
</div>
