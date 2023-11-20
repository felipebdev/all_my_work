@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#mailchimp-tags').select2({
                allowClear: true,
                placeholder: 'Tags'
            });
            $('#mailchimp-list').change(function () {
                const listId = $(this).val();
                mailchimpTags(listId);
            });
        });

        async function mailchimpLists() {
            try {   
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/mailchimp/lists`;
                const { 
                    data: { 
                        lists = [] 
                    } 
                } = await axios.get(url);

                $('#mailchimp-list').empty();
                lists.forEach(list => {
                    $('#mailchimp-list').append(new Option(list.name, list.id, false, false));
                });
            } catch (error) {}
        }

        // @todo
        async function mailchimpTags(listId) {
            try {   
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/mailchimp/tags`;
                const { 
                    data: { 
                        tags = [] 
                    } 
                } = await axios.get(`${url}?listId=${listId}`);

                $('#mailchimp-tags').empty();
                tags.forEach(tag => {
                    $('#mailchimp-tags').append(new Option(tag.name, tag.id, false, false));
                });
            } catch (error) {}
        }
    </script>
@endpush

<div id="modal-action-mailchimp" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h3>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::MAILCHIMP }}">

            <div class="d-flex form-check form-switch mb-2">
                <input class="form-check-input me-2" type="checkbox" id="mailchimp-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="mailchimp-is_active">Ativo</label> 
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="mailchimp-description" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="mailchimp-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-products" id="mailchimp-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php $events = \Modules\Integration\Enums\EventEnum::getAllValues(); @endphp
                <select class="xgrow-select" id="mailchimp-event" name="event">
                    @foreach ($events as $event)
                        <option value="{{ $event }}">{{ trans("apps::lang.integrations.events.{$event}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php 
                    $actions = [
                        \Modules\Integration\Enums\ActionEnum::INSERT_CONTACT, 
                        \Modules\Integration\Enums\ActionEnum::REMOVE_CONTACT, 
                    ];
                @endphp
                <select class="xgrow-select" id="mailchimp-action" name="action">
                    @foreach ($actions as $action)
                        <option value="{{ $action }}">{{ trans("apps::lang.integrations.actions.{$action}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="mailchimp-list" name="metadata[list]"></select>
                <label for="type_plan">Lista</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select" id="mailchimp-tags" name="metadata[tags][]" multiple></select>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>