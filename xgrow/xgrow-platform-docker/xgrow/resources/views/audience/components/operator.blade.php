<select class="xgrow-select operator-condition" name="operator-condition" required>
    <option value="" disabled="" hidden="" {{($operator ?? 0) == 0 ? 'selected' : ''}}>
        Selecione uma condição
    </option>
    <option value="1" {{($operator ?? 0) == 1 ? 'selected' : ''}}>igual a</option>
    <option value="2" {{($operator ?? 0) == 2 ? 'selected' : ''}}>diferente de</option>
    <option value="3" {{($operator ?? 0) == 3 ? 'selected' : ''}}>maior que</option>
    <option value="4" {{($operator ?? 0) == 4 ? 'selected' : ''}}>maior ou igual a</option>
    <option value="5" {{($operator ?? 0) == 5 ? 'selected' : ''}}>menor que</option>
    <option value="6" {{($operator ?? 0) == 6 ? 'selected' : ''}}>maior ou igual a</option>
    <option value="7" {{($operator ?? 0) == 7 ? 'selected' : ''}}>nao preenchido</option>
</select>
