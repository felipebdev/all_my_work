<div id="hotmart-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/hotmart.png') }}" alt="">
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
                    <input required="" id="hotmart-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="hotmart-name_integration">Nome da integração</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="hotmart-url_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine d-none" readonly>
                    <label for="hotmart-url_webhook">Url do webhook xgrow</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input required="" id="hotmart-days_limit_payment_pendent" name="days_limit_payment_pendent" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="hotmart-days_limit_payment_pendent">Dias limite de pagamento pendente</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                    <input required="" id="hotmart-source_token" name="source_token" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="hotmart-source_token">Token hotmart</label>
                </div>
                <div class="d-flex form-check form-switch">
                    <input class="form-check-input me-2" type="checkbox" id="hotmart-flag_enable" name="flag_enable" value="1" checked="">
                    <label class="text-white" for="hotmart-flag_enable">Ativo</label> 
                </div>
                <div class="d-flex form-check form-switch">
                    <input class="form-check-input me-2" type="checkbox" id="hotmart-trigger_email" name="trigger_email" value="1" checked="">
                    <label class="text-white" for="hotmart-trigger_email">Enviar e-mail quando um novo assinante for incluído</label>
                </div>
            </div>

            <div class="footer-modal">
                <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button ">Integrar</button>
            </div>
        </form>
    </div>
</div>