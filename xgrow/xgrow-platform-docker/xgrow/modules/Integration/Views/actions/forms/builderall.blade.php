@push('after-scripts')
    <script>
        $(document).ready(function () {
            $('#builderall-days_never_accessed').val('').prop('required', false).hide();
            $('#builderall-event').change(function () {
                const selectedEvent = $(this).val();
                selectedEvent === 'onNeverAccessed' ?
                    $('#builderall-days_never_accessed').prop('required', true).show() :
                    $('#builderall-days_never_accessed').val('').prop('required', false).hide();
            });

            $('#builderall-list').select2({
                allowClear: true,
                placeholder: 'Na lista',
                maximumSelectionLength: 1,
                language: {
                    maximumSelected: function () {
                        return "Você só pode selecionar 1 lista";
                    }
                }
            });

            $('#builderall-tags').select2({
                allowClear: true,
                placeholder: 'Com as tags'
            });
        });

        async function builderallLists() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/builderall/lists`;
                const {
                    data: {
                        lists = []
                    }
                } = await axios.get(url);

                $('#builderall-list').empty();
                lists.forEach(list => {
                    $('#builderall-list').append(new Option(list.name, list.id, false, false));
                });
            } catch (error) {
            }
        }
    </script>
@endpush

<div id="modal-action-builderall" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h5>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}"
              method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::BUILDERALL }}">

            <div class="d-flex form-check form-switch mb-2">
                <input class="form-check-input me-2" type="checkbox" id="builderall-is_active" name="is_active"
                       value="1" checked="">
                <label class="text-white" for="builderall-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="builderall-description" name="description" type="text"
                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="builderall-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-products" id="builderall-products"
                        onChange="changeProduct('builderall')" multiple></select>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-plans" id="builderall-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php $events = \Modules\Integration\Enums\EventEnum::getAllValues(); @endphp
                <select class="xgrow-select" id="builderall-event" name="event">
                    @foreach ($events as $event)
                        <option value="{{ $event }}">{{ trans("apps::lang.integrations.events.{$event}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="builderall-days_never_accessed" name="metadata[days_never_accessed]" type="number" min="1"
                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="builderall-days_never_accessed">Dias sem acessar</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php
                    $actions = [
                        \Modules\Integration\Enums\ActionEnum::INSERT_CONTACT,
                        \Modules\Integration\Enums\ActionEnum::REMOVE_CONTACT,
                    ];
                @endphp
                <select class="xgrow-select" id="builderall-action" name="action">
                    @foreach ($actions as $action)
                        <option value="{{ $action }}">{{ trans("apps::lang.integrations.actions.{$action}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>

            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select" id="builderall-list" name="metadata[list]" multiple></select>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
