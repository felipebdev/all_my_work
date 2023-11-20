<div id="modal-facebookpixel" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img class="mb-2" width="200" src="{{ asset('xgrow-vendor/assets/img/facebook-pixel.png') }}" alt="">
            <p>
                O Facebook Pixel é uma ferramenta analítica que o Facebook disponibiliza para ajudar a mensurar o
                sucesso de uma campanha patrocinada a partir de um código inserido em cada página de seu site.
            </p>
            <a href="https://pt-br.facebook.com/business/learn/facebook-ads-pixel" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::FACEBOOKPIXEL }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::FACEBOOKPIXEL }}">

            <div class="facebook-part-1">
                <div class="d-flex form-check form-switch mb-3">
                    <input class="form-check-input me-2" type="checkbox" id="facebookpixel-is_active" name="is_active" value="1" checked="">
                    <label class="text-white" for="facebookpixel-is_active">Ativo</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="facebookpixel-description" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="facebookpixel-description">Nome da integração</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="facebookpixel-api_account" name="api_account" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="facebookpixel-api_account">ID do pixel Facebook</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="facebookpixel-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="facebookpixel-api_key">Token de acesso</label>
                </div>
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                    <input id="facebookpixel-test_event_code" name="metadata[test_event_code]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="facebookpixel-test_event_code">Código para teste de evento</label>
                </div>
                <div class="mt-2 text-light small">Use esse código para testar eventos via servidor. Remova após testes.</div>
                <div class="footer-modal p-0 my-4">
                    <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="button" class="xgrow-button btn-avancar" data-integration-name="facebook" data-page="1">Avançar</button>
                </div>
            </div>

            <div class="facebook-part-2 d-none">
                <label for="">Quais informações deseja receber?</label>
                <div class="checkbox-modal">
                    <input id="facebookpixel-checkout_visit" type="checkbox" name="metadata[checkout_visit]" value="true">
                    <label for="facebookpixel-checkout_visit" class="check-input-label"></label>
                    <div class="label-right-check">
                        <label for="facebookpixel-checkout_visit">Início de finalização de compra</label>
                        <label for="facebookpixel-checkout_visit">Você saberá quantas pessoas visitaram a página de pagamento</label>
                    </div>
                </div>

                <div class="checkbox-modal">
                    <input id="facebookpixel-sales_conversion" type="checkbox" name="metadata[sales_conversion]" value="true">
                    <label for="facebookpixel-sales_conversion" class="check-input-label"></label>
                    <div class="label-right-check">
                        <label for="facebookpixel-sales_conversion">Conversão de vendas</label>
                        <label for="facebookpixel-sales_conversion">Você saberá quantas pessoas chegaram até a “página de obrigado” do produto </label>
                    </div>
                </div>

                <p class="mt-3">Avançado</p>
                <div class="confirms">
                    <div class="receber">
                        <p>Receber confirmação de venda de qual meio de pagamento?</p>
                        <div class="radio-confirmacao">
                            <input id="facebookpixel-all_payment_methods" name="metadata[payment_method]" type="radio" value="all_payment_methods">
                            <label for="facebookpixel-all_payment_methods" class="radio-input-label"></label>
                            <div class="label-right-radio">
                                <label for="facebookpixel-all_payment_methods">Todos os meios de pagamento</label>
                            </div>
                        </div>
                        <div class="radio-confirmacao">
                            <input id="facebookpixel-card_payment_methods" name="metadata[payment_method]" type="radio" value="card_payment_methods">
                            <label for="facebookpixel-card_payment_methods" class="radio-input-label"></label>
                            <div class="label-right-radio">
                                <label for="facebookpixel-card_payment_methods">Somente cartão de crédito</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-modal p-0 my-4">
                    <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
