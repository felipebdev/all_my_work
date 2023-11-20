<div id="modal-pipedrive" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="rounded" width="240" src="{{ asset('xgrow-vendor/assets/img/pipedrive-logo.png') }}" alt="">
            <p class="mt-2">
                Pipedrive é uma plataforma para te auxiliar a focar nas negociações certas.
                <a href="https://www.pipedrive.com" target="_blank">Saber mais sobre</a>
            </p>
            <p>
                <a href="https://xgrow-docs.vercel.app/docs/integrations/Pipedrive" target="_blank">Como encontrar as informações abaixo</a>
            </p>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::PIPEDRIVE }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::PIPEDRIVE }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="pipedrive-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="pipedrive-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="pipedrive-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="pipedrive-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="pipedrive-api_account" name="api_account" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="pipedrive-api_account">Domínio da conta Pipedrive</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="pipedrive-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="pipedrive-api_key">Token da API Pipedrive</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>