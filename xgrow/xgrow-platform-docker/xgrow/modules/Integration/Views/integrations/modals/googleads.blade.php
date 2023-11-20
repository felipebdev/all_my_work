<div id="modal-googleads" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <div class="mb-3 text-center">
                <img class="w-75" src="{{ asset('xgrow-vendor/assets/img/google-pixel.png') }}" alt="Ícone do apresentação do google ads">
            </div>
            <p>Alcance novos clientes e expanda seus negócios com o Google Ads, a solução de publicidade on-line do Google.
                <a href="https://support.google.com/google-ads/answer/6146252?utm_medium=et&utm_campaign=pt-BR&utm_source=ww-ww-et-b2bfooter_adwords&subid=br-pt-ha-awa-bk-c-cor!o3~Cj0KCQjwyN-DBhCDARIsAFOELTkOemW8GNPCcqg4bqafL7IYN1-Gu7aB2T9oyLydjiaY-H7RRRmtVacaAt9yEALw_wcB~84865307024~kwd-94527731~6500862360~437246732843" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::GOOGLEADS }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::GOOGLEADS }}">

            <div class="google-part-1">
                <div class="d-flex form-check form-switch mb-3">
                    <input class="form-check-input me-2" type="checkbox" id="google-is_active" name="is_active" value="1" checked="">
                    <label class="text-white" for="google-is_active">Ativo</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                    <input required="" id="google-description" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="google-description">Nome da integração</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                    <input required="" id="google-ads_id" name="metadata[adsId]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="google-ads_id">ID do pixel Adwords</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input required="" id="google-ads_conversion_label" name="metadata[adsConversionLabel]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="google-ads_conversion_label">Label de conversão do Adwords</label>
                </div>
                <div class="footer-modal p-0 my-4">
                    <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="button" class="xgrow-button btn-avancar" data-integration-name="google" data-page="1">Avançar</button>
                </div>
            </div>

            <div class="google-part-2 d-none">
                <p class="mt-3">Quais informações deseja receber?</p>
                <div class="checkbox-modal">
                    <input id="google-ads_checkout_visit" type="checkbox" name="metadata[adsCheckoutVisit]" value="true">
                    <label for="google-ads_checkout_visit" class="check-input-label"></label>
                    <div class="label-right-check">
                        <label for="google-ads_checkout_visit">Visitas em checkout</label>
                        <label for="google-ads_checkout_visit">Você saberá quantas pessoas visitaram a página de pagamento</label>
                    </div>
                </div>
                <div class="checkbox-modal">
                    <input id="google-ads_sales_conversion" type="checkbox" name="metadata[adsSalesConversion]" value="true">
                    <label for="google-ads_sales_conversion" class="check-input-label"></label>
                    <div class="label-right-check">
                        <label for="google-ads_sales_conversion">Conversão de vendas</label>
                        <label for="google-ads_sales_conversion">Você saberá quantas pessoas chegaram até a “página de obrigado” do produto </label>
                    </div>
                </div>

                <p class="mt-3">Avançado</p>
                <div class="confirms">
                    <div class="receber">
                        <p>Receber confirmação de venda de qual meio de pagamento?</p>
                        <div class="radio-confirmacao">
                            <input id="google-ads_all_payment_methods" type="radio" name="metadata[adsPaymentMethods]" value="all" checked>
                            <label for="google-ads_all_payment_methods" class="radio-input-label"></label>
                            <div class="label-right-radio">
                                <label for="google-ads_all_payment_methods">Todos os meios de pagamento</label>
                            </div>
                        </div>
                        <div class="radio-confirmacao">
                            <input id="google-ads_card_payment_methods" type="radio" name="metadata[adsPaymentMethods]" value="card">
                            <label for="google-ads_card_payment_methods" class="radio-input-label"></label>
                            <div class="label-right-radio">
                                <label for="google-ads_card_payment_methods">Somente cartão de crédito</label>
                            </div>
                        </div>
                    </div>
                    <div class="receber mt-3">
                        <p>Receber confirmação com qual valor?</p>
                        <div class="radio-confirmacao">
                            <input id="google-ads_sale_real_price" type="radio" name="metadata[adsSalePrice]" value="sale" checked>
                            <label for="google-ads_sale_real_price" class="radio-input-label"></label>
                            <div class="label-right-radio">
                                <label for="google-ads_sale_real_price">Valor real da venda</label>
                            </div>
                        </div>
                        <div class="radio-confirmacao">
                            <input id="google-ads_sale_client_price" type="radio" name="metadata[adsSalePrice]" value="defined">
                            <label for="google-ads_sale_client_price" class="radio-input-label"></label>
                            <div class="label-right-radio">
                                <label for="google-ads_sale_client_price">Valor que eu definir</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-modal p-0 mt-4">
                    <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>