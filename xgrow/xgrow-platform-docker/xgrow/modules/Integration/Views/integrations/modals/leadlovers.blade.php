<div id="modal-leadlovers" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="rounded" width="240" src="{{ asset('xgrow-vendor/assets/img/leadlovers-logo.png') }}" alt="">
            <p class="mt-2">
                LeadLovers é uma plataforma de automação de marketing digital.
            </p>
            <a href="https://www.leadlovers.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::LEADLOVERS }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::LEADLOVERS }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="leadlovers-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="leadlovers-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="leadlovers-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="leadlovers-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="leadlovers-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mailchimp-api_key">Chave da API LeadLovers</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>