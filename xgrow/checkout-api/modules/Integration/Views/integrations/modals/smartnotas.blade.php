<div id="modal-smartnotas" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="mb-2" width="200" src="{{ asset('xgrow-vendor/assets/img/smartnotas-logo.png') }}" alt="">
            <p>
                SmartNotas é sistema de emissão de notas fiscais inteligente. Ele realiza uma análise detalhada de suas
                movimentações e recomenda ajustes fiscais de forma simples, segura e confiável.
            </p>
            <a href="https://lp.smart-notas.com/" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::SMARTNOTAS }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::SMARTNOTAS }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="smartnotas-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="smartnotas-is_active">Ativo</label> 
            </div>
            
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="smartnotas-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="smartnotas-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="smartnotas-api_xgrow_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine ipt-not-hidden" value="{{ Auth::user()->platform_id }}" readonly>
                <label for="smartnotas-api_xgrow_key">Chave da api xgrow</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="smartnotas-api_webhook" name="api_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="smartnotas-api_webhook">Link do webhook smartnotas</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="smartnotas-process_after_days" name="metadata[process_after_days]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine" min="0" max="30" step="1">
                <label for="smartnotas-process_after_days">Garantia (0 para processamento imediato) </label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>