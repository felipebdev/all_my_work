{{-- <div class="xgrow-check d-flex align-items-center mb-3">
	<input type="checkbox" name="active_sales" id="active_sales" class="me-2"
		{{$config[0]['active_sales'] == 1 ? 'checked' : ''}} value="t" />
	<label class="xgrow-medium-bold" for="active_sales">Ativar vendas</label>
</div>
<p class="xgrow-medium-regular ">
	Taxa: 5% + R$ 1,00 por transação
</p>
<p class="xgrow-medium-regular mb-3">
	Dias de recebimento: 30 dias
</p>
<p class="xgrow-large-bold mb-3">
	Dados bancários para recebimento
</p> --}}
<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label w-100 me-4">
            <input id="bank" name="bank" autocomplete="off" type="text" spellcheck="false" value="{{ $client->bank }}"
                maxlength="10" />
            <label for="bank">Banco</label>
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label w-100">
            <input id="branch" name="branch" autocomplete="off" type="text" spellcheck="false"
                value="{{ $client->branch }}" maxlength="10" />
            <label for="branch">Agência</label>
        </div>
    </div>

    {{-- <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input id="account" name="account" autocomplete="off" type="text" spellcheck="false"
				value="{{ $client->account }}" />
			<label for="account">Conta</label>
			<span onclick="document.getElementById('account').value = ''"></span>
		</div> --}}
</div>

<div class="col-md-12">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
        <input id="account" name="account" autocomplete="off" type="text" spellcheck="false"
            value="{{ $client->account }}" />
        <label for="account">Conta</label>
    </div>
    {{-- <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input id="pixel_id" name="pixel_id" autocomplete="off" type="text" spellcheck="false"
				value="{{ $config[0]['pixel_id'] }}" />
			<label>Facebook Pixel</label>
			<span onclick="document.getElementById('pixel_id').value = ''"></span>
		</div>

		<div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input id="google_tag_id" name="google_tag_id" autocomplete="off" type="text" spellcheck="false"
				value="{{ $config[0]['google_tag_id'] }}" />
			<label>Google Tag Manager ID</label>
			<span onclick="document.getElementById('google_tag_id').value = ''"></span>
		</div> --}}
</div>
