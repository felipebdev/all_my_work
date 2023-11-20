<select class="xgrow-select value-condition" name="value-condition" required>
    <option
        value=""
        disabled=""
        hidden=""
        {{($current_value ?? '') == '' ? 'selected' : ''}}
    >
        Selecione um produto
    </option>

    @foreach ( \App\Product::where('status', 1)->where('platform_id', Auth::user()->platform_id)->get() as $product)
        <option
            value="{{ $product->id }}"
            {{($current_value ?? '') == $product->id ? 'selected' : ''}}
        >{{ $product->name }}</option>
    @endforeach
</select>
