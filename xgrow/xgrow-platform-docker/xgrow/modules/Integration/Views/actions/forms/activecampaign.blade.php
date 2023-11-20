@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#activecampaign-days_never_accessed').val('').prop('required', false).hide();
            $('#activecampaign-event').change(function() {
                const selectedEvent = $(this).val();
                selectedEvent === 'onNeverAccessed' ?
                    $('#activecampaign-days_never_accessed').prop('required', true).show() :
                    $('#activecampaign-days_never_accessed').val('').prop('required', false).hide();
            });

            $('#activecampaign-list').select2({
                allowClear: true,
                placeholder: 'Na lista',
                maximumSelectionLength: 1,
                language: {
                    maximumSelected: function() {
                        return "Você só pode selecionar 1 lista";
                    }
                }
            });

            $('#activecampaign-tags').select2({
                allowClear: true,
                placeholder: 'Com as tags'
            });
        });

        async function activecampaignLists() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/activecampaign/lists`;
                const {
                    data: {
                        lists = []
                    }
                } = await axios.get(url);

                $('#activecampaign-list').empty();
                lists.forEach(list => {
                    $('#activecampaign-list').append(new Option(list.name, list.id, false, false));
                });
            } catch (error) {}
        }

        async function activecampaignTags() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/activecampaign/tags`;
                const {
                    data: {
                        tags = []
                    }
                } = await axios.get(url);

                $('#activecampaign-tags').empty();
                tags.forEach(tag => {
                    $('#activecampaign-tags').append(new Option(tag.tag, tag.id, false, false));
                });
            } catch (error) {}
        }

        async function activecampaignChangeCardField() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/activecampaign/customFields`;
                const {
                    data: {
                        fields = []
                    }
                } = await axios.get(url);
                $('#activecampaign-change_card_field').empty();
                fields.forEach(field => {
                    $('#activecampaign-change_card_field').append(new Option(field.title, field.id, false,
                        false));
                });
            } catch (error) {}
        }

        function toggleCardField(val) {
            const divId = '#div-change_card_field'
            val.value === 'onRefusePayment' ? $(divId).show() : $(divId).hide();
        }
    </script>
@endpush

<div id="modal-action-activecampaign" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h3>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::ACTIVECAMPAIGN }}">

            <div class="d-flex form-check form-switch mb-2">
                <input class="form-check-input me-2" type="checkbox" id="activecampaign-is_active" name="is_active"
                    value="1" checked="">
                <label class="text-white" for="activecampaign-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="activecampaign-description" name="description" type="text"
                    class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="activecampaign-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-products" id="activecampaign-products"
                    onChange="changeProduct('activecampaign')" multiple></select>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-plans" id="activecampaign-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php $events = \Modules\Integration\Enums\EventEnum::getAllValues(); @endphp
                <select class="xgrow-select" id="activecampaign-event" name="event" onchange="toggleCardField(this)">
                    @foreach ($events as $event)
                        <option value="{{ $event }}">{{ trans("apps::lang.integrations.events.{$event}") }}
                        </option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="activecampaign-days_never_accessed" name="metadata[days_never_accessed]" type="number"
                    min="1" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="activecampaign-days_never_accessed">Dias sem acessar</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php
                    $actions = [\Modules\Integration\Enums\ActionEnum::INSERT_CONTACT, \Modules\Integration\Enums\ActionEnum::REMOVE_CONTACT];
                @endphp
                <select class="xgrow-select" id="activecampaign-action" name="action">
                    @foreach ($actions as $action)
                        <option value="{{ $action }}">{{ trans("apps::lang.integrations.actions.{$action}") }}
                        </option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select" id="activecampaign-list" name="metadata[list]" multiple></select>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select" id="activecampaign-tags" name="metadata[tags][]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2" id="div-change_card_field"
                style="display: none">
                <select class="xgrow-select" id="activecampaign-change_card_field"
                    name="metadata[change_card_field]"></select>
                <label for="activecampaign-change_card_field">Link de troca de cartão (Renovação de assinatura
                    recusada)</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
