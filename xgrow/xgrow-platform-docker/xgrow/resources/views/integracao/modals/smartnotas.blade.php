<div id="smartnotas-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/smartnotas-logo.png') }}" alt="">
            <p>SmartNotas é sistema de emissão de notas fiscais inteligente. Ele realiza uma análise detalhada de suas movimentações e recomenda ajustes fiscais que podem chegar a uma redução de até 70% nos seus impostos de maneira legalmente garantida de forma simples, segura e confiável.</p>
            <a href="https://lp.smart-notas.com/" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="11">
            <input type="hidden" name="id_webhook" value="11">
            
            <div class="column-first ">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="smartnotas-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="smartnotas-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input id="smartnotas-api_xgrow_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine ipt-not-hidden" value="{{ Auth::user()->platform_id }}" readonly>
                        <label for="smartnotas-api_xgrow_key">Chave da api xgrow</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="smartnotas-url_webhook" name="url_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="smartnotas-url_webhook">Link do webhook smartnotas</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="smartnotas-process_after_days" name="process_after_days" type="number" class="mui--is-empty mui--is-untouched mui--is-pristine" min="0" max="30" step="1">
                        <label for="smartnotas-process_after_days">Garantia (0 para processamento imediato) </label>
                    </div>
                    <div class="d-flex form-check form-switch mb-3">
                        <input class="form-check-input me-2" type="checkbox" id="smartnotas-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="smartnotas-flag_enable">Ativo</label> 
                    </div>
                </div>
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button btn-avancar">Avançar</button>
                </div>
            </div>
            <div class="column-two d-none">
                <div class="top-column-two">
                    <label for="">Em quais eventos a integração será acionada?</label>
                    <div class="mt-2 mb-2">
                        <div class="checkbox-modal">
                            <input id="smartnotas-on_approve_payment" type="checkbox" name="events[on_approve_payment]" value="true" checked>
                            <label for="smartnotas-on_approve_payment" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="smartnotas-on_approve_payment"><strong>Pagamento aprovado</strong></label>
                                <label for="smartnotas-on_approve_payment"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="smartnotas-do_sefaz_doc" type="checkbox" name="events[on_approve_payment][do_sefaz_doc]" value="true" checked>
                                <label for="smartnotas-do_sefaz_doc" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="smartnotas-do_sefaz_doc">Gerar nota fiscal</label>
                                    <label for="smartnotas-do_sefaz_doc">Para cada pagamento aprovado será emitido uma nota fiscal</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-modal mt-3">
                    <button class="xgrow-button-cancel btn-voltar">Voltar</button>
                    <button type="submit" class="xgrow-button">Integrar</button>
                </div>
            </div>
        </form>
    </div>
</div>