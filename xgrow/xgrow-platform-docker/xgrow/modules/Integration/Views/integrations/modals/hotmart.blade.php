<div id="modal-hotmart" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/hotmart.png') }}" alt="">
            <p>Transforme o que você sabe em um produto digital e venda para milhões de pessoas ao redor do mundo.</p>
            <a href="https://www.hotmart.com/pt-BR" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="1">
            <input type="hidden" name="id_webhook" value="1">

            <div class="d-flex form-check form-switch">
                <input class="form-check-input me-2 mb-2" type="checkbox" id="hotmart-flag_enable" name="flag_enable" value="1" checked="">
                <label class="text-white" for="hotmart-flag_enable">Ativo</label> 
            </div>
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
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="hotmart-source_token" name="source_token" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="hotmart-source_token">Token hotmart</label>
            </div>
            <div class="d-flex form-check form-switch">
                <input class="form-check-input me-2 mb-3" type="checkbox" id="hotmart-trigger_email" name="trigger_email" value="1" checked="">
                <label class="text-white" for="hotmart-trigger_email">Enviar e-mail quando um novo assinante for incluído</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>