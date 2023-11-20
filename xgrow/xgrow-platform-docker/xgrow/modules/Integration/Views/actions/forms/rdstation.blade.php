@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#rdstation-days_never_accessed').val('').prop('required', false).hide();
            $('#rdstation-event').change(function () {
                const selectedEvent = $(this).val();
                selectedEvent === 'onNeverAccessed' ?
                    $('#rdstation-days_never_accessed').prop('required', true).show() :
                    $('#rdstation-days_never_accessed').val('').prop('required', false).hide();
            });
        });
    </script>
@endpush

<div id="modal-action-rdstation" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h5>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}"
              method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::RDSTATION }}">

            <div class="d-flex form-check form-switch mb-3">
                <input class="form-check-input me-2" type="checkbox" id="rdstation-is_active" name="is_active" value="1"
                       checked="">
                <label class="text-white" for="rdstation-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                <input required="" id="rdstation-description" name="description" type="text"
                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="rdstation-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-3">
                <select class="xgrow-select slc-products" id="rdstation-products"
                        onChange="changeProduct('rdstation')"
                        multiple></select>
            </div>
            <div class="xgrow-form-control mb-3">
                <select class="xgrow-select slc-plans" id="rdstation-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="rdstation-event" name="event">
                    @foreach (\Modules\Integration\Enums\EventEnum::getAllValues() as $event)
                        <option value="{{ $event }}">{{ trans("apps::lang.integrations.events.{$event}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="rdstation-days_never_accessed" name="days_never_accessed" type="number" min="1"
                    class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="rdstation-days_never_accessed">Dias sem acessar</label>
            </div>

            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                @php $actions = \Modules\Integration\Enums\ActionEnum::returnActionsByCategory('member-area'); @endphp
                <select class="xgrow-select" id="memberkit-action" name="action">
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
