@push('after-scripts')
{{--    <script>--}}
{{--        $(document).ready(function() {--}}
{{--            $('#builderall-lead_tags').select2({--}}
{{--                allowClear: true,--}}
{{--                tags: true,--}}
{{--                tokenSeparators: [','],--}}
{{--                placeholder: 'Tags da lista de leads'--}}
{{--            });--}}

{{--            $('#builderall-subscriber_tags').select2({--}}
{{--                allowClear: true,--}}
{{--                tags: true,--}}
{{--                tokenSeparators: [','],--}}
{{--                placeholder: 'Tags da lista de alunos'--}}
{{--            });--}}

{{--            $('#builderall-modal .btn-avancar').on('click', function() {--}}
{{--                getListsFromActiveCampaign();--}}
{{--                setTags();--}}
{{--            });--}}
{{--        });--}}

{{--        async function getListsFromActiveCampaign() {--}}
{{--            const apiUrl = $('#builderall-url_webhook').val();--}}
{{--            const apiKey = $('#builderall-api_key').val();--}}
{{--            if (apiUrl && apiKey) {--}}
{{--                try {--}}
{{--                    const { data: { lists = [] } } = await axios.get(--}}
{{--                        '/integracao/builderall/lists',--}}
{{--                        { params: { apiUrl, apiKey } }--}}
{{--                    );--}}

{{--                    const leadId = $('#builderall-ipt_lead_list_id').val();--}}
{{--                    const subscriberId = $('#builderall-ipt_subscriber_list_id').val();--}}

{{--                    let leadOptions = ``;--}}
{{--                    let subscriberOptions = ``;--}}
{{--                    lists.forEach(list => {--}}
{{--                        const leadChecked = (list.id == leadId) ? 'selected' : '';--}}
{{--                        const subscriberChecked = (list.id == subscriberId) ? 'selected' : '';--}}
{{--                        leadOptions += `<option value="${list.id}" ${leadChecked}>${list.name}</option>`;--}}
{{--                        subscriberOptions += `<option value="${list.id}" ${subscriberChecked}>${list.name}</option>`;--}}
{{--                    });--}}

{{--                    $('#builderall-lead_list_id').empty().append(leadOptions);--}}
{{--                    $('#builderall-subscriber_list_id').empty().append(subscriberOptions);--}}
{{--                }--}}
{{--                catch(error) {--}}
{{--                    console.log(error);--}}
{{--                }--}}
{{--            }--}}
{{--        }--}}

{{--        function setTags() {--}}
{{--            const leadTags = $('#builderall-ipt_lead_tags').val();--}}
{{--            const subscriberTags = $('#builderall-ipt_subscriber_tags').val();--}}

{{--            if (leadTags) {--}}
{{--                const tags = leadTags.split(',');--}}
{{--                tags.forEach(tag => {--}}
{{--                    const option = new Option(tag, tag, false, true);--}}
{{--                    $('#builderall-lead_tags').append(option);--}}
{{--                });--}}
{{--            }--}}

{{--            if (subscriberTags) {--}}
{{--                const tags = subscriberTags.split(',');--}}
{{--                tags.forEach(tag => {--}}
{{--                    const option = new Option(tag, tag, false, true);--}}
{{--                    $('#builderall-subscriber_tags').append(option);--}}
{{--                });--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
@endpush

<div id="modal-builderall" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <div class="mb-3 text-center">
                <img class="w-75" src="{{ asset('xgrow-vendor/assets/img/integrations/builderall.png') }}" alt="Ícone claro do BuilderAll">
            </div>
            <p>Crie Negócios, Conquiste Clientes, Automatize Processos, Divulgue e Venda muito mais com a Plataforma Líder de Marketing Digital.
                <a href="https://oficial.buillderall.com.br/#" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ route('apps.integrations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="code" value="{{ Modules\Integration\Enums\CodeEnum::BUILDERALL }}">
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::BUILDERALL }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="builderall-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="builderall-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="builderall-description_integration" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="builderall-description_integration">Nome da integração</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="builderall-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="builderall-api_key">Chave da api BuilderAll/MailingBoss</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
