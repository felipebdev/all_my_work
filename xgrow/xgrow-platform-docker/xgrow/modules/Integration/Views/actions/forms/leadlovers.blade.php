@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#leadlovers-days_never_accessed').val('').prop('required', false).hide();
            $('#leadlovers-event').change(function () {
                const selectedEvent = $(this).val();
                selectedEvent === 'onNeverAccessed' ?
                    $('#leadlovers-days_never_accessed').prop('required', true).show() :
                    $('#leadlovers-days_never_accessed').val('').prop('required', false).hide();
            });
        })

        let machineCode;
        let emailSequenceCode;
        $(document).ready(function() {
            $('#leadlovers-tags').select2({
                allowClear: true,
                placeholder: 'Tags'
            });
            $('#leadlovers-machine').change(function () {
                $('#leadlovers-emailSequence').empty().prop('disabled', true).append(new Option("Carregando Sequências de Email...", "1", false, false));
                machineCode = $(this).val();
                leadloversEmailSequences(machineCode);
            });
            $('#leadlovers-emailSequence').change(function () {
                emailSequenceCode = $(this).val();
                leadloversLevels(machineCode, emailSequenceCode);
            });
        });

        async function leadloversMachines() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/leadlovers/machines`;
                const {
                    data: {
                        Items: machines = []
                    }
                } = await axios.get(url);

                $('#leadlovers-machine').empty();
                machines.forEach(machine => {
                    $('#leadlovers-machine').append(new Option(machine.MachineName, machine.MachineCode, false, false));
                });

                $('#leadlovers-emailSequence').trigger('change');
            } catch (error) {}
        }

        async function leadloversEmailSequences(machineCode) {
            $('#leadlovers-level').empty().prop('disabled', true).append(new Option("Carregando Níveis de Funil...", "1", false, false));
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/leadlovers/emailSequences`;
                const {
                    data: {
                        Items: emailSequences = []
                    }
                } = await axios.get(`${url}?machineCode=${machineCode}`);

                $('#leadlovers-emailSequence').empty().prop('disabled', false);
                emailSequences.forEach(emailSequence => {
                    $('#leadlovers-emailSequence').append(new Option(emailSequence.SequenceName, emailSequence.SequenceCode, false, false));
                });

                $('#leadlovers-emailSequence').trigger('change');
            } catch (error) {}
        }

        async function leadloversLevels(machineCode, emailSequenceCode) {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/leadlovers/levels`;
                const {
                    data: {
                        Items: levels = []
                    }
                } = await axios.get(`${url}?machineCode=${machineCode}&emailSequenceCode=${emailSequenceCode}`);

                $('#leadlovers-level').empty().prop('disabled', false);
                levels.forEach(level => {
                    $('#leadlovers-level').append(new Option(level.Subject, level.Sequence, false, false));
                });

            } catch (error) {}
        }

        async function leadloversTags() {
            try {
                const integration = @json($integration);
                const url = `/apps/integrations/${integration.id}/leadlovers/tags`;
                const {
                    data: {
                        Tags: tags = []
                    }
                } = await axios.get(url);

                $('#leadlovers-tags').empty();
                tags.forEach(tag => {
                    $('#leadlovers-tags').append(new Option(tag.Title, tag.Id, false, false));
                });
            } catch (error) {}
        }
    </script>
@endpush

<div id="modal-action-leadlovers" class="modal-integration modal-integration-two-items action-form">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Configurar ação</h3>
        </div>

        <form action="{{ route('apps.integrations.actions.store', ['integration' => $integration->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ Modules\Integration\Enums\TypeEnum::LEADLOVERS }}">

            <div class="d-flex form-check form-switch mb-2">
                <input class="form-check-input me-2" type="checkbox" id="leadlovers-is_active" name="is_active" value="1" checked="">
                <label class="text-white" for="leadlovers-is_active">Ativo</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input required="" id="leadlovers-description" name="description" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="leadlovers-description">Nome da ação</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-products" id="leadlovers-products"
                        onChange="changeProduct('leadlovers')"
                        multiple></select>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select slc-plans" id="leadlovers-plans" name="plans[]" multiple></select>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php $events = \Modules\Integration\Enums\EventEnum::getAllValues(); @endphp
                <select class="xgrow-select" id="leadlovers-event" name="event">
                    @foreach ($events as $event)
                        <option value="{{ $event }}">{{ trans("apps::lang.integrations.events.{$event}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Quando ocorrer (evento)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <input id="leadlovers-days_never_accessed" name="metadata[days_never_accessed]" type="number" min="1"
                    class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="leadlovers-days_never_accessed">Dias sem acessar</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                @php
                    $actions = [
                        \Modules\Integration\Enums\ActionEnum::INSERT_CONTACT,
                        \Modules\Integration\Enums\ActionEnum::REMOVE_CONTACT,
                    ];
                @endphp
                <select class="xgrow-select" id="leadlovers-action" name="action">
                    @foreach ($actions as $action)
                        <option value="{{ $action }}">{{ trans("apps::lang.integrations.actions.{$action}") }}</option>
                    @endforeach
                </select>
                <label for="type_plan">Faça (ação)</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="leadlovers-machine" name="metadata[machineCode]"></select>
                <label for="type_plan">Máquina</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="leadlovers-emailSequence" name="metadata[sequenceCode]"></select>
                <label for="type_plan">Sequência de Email</label>
            </div>
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                <select class="xgrow-select" id="leadlovers-level" name="metadata[levelCode]"></select>
                <label for="type_plan">Nível</label>
            </div>
            <div class="xgrow-form-control mb-2">
                <select class="xgrow-select" id="leadlovers-tags" name="metadata[tags][]" multiple></select>
            </div>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Salvar</button>
            </div>
        </form>
    </div>
</div>
