<div id="modal-kajabi" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img src="{{ asset('xgrow-vendor/assets/img/kajabi.png') }}" alt="">
            <p>
                Kajabi é um sistema de marketing de conteúdo que oferece aos indivíduos e pequenas e médias empresas uma
                plataforma única e centralizada para vender, comercializar e entregar o conteúdo do produto.
            </p>
            <a href="https://www.kajabi.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::KAJABI }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::KAJABI }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="kajabi-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="kajabi-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="kajabi-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="kajabi-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="kajabi-api_account" name="api_account" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="kajabi-api_account">E-mail da conta kajabi</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>