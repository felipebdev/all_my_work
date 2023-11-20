<div class="col-lg-6 col-md-12">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
        <input id="name_webhook" autocomplete="off" type="text" spellcheck="false"
            name="name_integration" value="{{$webhook->name_integration ?? ''}}" required>
        <label>Nome Webhook</label>
    </div>
</div>

<div class="col-lg-6 col-md-12">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
        <input id="days_limit_payment_pendent" autocomplete="off" type="number" min="1"
            name="days_limit_payment_pendent" value="{{$webhook->days_limit_payment_pendent ?? ''}}" required>
        <label>Dias limite de pagamento pendente</label>
    </div>
</div>

<div class="col-lg-6 col-md-12">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
        <input id="token" autocomplete="off" type="text" spellcheck="false"
            name="source_token" value="{{$webhook->source_token ?? ''}}" required
            {{ (isset($webhook->id_webhook) && $webhook->id_webhook === 4) ? 'readonly = "readonly"' : ''  }} >
        <label>Token</label>
        @if (!isset($webhook->id_webhook))
        @endif
    </div>
</div>

<div class="col-12">
    <div class="d-flex form-check form-switch">
        <input class="form-check-input me-2" type="checkbox"
            id="ckbx-style-1-1" name="flag_enable" value="1"
            @if (isset($webhook->flag_enable))
                {{$webhook->flag_enable == 1 ? 'checked' : ''}}
            @else
                checked
            @endif
        />
        Ativo
    </div>

    <div class="d-flex form-check form-switch">
        <input class="form-check-input me-2" type="checkbox"
            id="ckbx-style-1-2" name="trigger_email" value="1"
            @if (isset($webhook->trigger_email))
                {{$webhook->trigger_email == 1 ? 'checked' : ''}}
            @endif
        />
        <label class="w-75" for="">Enviar e-mail quando um novo assinante for incluído</label>
    </div>
</div>

@if(isset($webhook->id) && $webhook->id_webhook == 4 && isset($webhook->tokensGetnet))
    <div class="col-lg-6 col-md-12 mt-3">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Dados produção</p>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_seller_id" autocomplete="off" type="text" spellcheck="false"
                name="prod_seller_id" value="{{ $webhook->tokensGetnet->production->seller_id }}"
                {{ ($webhook->id_webhook === 4) ? 'required' : '' }} >
            <label>Seller Id</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_client_id" autocomplete="off" type="text" spellcheck="false"
                name="prod_client_id" value="{{ $webhook->tokensGetnet->production->client_id }}"
                {{ ($webhook->id_webhook === 4) ? 'required' : '' }} >
            <label>Client Id</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_secret_id" autocomplete="off" type="text" spellcheck="false"
                name="prod_secret_id" value="{{ $webhook->tokensGetnet->production->secret_id }}"
                {{ ($webhook->id_webhook === 4) ? 'required' : '' }} >
            <label>Secret Id</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_url_api" autocomplete="off" type="text" spellcheck="false"
                name="prod_url_api" value="https://api.getnet.com.br" readonly >
            <label>Url Api</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_url_checkout" autocomplete="off" type="text" spellcheck="false"
                name="prod_url_checkout" value="https://homologacao.getnet.com.br" readonly >
            <label>Url Checkout</label>
        </div>
    </div>

    <div class="col-lg-6 col-md-12 mt-3">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Dados homologação</p>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_seller_id" autocomplete="off" type="text" spellcheck="false"
                name="homol_seller_id" value="{{ $webhook->tokensGetnet->local->seller_id }}"
                {{ ($webhook->id_webhook === 4) ? 'required' : '' }} >
            <label>Seller Id</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_client_id" autocomplete="off" type="text" spellcheck="false"
                name="homol_client_id" value="{{ $webhook->tokensGetnet->local->client_id }}"
                {{ ($webhook->id_webhook === 4) ? 'required' : '' }} >
            <label>Client Id</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_secret_id" autocomplete="off" type="text" spellcheck="false"
                name="homol_secret_id" value="{{ $webhook->tokensGetnet->local->secret_id }}"
                {{ ($webhook->id_webhook === 4) ? 'required' : '' }} >
            <label>Secret Id</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_url_api" autocomplete="off" type="text" spellcheck="false"
                name="homol_url_api" value="https://api-homologacao.getnet.com.br" readonly >
            <label>Url Api</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_url_checkout" autocomplete="off" type="text" spellcheck="false"
                name="homol_url_checkout" value="https://checkout-homologacao.getnet.com.br" readonly >
            <label>Url Checkout</label>
        </div>
    </div>
@endif

@if(isset($webhook->id) && $webhook->id_webhook == 5 && isset($webhook->tokensMundipagg))
    <div class="col-lg-6 col-md-12 mt-3">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Dados produção</p>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_count_id" autocomplete="off" type="text" spellcheck="false"
                name="prod_count_id" value="{{ $webhook->tokensMundipagg->production->count_id }}"
                {{ ($webhook->id_webhook === 5) ? 'required' : '' }} >
            <label>Id da Conta</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_public_key" autocomplete="off" type="text" spellcheck="false"
                name="prod_public_key" value="{{ $webhook->tokensMundipagg->production->public_key }}"
                {{ ($webhook->id_webhook === 5) ? 'required' : '' }} >
            <label>Chave pública</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="prod_secret_key" autocomplete="off" type="text" spellcheck="false"
                name="prod_secret_key" value="{{ $webhook->tokensMundipagg->production->secret_key }}"
                {{ ($webhook->id_webhook === 5) ? 'required' : '' }} >
            <label>Chave privada</label>
        </div>
    </div>

    <div class="col-lg-6 col-md-12 mt-3">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Dados homologação</p>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_count_id" autocomplete="off" type="text" spellcheck="false"
                name="homol_count_id" value="{{ $webhook->tokensMundipagg->local->count_id }}"
                {{ ($webhook->id_webhook === 5) ? 'required' : '' }} >
            <label>Id da Conta</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_public_key" autocomplete="off" type="text" spellcheck="false"
                name="homol_public_key" value="{{ $webhook->tokensMundipagg->local->public_key }}"
                {{ ($webhook->id_webhook === 5) ? 'required' : '' }} >
            <label>Chave pública</label>
        </div>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input id="homol_secret_key" autocomplete="off" type="text" spellcheck="false"
                name="homol_secret_key" value="{{ $webhook->tokensMundipagg->local->secret_key }}"
                {{ ($webhook->id_webhook === 5) ? 'required' : '' }} >
            <label>Chave privada</label>
        </div>
    </div>
@endif
