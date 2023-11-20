<div id="octadesk-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/octadesk-logo.png') }}" alt="">
            <p>Sistema de Marketing, Vendas e Atendimento. O sistema perfeito para equipes de marketing, vendas e atendimento alcançarem resultados incríveis com menos esforço.</p>
            <a href="https://pt.octadesk.com/funcionalidades/integracoes" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="12">
            <input type="hidden" name="id_webhook" value="12">
            
            <div class="column-first mt-3">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="octadesk-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="octadesk-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="octadesk-email_client" name="email_client" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="octadesk-email_client">E-mail da conta octadesk</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="octadesk-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="octadesk-api_key">Chave da api octadesk</label>
                    </div>
                    <div class="d-flex form-check form-switch mb-3">
                        <input class="form-check-input me-2" type="checkbox" id="octadesk-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="octadesk-flag_enable">Ativo</label> 
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
                            <input id="octadesk-on_create_lead" type="checkbox" name="events[on_create_lead]" value="true" checked>
                            <label for="octadesk-on_create_lead" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="octadesk-on_create_lead"><strong>Lead criado</strong></label>
                                <label for="octadesk-on_create_lead"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="octadesk-do_insert_lead" type="checkbox" name="events[on_create_lead][do_insert_lead]" value="true" checked>
                                <label for="octadesk-do_insert_lead" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="octadesk-do_insert_lead">Inserir lead</label>
                                    <label for="octadesk-do_insert_lead">Para cada lead criado na Xgrow será criado um lead na Octadesk</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="checkbox-modal">
                            <input id="octadesk-on_create_subscriber" type="checkbox" name="events[on_create_subscriber]" value="true" checked>
                            <label for="octadesk-on_create_subscriber" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="octadesk-on_create_subscriber"><strong>Aluno criado</strong></label>
                                <label for="octadesk-on_create_subscriber"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="octadesk-do_insert_client" type="checkbox" name="events[on_create_subscriber][do_insert_client]" value="true" checked>
                                <label for="octadesk-do_insert_client" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="octadesk-do_insert_client">Inserir cliente</label>
                                    <label for="octadesk-do_insert_client">Para cada aluno criado na Xgrow será criado um cliente na Octadesk</label>
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