<div id="modal-memberkit" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>

        <div class="integration-info">
            <div class="mb-3 text-center">
                <img class="w-75" src="{{ asset('xgrow-vendor/assets/img/integrations/memberkit-white-logo.svg') }}" alt="Ícone do enotas">
            </div>
            <p>MemberKit ajuda você a vender mais todos os meses, aumentando 10x o engajamento dos seus alunos
                com seu material e estimulando indicações e recompra!
                <a href="https://memberkit.com.br/" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::MEMBERKIT }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::MEMBERKIT }}">

            <div class="d-flex form-check form-switch mb-4">
                <input class="form-check-input me-2" type="checkbox" id="memberkit-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="memberkit-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="memberkit-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="memberkit-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="memberkit-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="memberkit-api_key">Chave secreta da memberKit</label>
            </div>
            <div class="footer-modal p-0 mt-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>