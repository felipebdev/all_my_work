<div id="modal-mailchimp" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="rounded" width="240" src="{{ asset('xgrow-vendor/assets/img/mailchimp-logo.jpg') }}" alt="">
            <p class="mt-2">
                Faça tudo com o Mailchimp. Reúna os dados do seu público, canais de marketing e insights para que você alcance suas metas com mais rapidez – tudo em uma única plataforma.
            </p>
            <a href="https://www.mailchimp.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::MAILCHIMP }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::MAILCHIMP }}">
            
            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="mailchimp-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="mailchimp-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="mailchimp-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mailchimp-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="mailchimp-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mailchimp-api_key">Chave da api mailchimp</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>