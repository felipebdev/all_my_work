<div id="digitalmanagerguru-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/digitalmanagerguru.png') }}" alt="">
            <p>Reúna tudo o que precisa para otimizar seu negócio online e gerenciar toda a estratégia digital num só lugar, com total liberdade de escolha sobre quais serviços e ferramentas utilizar.</p>
            <a href="#">Saber mais sobre</a>
        </div>

        <form class="column-first" action="{{ url('/integracao/store') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="13">
            <input type="hidden" name="id_webhook" value="13">

            <div class="input-two-first">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input required="" id="digitalmanagerguru-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="digitalmanagerguru-name_integration">Nome da integração</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="digitalmanagerguru-url_webhook" type="text" name="url_webhook" class="mui--is-empty mui--is-untouched mui--is-pristine d-none" readonly>
                    <label for="digitalmanagerguru-url_webhook">Url do webhook xgrow</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input required="" id="digitalmanagerguru-api_key" name="digitalmanagerguru_api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="digitalmanagerguru-api_key">Chave da Digital Manager</label>
                </div>
                <div class="d-flex form-check form-switch">
                    <input class="form-check-input me-2" type="checkbox" id="digitalmanagerguru-flag_enable" name="flag_enable" value="1" checked="">
                    <label class="text-white" for="digitalmanagerguru-flag_enable">Ativo</label>
                </div>
            </div>

            <div class="footer-modal">
                <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button ">Integrar</button>
            </div>
        </form>
    </div>
</div>
