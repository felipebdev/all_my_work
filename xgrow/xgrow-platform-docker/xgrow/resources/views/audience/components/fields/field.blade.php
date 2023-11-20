<select class="xgrow-select field-condition" name="field-condition" required>
    <option
        value=""
        disabled=""
        hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >
        Selecione um campo
    </option>

    @foreach ($options as $option)
        <option
            value="{{$option->value}}"
            {{(($current_value ?? null) == $option->value) ? 'selected' : '' }}
        >{{$option->text ?? ''}}</option>
    @endforeach
</select>
