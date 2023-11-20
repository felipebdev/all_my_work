@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#hubspot-days_never_accessed').val('').prop('required', false).hide();
            $('#hubspot-event').change(function () {
                const selectedEvent = $(this).val();
                selectedEvent === 'onNeverAccessed' ?
                    $('#hubspot-days_never_accessed').prop('required', true).show() :
                    $('#hubspot-days_never_accessed').val('').prop('required', false).hide();
            });
        })

        async function hubspotLists() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/hubspot/lists`;
                const {
                    data: {
                        lists = []
                    }
                } = await axios.get(url);

                $('#hubspot-list').empty();
                lists.forEach(list => {
                    $('#hubspot-list').append(new Option(list.name, list.listId, false, false));
                });
            } catch (error) {
            }
        }
    </script>
@endpush

<div id="modal-action-hubspot" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h5>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}"
              method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::HUBSPOT }}">

            <div class="d-flex form-check form-switch mb-2">
                <input class="form-check-input me-2" type="checkbox" id="hubspot-is_active" name="is_active" value="1"
                       checked="">
                <label class="text-white" for="hubspot-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="hubspot-description" name="description" type="text"
                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="hubspot-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-products" id="hubspot-products"
                        onChange="changeProduct('hubspot')"
                        multiple></select>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-plans" id="hubspot-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="hubspot-event" name="event">
                    @foreach (\Modules\Integration\Enums\EventEnum::getAllValues() as $event)
                        <option value="{{ $event }}">{{ trans("apps::lang.integrations.events.{$event}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="hubspot-days_never_accessed" name="metadata[days_never_accessed]" type="number" min="1"
                    class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="hubspot-days_never_accessed">Dias sem acessar</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php
                    $actions = [
                        \Modules\Integration\Enums\ActionEnum::INSERT_CONTACT,
                        \Modules\Integration\Enums\ActionEnum::REMOVE_CONTACT,
                    ];
                @endphp
                <select class="xgrow-select" id="hubspot-action" name="action">
                    @foreach ($actions as $action)
                        <option value="{{ $action }}">{{ trans("apps::lang.integrations.actions.{$action}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="hubspot-list" name="metadata[list]"></select>
                <label for="type_plan">Lista</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
