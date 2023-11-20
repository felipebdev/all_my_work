<div class="row">
    <div class="form-group">
        <div class="col-md-12 d-flex align-items-center">
            <label for="status" class="mr-3">Status</label>
            <div class="ckbx-style-8">
                <input type="checkbox" id="ckbx-style-1-1" name="status" value="1" @if (isset($plan->status))
                {{$plan->status == 1 ? 'checked' : ''}}
                @else
                checked
                    @endif
                >
                <label for="ckbx-style-1-1"></label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $plan->name ?? '' }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="recurrence">Periodicidade</label>
            <select class="custom-select" id="recurrence" name="recurrence">
                @foreach ($recurrence as $key => $value)
                    <option value="{{$key}}" {{isset($plan->recurrence) && $plan->recurrence == $key ? 'selected' : ''}}>
                        {{$value}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="charge_until">Forma de cobrança</label>
            <input type="number" min="0" step="any" class="form-control" id="charge_until" name="charge_until"
                   value="{{$plan->charge_until ?? 0}}" required>
            <small>Deixe '0' caso queira cobrar até cancelar</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="currency">Moeda</label>
            <select class="custom-select" id="currency" name="currency">
                @foreach ($currency as $key => $value)
                    <option value="{{$key}}" {{isset($plan->currency) && $plan->currency == $key ? 'selected' : ''}}>
                        {{$value}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="price">Valor</label>
            <input type="text" min="0" step="any" class="form-control" id="price" name="price" value="{{ (isset($plan->price)) ?  number_format($plan->price, 2, ',', '.') : '' }}" onkeypress="$(this).mask('#.##0,00', {reverse: true})" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="setup_price">Setup</label>
            <input type="text" min="0" step="any" onkeypress="$(this).mask('#.##0,00', {reverse: true})" class="form-control" id="setup_price" name="setup_price"
                   value="{{$plan->setup_price ?? ''}}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="freedays_type">Tipo de teste</label>
            <select class="custom-select" id="freedays_type" name="freedays_type">
                @foreach ($freedays_type as $key => $value)
                    <option value="{{$key}}" {{isset($plan->freedays_type) && $plan->freedays_type == $key ? 'selected' : ''}}>{{$value}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="freedays">Dias de teste</label>
            <input type="number" min="1" step="any" class="form-control" id="freedays" name="freedays"
                   value="{{$plan->freedays ?? 7}}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="freedays_type">Gateway</label>
            <select class="custom-select" id="integration_id" name="integration_id" {{ isset($plan->integration->integration_id) ? "disabled='disabled'" : '' }}>
                <option>Selecione um gateway</option>
                @foreach ($gateways as $gateway)
                    <option value="{{$gateway->id}}" {{isset($plan->integration->integration_id) && $plan->integration->integration_id == $gateway->id ? 'selected' : '' }}>{{$gateway->name_integration}} {{ $gateway->id }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="url_checkout">Link do checkout</label>
            <div class="input-group">
                <input type="text" class="form-control" id="url_checkout" name="url_checkout" value="{{ isset($urlCheckout) ? $urlCheckout : '' }}" readonly>
                <div class="input-group-append" onclick="copyUrlCheckout()">
                    <span style="background-color: #FFF;cursor: pointer;" class="input-group-text fa fa-copy"></span>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6">
        {{ old('type_plan') }}
        <div class="form-group">
            <div class="form-check form-check-inline" >
                <input class="form-check-input" type="radio" name="type_plan" id="radioRecurrency" value="R" @if($plan->type_plan === 'R') checked="checked" @endif>
                <label class="form-check-label" for="radioRecurrency">Recorrência</label>
            </div>
            <div class="form-check form-check-inline" >
                <input class="form-check-input" type="radio" name="type_plan" id="radioParceled" value="P" @if($plan->type_plan === 'P') checked="checked" @endif>
                <label class="form-check-label" for="radioParceled">Compra parcelada</label>
            </div>

            <div class="form-check form-check-inline @if($plan->type_plan !== 'P') d-none @endif" id="div_installment">
                <input class="form-control" type="integer" name="installment" id="installment" placeholder="Quantidade de parcelas" value="@if($plan->installment > 0) {{$plan->installment}} @endif">
            </div>
        </div>

    </div>
</div>

<input type="hidden" id="platform_id" name="platform_id" value="{{Auth::user()->platform_id}}">
