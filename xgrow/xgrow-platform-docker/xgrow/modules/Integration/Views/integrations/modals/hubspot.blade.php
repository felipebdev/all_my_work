<div id="modal-hubspot" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img src="{{ asset('xgrow-vendor/assets/img/hubspot.png') }}" alt="">
            <p>
                Plataforma de software de marketing, vendas, atendimento ao cliente e CRM, metodologia, recursos e suporte.
            </p>
            <a href="https://www.hubspot.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::HUBSPOT }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::HUBSPOT }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="hubspot-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="hubspot-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="hubspot-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="hubspot-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="hubspot-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="hubspot-api_key">Chave da API HubSpot</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
