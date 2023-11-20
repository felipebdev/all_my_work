@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#activecampaign-lead_tags').select2({
                allowClear: true,
                tags: true,
                tokenSeparators: [','],
                placeholder: 'Tags da lista de leads'
            });

            $('#activecampaign-subscriber_tags').select2({
                allowClear: true,
                tags: true,
                tokenSeparators: [','],
                placeholder: 'Tags da lista de alunos'
            });

            $('#activecampaign-modal .btn-avancar').on('click', function() {
                getListsFromActiveCampaign();
                setTags();
            });
        });

        async function getListsFromActiveCampaign() {
            const apiUrl = $('#activecampaign-url_webhook').val();
            const apiKey = $('#activecampaign-api_key').val();
            if (apiUrl && apiKey) {
                try {
                    const { data: { lists = [] } } = await axios.get(
                        '/integracao/activecampaign/lists', 
                        { params: { apiUrl, apiKey } }
                    );
                    
                    const leadId = $('#activecampaign-ipt_lead_list_id').val();
                    const subscriberId = $('#activecampaign-ipt_subscriber_list_id').val();

                    let leadOptions = ``;
                    let subscriberOptions = ``;
                    lists.forEach(list => {
                        const leadChecked = (list.id == leadId) ? 'selected' : '';
                        const subscriberChecked = (list.id == subscriberId) ? 'selected' : '';
                        leadOptions += `<option value="${list.id}" ${leadChecked}>${list.name}</option>`;
                        subscriberOptions += `<option value="${list.id}" ${subscriberChecked}>${list.name}</option>`;
                    });

                    $('#activecampaign-lead_list_id').empty().append(leadOptions);
                    $('#activecampaign-subscriber_list_id').empty().append(subscriberOptions);
                }
                catch(error) {
                    console.log(error);
                }
            }
        }

        function setTags() {
            const leadTags = $('#activecampaign-ipt_lead_tags').val();
            const subscriberTags = $('#activecampaign-ipt_subscriber_tags').val();

            if (leadTags) {
                const tags = leadTags.split(',');
                tags.forEach(tag => {
                    const option = new Option(tag, tag, false, true);
                    $('#activecampaign-lead_tags').append(option);
                });
            }

            if (subscriberTags) {
                const tags = subscriberTags.split(',');
                tags.forEach(tag => {
                    const option = new Option(tag, tag, false, true);
                    $('#activecampaign-subscriber_tags').append(option);
                });
            }
        }
    </script>
@endpush

<div id="activecampaign-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/active-campaign.png') }}" alt="">
            <p>O ActiveCampaign é uma plataforma para automação da experiência do cliente, que combina as categorias de email marketing, automação de marketing, automação de vendas e CRM.</p>
            <a href="https://help.activecampaign.com/hc/pt-br/articles/207317590-Introdu%C3%A7%C3%A3o-%C3%A0-API" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="8">
            <input type="hidden" name="id_webhook" value="8">
            <input id="activecampaign-ipt_lead_list_id" type="hidden">
            <input id="activecampaign-ipt_subscriber_list_id" type="hidden">
            <input id="activecampaign-ipt_lead_tags" type="hidden">
            <input id="activecampaign-ipt_subscriber_tags" type="hidden">
            
            <div class="column-first ">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="activecampaign-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="activecampaign-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="activecampaign-url_webhook" name="url_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="activecampaign-url_webhook">Url da api activecampaign</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="activecampaign-api_key" name="activecampaign_api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="activecampaign-api_key">Chave da api activecampaign</label>
                    </div>
                    <div class="d-flex form-check form-switch mb-3">
                        <input class="form-check-input me-2" type="checkbox" id="activecampaign-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="activecampaign-flag_enable">Ativo</label> 
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
                    <div class="mt-2 mb-3">
                        <div class="checkbox-modal">
                            <input id="activecampaign-on_create_lead" type="checkbox" name="events[on_create_lead]" value="true" checked>
                            <label for="activecampaign-on_create_lead" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="activecampaign-on_create_lead"><strong>Lead criado</strong></label>
                                <label for="activecampaign-on_create_lead"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="activecampaign-do_insert_lead" type="checkbox" name="events[on_create_lead][do_insert_lead]" value="true" checked>
                                <label for="activecampaign-do_insert_lead" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="activecampaign-do_insert_lead">Inserir contato</label>
                                    <label for="activecampaign-do_insert_lead">Para cada lead criado na Xgrow será criado um contato na ActiveCampaign</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                        <select id="activecampaign-lead_list_id" class="xgrow-select w-100" name="events[on_create_lead][do_insert_lead][lead_list_id]"></select>
                                        <label for="activecampaign-lead_list_id">Lista de leads</label>    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="xgrow-form-control">
                                        <select id="activecampaign-lead_tags" class="xgrow-select w-100" name="events[on_create_lead][do_insert_lead][lead_tags][]" multiple></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="checkbox-modal">
                            <input id="activecampaign-on_create_subscriber" type="checkbox" name="events[on_create_subscriber]" value="true" checked>
                            <label for="activecampaign-on_create_subscriber" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="activecampaign-on_create_subscriber"><strong>Aluno criado</strong></label>
                                <label for="activecampaign-on_create_subscriber"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="activecampaign-do_insert_subscriber" type="checkbox" name="events[on_create_subscriber][do_insert_subscriber]" value="true" checked>
                                <label for="activecampaign-do_insert_subscriber" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="activecampaign-do_insert_subscriber">Inserir contato</label>
                                    <label for="activecampaign-do_insert_subscriber">Para cada aluno criado na Xgrow será criado um contato na ActiveCampaign</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                        <select id="activecampaign-subscriber_list_id" class="xgrow-select w-100" name="events[on_create_subscriber][do_insert_subscriber][subscriber_list_id]"></select>
                                        <label for="activecampaign-subscriber_list_id">Lista de alunos</label>    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="xgrow-form-control">
                                        <select id="activecampaign-subscriber_tags" class="xgrow-select w-100" name="events[on_create_subscriber][do_insert_subscriber][subscriber_tags][]" multiple></select>
                                    </div>
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