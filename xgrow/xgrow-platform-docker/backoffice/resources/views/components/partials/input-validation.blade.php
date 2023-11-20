<div class="validation-container client-validation">

    @if(@isset($validation) && !@empty($validation))
        <ul class="validation-rules-list">
            @foreach($validation as $rule)

                <li data-rule="{{ $rule['rule'] }}" class="validation-message client-validation-message">
                <i class="fas fa-times"></i> {{ vsprintf($rule['message'], $rule['params']) }}
                </li>

            @endforeach
        </ul>
    @endif

</div>

<div class="validation-container server-validation">

    @if(@isset($serverValidation) && !@empty($serverValidation))
        <ul class="validation-rules-list">
            @foreach($serverValidation as $rule)

                <li data-rule="{{ $rule['rule'] }}" class="validation-message server-validation-message">
                <i class="fas fa-times"></i> {{ vsprintf($rule['message'], $rule['params']) }}
                </li>

            @endforeach
        </ul>
    @endif

</div>
