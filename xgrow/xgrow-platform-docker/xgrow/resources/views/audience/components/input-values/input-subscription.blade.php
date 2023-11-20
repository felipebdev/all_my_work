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
    <option value="{{\App\Payment::STATUS_FAILED}}" {{($current_value ?? '') == \App\Payment::STATUS_FAILED ? 'selected' : ''}}>Transações canceladas</option>

    <option value="{{\App\Subscriber::STATUS_ACTIVE}}" {{($current_value ?? '') == \App\Subscriber::STATUS_ACTIVE ? 'selected' : ''}}>Ativo</option>
    <option value="{{\App\Subscriber::STATUS_CANCELED}}" {{($current_value ?? '') == \App\Subscriber::STATUS_CANCELED ? 'selected' : ''}}>Inativo</option>
</select>

