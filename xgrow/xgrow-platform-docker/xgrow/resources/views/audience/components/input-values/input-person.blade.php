<select class="xgrow-select value-condition" name="value-condition" required>
    <option
        value=""
        disabled=""
        hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >
        Escolha Física ou Jurídica
    </option>

    <option value="CPF" {{($current_value ?? '') == 'CPF' ? 'selected' : ''}}>Física</option>
    <option value="CNPJ" {{($current_value ?? '') == 'CNPJ' ? 'selected' : ''}}>Jurídica</option>
</select>
