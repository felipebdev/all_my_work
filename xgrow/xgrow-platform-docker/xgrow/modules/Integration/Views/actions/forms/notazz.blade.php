@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#notazz-days_never_accessed').val('').prop('required', false).hide();
            $('#notazz-event').change(function() {
                const selectedEvent = $(this).val();
                selectedEvent === 'onNeverAccessed' ?
                    $('#notazz-days_never_accessed').prop('required', true).show() :
                    $('#notazz-days_never_accessed').val('').prop('required', false).hide();
            });
        })
    </script>
@endpush

<div id="modal-action-notazz" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h3>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::NOTAZZ }}">

            <div class="d-flex form-check form-switch mb-2">
                <input class="form-check-input me-2" type="checkbox" id="notazz-is_active" name="is_active"
                    value="1" checked="">
                <label class="text-white" for="notazz-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="notazz-description" name="description" type="text"
                    class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="notazz-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-products" id="notazz-products" name="products[]" required
                    onChange="changeProduct('notazz')" multiple></select>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-plans" id="notazz-plans" name="plans[]" multiple required></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php $events = \Modules\Integration\Enums\EventEnum::returnEventsByCategory('notes'); @endphp
                <select class="xgrow-select" id="notazz-event" name="event" required>
                    @foreach ($events as $ev)
                        <option value="{{ $ev['event'] }}">{{ $ev['name'] }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="notazz-days_never_accessed" name="metadata[days_never_accessed]" type="number"
                    min="1" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="notazz-days_never_accessed">Dias sem acessar</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php $actions = \Modules\Integration\Enums\ActionEnum::returnActionsByCategory('notes'); @endphp
                <select class="xgrow-select" id="notazz-action" name="action" required>
                    @foreach ($actions as $ac)
                        <option value="{{ $ac['action'] }}">{{ $ac['name'] }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
