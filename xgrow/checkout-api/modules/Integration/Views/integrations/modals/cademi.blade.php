<div id="modal-cademi" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img src="{{ asset('xgrow-vendor/assets/img/cademi.png') }}" alt="">
            <p>
                Cademí é uma plataforma de ensino e edutenimento com uma experiência de aprendizagem moderna e
                intuitiva. Aumente a retenção e aproveitamento dos seus alunos em seus cursos e treinamentos online.
            </p>
            <a href="https://www.cademi.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::CADEMI }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::CADEMI }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="cademi-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="cademi-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="cademi-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="cademi-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="cademi-api_webhook" name="api_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="cademi-api_webhook">Url da api cademí</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="cademi-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="cademi-api_key">Token da api cademí</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>