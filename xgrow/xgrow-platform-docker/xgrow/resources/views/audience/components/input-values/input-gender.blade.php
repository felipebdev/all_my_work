<select class="xgrow-select value-condition" name="value-condition" required>
    <option
        value=""
        disabled=""
        hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >
        Selecione um gênero
    </option>

    @foreach ( \App\Subscriber::allGenders() as $code => $description)
        <option
            value="{{ $code }}"
            {{($current_value ?? '') == $code ? 'selected' : ''}}
        >{{ $description }}</option>
    @endforeach
</select>
