<div id="modal-voxuy" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="mb-2" width="75%" src="{{ asset('xgrow-vendor/assets/img/voxuy-logo.png') }}" alt="">
            <p>Para você, que precisa gerenciar seu tempo, converter mais vendas e fortalecer relacionamentos.
            <a href="https://www.voxuy.com/" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::VOXUY }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::VOXUY }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="voxuy-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="voxuy-is_active">Ativo</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="voxuy-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="voxuy-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="voxuy-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine ipt-not-hidden">
                <label for="voxuy-api_key">Chave da API</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="voxuy-api_webhook" name="api_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="voxuy-api_webhook">Link do Webhook Voxuy</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="voxuy-planId" name="metadata[planId]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="voxuy-planId">ID do Plano </label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
