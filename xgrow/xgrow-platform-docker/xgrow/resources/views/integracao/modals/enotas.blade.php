<div id="enotas-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/enotas-logo.png') }}" alt="">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisl eu sit feugiat magna odio. Nibh ante sit tellus ipsum ac penatibus vulputate. Odio nulla eget tortor vel. Nisl id elementum purus nisl vestibulum nisl aliquet aenean eget.</p>
            <a href="#">Saber mais sobre</a>
        </div>

        <form class="column-first" action="{{ url('/integracao/store') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="1">
            <input type="hidden" name="id_webhook" value="1">
            
            <div class="input-two-first">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="enotas-url_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine d-none" readonly>
                    <label for="enotas-url_webhook">URL</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input required="" id="enotas-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="enotas-name_integration">Nome da integração</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                    <input required="" id="enotas-source_token" name="source_token" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="enotas-source_token">API Token</label>
                </div>
                <div class="d-flex form-check form-switch">
                    <input class="form-check-input me-2" type="checkbox" id="enotas-flag_enable" name="flag_enable" value="1" checked="">
                    <label class="text-white" for="enotas-flag_enable">Ativo</label> 
                </div>
                <div class="d-flex form-check form-switch">
                    <input class="form-check-input me-2" type="checkbox" id="enotas-trigger_email" name="trigger_email" value="1" checked="">
                    <label class="text-white" for="enotas-trigger_email">Enviar por e-mail a nota fiscal para o aluno</label>
                </div>
            </div>

            <div class="footer-modal">
                <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button ">Integrar</button>
            </div>
        </form>
    </div>
</div>