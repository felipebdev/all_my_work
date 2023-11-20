<div id="pandavideo-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/panda-video.png') }}" alt="">
            <p>Segurança em hospedagem de vídeos para marketing digital e cursos online.</p>
            <a href="https://prd.pandavideo.com.br/">Criar conta gratuita</a>
        </div>

        <form class="column-first" action="{{ url('/integracao/store') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="15">
            <input type="hidden" name="id_webhook" value="15">

            <div class="input-two-first">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input required="" id="pandavideo-name_integration" name="name_integration" type="text"
                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="pandavideo-name_integration">Nome da integração</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input required="" id="pandavideo-api_key" name="pandavideo_api_key" type="text"
                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="pandavideo-api_key">Chave da Panda Video</label>
                </div>
                <div class="d-flex form-check form-switch">
                    <input class="form-check-input me-2" type="checkbox" id="pandavideo-flag_enable" name="flag_enable"
                           value="1" checked="">
                    <label class="text-white" for="pandavideo-flag_enable">Ativo</label>
                </div>
            </div>

            <div class="footer-modal">
                <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button ">Integrar</button>
            </div>
        </form>
    </div>
</div>
