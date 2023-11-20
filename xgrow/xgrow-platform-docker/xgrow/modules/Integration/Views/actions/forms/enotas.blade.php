@push('after-scripts')
    <script>
        $(document).ready(function () {
            $("#enotas-days_never_accessed").val("").prop("required", false).hide();
            $("#enotas-event").change(function () {
                const selectedEvent = $(this).val();
                selectedEvent === "onNeverAccessed" ?
                    $("#enotas-days_never_accessed").prop("required", true).show() :
                    $("#enotas-days_never_accessed").val("").prop("required", false).hide();
            });
        });
    </script>
@endpush

<div id="modal-action-enotas" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h5>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}"
              method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::ENOTAS }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="enotas-is_active" name="is_active" value="1"
                       checked="">
                <label class="text-white" for="enotas-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="enotas-description" name="description" type="text"
                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="enotas-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-3">
                <select class="xgrow-select slc-products" id="enotas-products"
                        onChange="changeProduct('enotas')"
                        multiple></select>
            </div>
            <div class="xgrow-form-control mb-3">
                <select class="xgrow-select slc-plans" id="enotas-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                @php $events = \Modules\Integration\Enums\EventEnum::returnEventsByCategory('notes'); @endphp
                <select class="xgrow-select" id="enotas-event" name="event">
                    @foreach ($events as $ev)
                        <option value="{{ $ev['event'] }}">{{$ev['name']}}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input id="enotas-days_never_accessed" name="metadata[days_never_accessed]" type="number" min="1"
                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="enotas-days_never_accessed">Dias sem acessar</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                @php $actions = \Modules\Integration\Enums\ActionEnum::returnActionsByCategory('notes'); @endphp
                <select class="xgrow-select" id="enotas-action" name="action">
                    @foreach ($actions as $ac)
                        <option value="{{ $ac['action'] }}">{{ $ac['name'] }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>
            <div class="footer-modal p-0 mt-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
