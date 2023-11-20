<div id="modal-tiktok" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>

        <div class="integration-info">
            <div class="mb-3 text-center">
                <img class="w-75" src="{{ asset('xgrow-vendor/assets/img/integrations/tiktok-banner.svg') }}" alt="Ícone claro do TikTok">
            </div>
            <p>TikTok onde as tendências começam aqui. Em um dispositivo ou na web, os espectadores podem
                assistir e descobrir milhões de vídeos curtos personalizados.
                <a href="https://www.tiktok.com/pt-BR/" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::TIKTOK }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::TIKTOK }}">

            <div class="d-flex form-check form-switch mb-4">
                <input class="form-check-input me-2" type="checkbox" id="tiktok-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="tiktok-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="tiktok-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="tiktok-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="tiktok-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="tiktok-api_key">Chave pixel do TikTok</label>
            </div>
            <div class="footer-modal p-0 mt-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
