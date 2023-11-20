<div id="facebook-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper" style="height: 640px">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/facebook-pixel.png') }}" alt="">
            <p>O Facebook Pixel é uma ferramenta analítica que o Facebook disponibiliza para ajudar a mensurar o sucesso de uma campanha patrocinada a partir de um código inserido em cada página de seu site.</p>
            <a href="https://pt-br.facebook.com/business/learn/facebook-ads-pixel" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="9">
            <input type="hidden" name="id_webhook" value="9">

            <div class="column-first ">
                <div class="input-two-first" style="padding-top: 8px">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="fb-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="fb-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input required="" id="fb-pixel_id" name="pixel_id" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="fb-fp_id">ID do pixel Facebook</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input required="" id="fb-pixel_token" name="pixel_token" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="fb-fp_token">Token de acesso</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input id="fb-pixel_test_event_code" name="pixel_test_event_code" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="fb-fp_test_event_code">Código para teste de evento</label>
                    </div>
                    <div class="mt-3 text-light small">Use esse código para testar eventos via servidor. Remova após testes.</div>
                    <div class="d-flex form-check form-switch mt-3">
                        <input class="form-check-input me-2" type="checkbox" id="fb-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="fb-flag_enable">Ativo</label>
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
                        <input id="fb-checkout_visit" type="checkbox" name="infos[fb_checkout_visit]" value="true">
                        <label for="fb-checkout_visit" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="fb-checkout_visit">Início de finalização de compra</label>
                            <label for="fb-checkout_visit">Você saberá quantas pessoas visitaram a página de pagamento</label>
                        </div>
                    </div>
                    <div class="checkbox-modal">
                        <input id="fb-sales_conversion" type="checkbox" name="infos[fb_sales_conversion]" value="true">
                        <label for="fb-sales_conversion" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="fb-sales_conversion">Conversão de vendas</label>
                            <label for="fb-sales_conversion">Você saberá quantas pessoas chegaram até a “página de obrigado” do produto </label>
                        </div>
                    </div>
                </div>
                <div class="middle-column-two">
                    <p>Avançado</p>
                    <div class="confirms">
                        <div class="receber">
                            <p>Receber confirmação de venda de qual meio de pagamento?</p>
                            <div class="radio-confirmacao">
                                <input id="fb-all_payment_methods" name="infos[fb_all_payment_methods]" type="radio" value="true">
                                <label for="fb-all_payment_methods" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="fb-all_payment_methods">Todos os meios de pagamento</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="fb-card_payment_methods" name="infos[fb_card_payment_methods]" type="radio" value="true">
                                <label for="fb-card_payment_methods" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="fb-card_payment_methods">Somente cartão de crédito</label>
                                </div>
                            </div>
                        </div>
{{--                        <div class="receber mt-3">--}}
{{--                            <p>Receber confirmação com qual valor?</p>--}}
{{--                            <div class="radio-confirmacao">--}}
{{--                                <input id="fb-sale_real_price" name="infos[fb_sale_real_price]" type="radio" value="true">--}}
{{--                                <label for="fb-sale_real_price" class="radio-input-label"></label>--}}
{{--                                <div class="label-right-radio">--}}
{{--                                    <label for="fb-sale_real_price">Valor real da venda</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="radio-confirmacao">--}}
{{--                                <input id="fb-sale_client_price" name="infos[fb_sale_client_price]" type="radio" value="true">--}}
{{--                                <label for="fb-sale_client_price" class="radio-input-label"></label>--}}
{{--                                <div class="label-right-radio">--}}
{{--                                    <label for="fb-sale_client_price">Valor que eu definir</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div>--}}
{{--                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">--}}
{{--                                    <input id="fb-sale_price_defined" autocomplete="off" spellcheck="false"--}}
{{--                                           class="mui--is-not-empty mui--is-pristine mui--is-empty mui--is-touched"--}}
{{--                                           required=""--}}
{{--                                           maxlength="10" name="infos[fb_sale_price_defined]" type="text">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
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