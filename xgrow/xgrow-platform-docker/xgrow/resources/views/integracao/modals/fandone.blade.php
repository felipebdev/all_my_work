<div id="fandone-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            {{-- <img src="{{ asset('xgrow-vendor/assets/img/google-pixel.png') }}" alt=""> --}}
            <h2 class="text-white">Fandone</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisl eu sit feugiat magna odio. Nibh ante sit tellus ipsum ac penatibus vulputate. Odio nulla eget tortor vel. Nisl id elementum purus nisl vestibulum nisl aliquet aenean eget.</p>
            <a href="#">Saber mais sobre</a>
        </div>
        
        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="5">
            <input type="hidden" name="id_webhook" value="5">

            <div class="column-first">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="fandone-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="fandone-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input id="fandone-url_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine d-none" readonly>
                        <label for="fandone-url_webhook">Url do webhook xgrow</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="fandone-days_limit_payment_pendent" name="days_limit_payment_pendent" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="fandone-days_limit_payment_pendent">Dias limite de pagamento pendente</label>
                    </div>
                    <div class="d-flex form-check form-switch">
                        <input class="form-check-input me-2" type="checkbox" id="fandone-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="fandone-flag_enable">Ativo</label> 
                    </div>
                    <div class="d-flex form-check form-switch">
                        <input class="form-check-input me-2" type="checkbox" id="fandone-trigger_email" name="trigger_email" value="1" checked="">
                        <label class="text-white" for="fandone-trigger_email">Enviar e-mail quando um novo assinante for incluído</label>
                    </div>
                </div>
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button btn-avancar">Avançar</button>
                </div>
            </div>
            <div class="column-two d-none">
                <div class="overflow-auto-fandone">   
                    <p class="text-white font-weigth-bold mt-3">Dados produção</p>
                    <div class="input-two-first">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input required="" id="fandone-prod_count_id" name="prod_count_id" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="fandone-prod_count_id">ID Conta</label>
                        </div>
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input required="" id="fandone-prod_public_key" name="prod_public_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="fandone-prod_public_key">Chave pública</label>
                        </div>
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input required="" id="fandone-prod_secret_key" name="prod_secret_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="fandone-prod_secret_key">Chave privada</label>
                        </div>
                    </div>
                    <p class="text-white font-weigth-bold mt-3">Dados homologação</p>
                    <div class="input-two-first">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input required="" id="fandone-homol_count_id" name="homol_count_id" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="fandone-homol_count_id">ID Conta</label>
                        </div>
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input required="" id="fandone-homol_public_key" name="homol_public_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="fandone-homol_public_key">Chave pública</label>
                        </div>
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input required="" id="fandone-homol_secret_key" name="homol_secret_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="fandone-homol_secret_key">Chave privada</label>
                        </div>
                    </div>
                </div>
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-voltar">Voltar</button>
                    <button type="submit" class="xgrow-button">Integrar</button>
                </div>
            </div>
        </form>
    </div>
</div>