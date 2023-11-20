<select class="xgrow-select value-condition" name="value-condition" required>
    <option value="" disabled="" hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >Selecione um método de pagamento</option>

    @foreach ( \App\Payment::listTypePayments() as $code => $description)
        <option value="{{$code}}" {{($current_value ?? '') == $code ? 'selected' : ''}}>{{$description}}</option>
    @endforeach
</select>
