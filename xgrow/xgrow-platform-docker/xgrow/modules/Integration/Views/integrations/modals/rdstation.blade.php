<div id="modal-rdstation" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>

        <div class="integration-info">
            <div class="mb-3 text-center">
                <img class="w-75" src="{{ asset('xgrow-vendor/assets/img/rdstation.png') }}" alt="Imagem de apresentação da RD Station em verde e preto">
            </div>
            <p>Ferramenta para automação em marketing digital e vendas.
                <a href="https://rdstation.com.br" target="_blank">Saber mais sobre</a>
            </p>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::RDSTATION }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::RDSTATION }}">

            <div class="d-flex form-check form-switch mb-4">
                <input class="form-check-input me-2" type="checkbox" id="rdstation-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="rdstation-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="rdstation-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="rdstation-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="rdstation-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="rdstation-api_key">API key</label>
            </div>
            <div class="footer-modal p-0 mt-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
