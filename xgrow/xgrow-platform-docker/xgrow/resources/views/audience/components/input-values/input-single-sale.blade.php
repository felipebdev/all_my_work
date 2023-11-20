<select class="xgrow-select value-condition" name="value-condition" required>
    <option
        value=""
        disabled=""
        hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >
        Selecione um status
    </option>

    <option value="{{\App\Payment::STATUS_PAID}}" {{($current_value ?? '') == \App\Payment::STATUS_PAID ? 'selected' : ''}}>Transações pagas</option>
    <option value="{{\App\Payment::STATUS_PENDING}}" {{($current_value ?? '') == \App\Payment::STATUS_PENDING ? 'selected' : ''}}>Transações pendentes</option>
    <option value="{{\App\Payment::STATUS_CANCELED}}" {{($current_value ?? '') == \App\Payment::STATUS_CANCELED ? 'selected' : ''}}>Transações canceladas</option>
</select>
