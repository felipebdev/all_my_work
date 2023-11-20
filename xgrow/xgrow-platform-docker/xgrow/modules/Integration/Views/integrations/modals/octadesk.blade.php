<div id="modal-octadesk" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img width="230" src="{{ asset('xgrow-vendor/assets/img/octadesk-logo.png') }}" alt="">
            <p>
                Sistema de Marketing, Vendas e Atendimento. O sistema perfeito para equipes de marketing, vendas e
                atendimento alcançarem resultados incríveis com menos esforço.
            </p>
            <a href="https://pt.octadesk.com/funcionalidades/integracoes" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::OCTADESK }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::OCTADESK }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="octadesk-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="octadesk-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="octadesk-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="octadesk-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="octadesk-api_account" name="api_account" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="octadesk-api_account">E-mail da conta octadesk</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="octadesk-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="octadesk-api_key">Chave da api octadesk</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>