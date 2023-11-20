<div id="modal-webhook" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="mb-2" width="200" src="{{ asset('xgrow-vendor/assets/img/webhook-logo.png') }}" alt="">
            <p>
                Webhook é uma forma de recebimento de informações, que são passadas quando um evento acontece. 
                Dessa forma, o webhook é a forma de enviar informações entre a Xgrow e outro sistema.
            </p>
            <a href="javascript:void(0)" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::WEBHOOK }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::WEBHOOK }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="webhook-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="webhook-is_active">Ativo</label> 
            </div>
            
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="webhook-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="webhook-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="webhook-api_xgrow_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine ipt-not-hidden" value="{{ generateToken(Auth::user()->platform_id) }}" readonly>
                <label for="webhook-api_xgrow_key">Chave da xgrow</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="webhook-api_webhook" name="api_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="webhook-api_webhook">Url do webhook</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>