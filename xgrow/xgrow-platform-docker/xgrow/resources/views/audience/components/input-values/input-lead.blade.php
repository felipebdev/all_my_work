<select class="xgrow-select value-condition" name="value-condition" required>
    <option
        value=""
        disabled=""
        hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >
        Selecione um tipo
    </option>

    <option
        value="{{\App\Subscriber::STATUS_LEAD}}" {{($current_value ?? '') == \App\Subscriber::STATUS_LEAD ? 'selected' : ''}}>
        Lead
    </option>
</select>
