<div id="modal-enotas" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <div class="mb-3 text-center">
                <img class="w-75" src="{{ asset('xgrow-vendor/assets/img/enotas-logo-white.png') }}" alt="Ícone do enotas">
            </div>
            <p>Cuidamos das notas fiscais para você cuidar do seu negócio.
                <a href="https://enotas.com.br/" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::ENOTAS }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::ENOTAS }}">

            <div class="d-flex form-check form-switch mb-4">
                <input class="form-check-input me-2" type="checkbox" id="enotas-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="enotas-is_active">Ativo</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="enotas-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="enotas-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="enotas-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine ipt-not-hidden">
                <label for="enotas-api_key">Chave da API</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="enotas-process_after_days" name="metadata[process_after_days]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine" min="0" max="30" step="1">
                <label for="enotas-process_after_days">Garantia (0 para processamento imediato) </label>
            </div>
            <div class="footer-modal p-0 mt-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
