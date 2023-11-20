<div id="google-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/google-pixel.png') }}" alt="">
            <p>Alcance novos clientes e expanda seus negócios com o Google Ads, a solução de publicidade on-line do Google.</p>
            <a href="https://support.google.com/google-ads/answer/6146252?utm_medium=et&utm_campaign=pt-BR&utm_source=ww-ww-et-b2bfooter_adwords&subid=br-pt-ha-awa-bk-c-cor!o3~Cj0KCQjwyN-DBhCDARIsAFOELTkOemW8GNPCcqg4bqafL7IYN1-Gu7aB2T9oyLydjiaY-H7RRRmtVacaAt9yEALw_wcB~84865307024~kwd-94527731~6500862360~437246732843" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="10">
            <input type="hidden" name="id_webhook" value="10">

            <div class="column-first">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="google-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="google-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="google-ads_id" name="ads_id" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="google-ads_id">ID do pixel Adwords</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input required="" id="google-ads_conversion_label" name="infos[ads_conversion_label]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="google-ads_conversion_label">Label de conversão do Adwords</label>
                    </div>
                    <div class="d-flex form-check form-switch mt-2">
                        <input class="form-check-input me-2" type="checkbox" id="google-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="google-flag_enable">Ativo</label> 
                    </div>
                </div>
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button btn-avancar">Avançar</button>
                </div>
            </div>
            <div class="column-two d-none">
                <div class="top-column-two">
                    <label for="">Quais informações deseja receber?</label>
                    <div class="checkbox-modal">
                        <input id="google-ads_checkout_visit" type="checkbox" name="infos[ads_checkout_visit]" value="true">
                        <label for="google-ads_checkout_visit" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="google-ads_checkout_visit">Visitas em checkout</label>
                            <label for="google-ads_checkout_visit">Você saberá quantas pessoas visitaram a página de pagamento</label>
                        </div>
                    </div>
                    <div class="checkbox-modal">
                        <input id="google-ads_sales_conversion" type="checkbox" name="infos[ads_sales_conversion]" value="true">
                        <label for="google-ads_sales_conversion" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="google-ads_sales_conversion">Conversão de vendas</label>
                            <label for="google-ads_sales_conversion">Você saberá quantas pessoas chegaram até a “página de obrigado” do produto </label>
                        </div>
                    </div>
                </div>
                <div class="middle-column-two">
                    <p>Avançado</p>
                    <div class="confirms">
                        <div class="receber">
                            <p>Receber confirmação de venda de qual meio de pagamento?</p>
                            <div class="radio-confirmacao">
                                <input id="google-ads_all_payment_methods" type="radio" name="infos[ads_all_payment_methods]" value="true">
                                <label for="google-ads_all_payment_methods" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="google-ads_all_payment_methods">Todos os meios de pagamento</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="google-ads_card_payment_methods" type="radio" name="infos[ads_card_payment_methods]" value="true">
                                <label for="google-ads_card_payment_methods" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="google-ads_card_payment_methods">Somente cartão de crédito</label>
                                </div>
                            </div>
                        </div>
                        <div class="receber mt-3">
                            <p>Receber confirmação com qual valor?</p>
                            <div class="radio-confirmacao">
                                <input id="google-ads_sale_real_price" type="radio" name="infos[ads_sale_real_price]" value="true">
                                <label for="google-ads_sale_real_price" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="google-ads_sale_real_price">Valor real da venda</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="google-ads_sale_client_price" type="radio" name="infos[ads_sale_client_price]" value="true">
                                <label for="google-ads_sale_client_price" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="google-ads_sale_client_price">Valor que eu definir</label>
                                </div>
                            </div>
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