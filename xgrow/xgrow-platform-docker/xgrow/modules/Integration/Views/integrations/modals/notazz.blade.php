<div id="modal-notazz" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <span>
                <img class="mb-2" width="100" src="{{ asset('xgrow-vendor/assets/img/integrations/notazz.png') }}"
                    alt="">
            </span>
            <p>Cuide do seu negócio, as notas fiscais ficam por nossa conta, automatizamos todo o processo de emissão de
                notas fiscais da sua empresa.</p>
            <a href="https://notazz.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::NOTAZZ }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::NOTAZZ }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="notazz-is_active" name="is_active"
                    value="1" checked="">
                <label class="text-white" for="notazz-is_active">Ativo</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="notazz-description_integration" name="description" type="text"
                    class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="notazz-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="notazz-api_key" name="api_key" type="text"
                    class="mui--is-empty mui--is-untouched mui--is-pristine ipt-not-hidden">
                <label for="notazz-api_key">Chave da API</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="notazz-process_after_days" name="metadata[process_after_days]" type="text"
                    class="mui--is-empty mui--is-untouched mui--is-pristine" min="0" max="30"
                    step="1">
                <label for="notazz-process_after_days">Garantia (0 para processamento imediato) </label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
