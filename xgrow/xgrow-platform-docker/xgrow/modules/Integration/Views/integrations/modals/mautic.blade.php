<div id="modal-mautic" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="rounded" width="240" src="{{ asset('xgrow-vendor/assets/img/mautic-logo.png') }}" alt="">
            <p class="mt-2">
            Mautic é uma plataforma para automatização de marketing digital.
                <a href="https://www.mautic.org" target="_blank">Saber mais sobre</a>
            </p>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::MAUTIC }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::MAUTIC }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="mautic-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="mautic-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="mautic-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mautic-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" placeholde="https://seu-mautic.com" id="mautic-api_webhook" name="api_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mautic-api_webhook">URL do seu Mautic</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="mautic-api_account" name="api_account" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mautic-api_account">Email da sua conta Mautic</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="mautic-api_key" name="api_key" type="password" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mautic-api_key">Senha da sua conta Mautic</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>