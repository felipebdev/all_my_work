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

<div id="modal-activecampaign" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img src="{{ asset('xgrow-vendor/assets/img/active-campaign.png') }}" alt="">
            <p>
                O ActiveCampaign é uma plataforma para automação da experiência do cliente, que combina as categorias
                de email marketing, automação de marketing, automação de vendas e CRM.
            </p>
            <a href="https://help.activecampaign.com/hc/pt-br/articles/207317590-Introdu%C3%A7%C3%A3o-%C3%A0-API"
                target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::ACTIVECAMPAIGN }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::ACTIVECAMPAIGN }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="activecampaign-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="activecampaign-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="activecampaign-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="activecampaign-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="activecampaign-api_webhook" name="api_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="activecampaign-api_webhook">Url da api activecampaign</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="activecampaign-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="activecampaign-api_key">Chave da api activecampaign</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>