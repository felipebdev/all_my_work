@extends('templates.xgrow.main')

@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/integret_add.css') }}">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/new-integrations.css') }}">

    <style>
        .x-dropdown {
            position: unset !important;
        }
        .select2-dropdown {
            z-index: 99999;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>

    @include('apps::actions.includes.scripts')
    <script>
        let allPlans = [];
        $(document).ready(function () {
            const integration = @json($integration);
            getPlans();
            getMetadata(integration.type);

            $('.btn-open-modal').click(function (e) {
                e.preventDefault();
                $('input[type=text]').not('.ipt-not-clear').val('').removeClass('mui--is-not-empty');
                $('select').not('.ipt-not-clear').val('');

                const provider = $(this).attr('data-provider');
                const modal = $(`#modal-action-${provider}`);
                const slcProducts = modal.find('.slc-products');
                slcProducts.empty();

                let options = [];
                allPlans.forEach(plan => {
                    slcProducts.append(new Option(plan.name, plan.id, false, false));
                });

                modal.addClass('active');
            });

            $('.btn-close-modal').click(function (e) {
                e.preventDefault();
                $(this).closest('.modal-integration').removeClass('active');
            });

            $('.slc-products').select2({
                allowClear: true,
                placeholder: 'Produtos'
            });

            $('.btn-action-edit').on('click', function (e) {
                const url = $(this).data('url');
                const provider = $(this).data('provider');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        const { plans = [] } = data;
                        const modal = $(`#modal-action-${provider}`);
                        const slcProducts = modal.find('.slc-products');
                        slcProducts.empty();

                        allPlans.forEach(plan => {
                            let selected = false;
                            if (plans.includes(plan.id)) selected = true;
                            slcProducts.append(new Option(plan.name, plan.id, false, selected));
                        });

                        integrations[provider](data); //apps::actions.includes.scripts
                        modal.addClass('active');
                    },
                    error: function (data) {
                        errorToast('Algum erro aconteceu!',
                            `Veja mais em: ${data.responseJSON.message}`);
                    }
                });
            });

            $('#modal-integration-delete').on('show.bs.modal', function (e) {
                const url = $(e.relatedTarget).data('url');
                $('#frm-modal-delete').attr('action', url);
            });
        });

        async function getPlans() {
            try {
                const {
                    data: { plans = [] } = {}
                } = await axios.get('/api/plans/get-all');
                allPlans = plans;
            } catch (error) {}
        }

        /**
         * Get provider metadata (lists, tags, etc)
         */
        function getMetadata(provider) {
            if (provider in metadataApps) metadataApps[provider](); //apps::actions.includes.scripts
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/">Início</a>
            </li>
            <li class="breadcrumb-item ms-2">
                <a href="{{ route('apps.integrations.index') }}">Integrações</a>
            </li>
            <li class="breadcrumb-item ms-2"><span>{{ Str::ucfirst($integration->type) }}</span></li>
            <li class="breadcrumb-item active ms-2"><span>Ações</span></li>
        </ol>
    </nav>

    @if ($integration->type === Modules\Integration\Enums\TypeEnum::WEBHOOK)
        <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
            <a class="xgrow-tab-item nav-item nav-link active" id="nav-actions-tab"
                data-bs-toggle="tab" href="#nav-actions" role="tab" aria-controls="nav-actions"
                aria-selected="true">
                Ações
            </a>
            <a class="xgrow-tab-item nav-item nav-link"
                id="nav-logs-tab" data-bs-toggle="tab" href="#nav-logs" role="tab" aria-controls="nav-logs"
                aria-selected="false">
                Histórico
            </a>
            <a class="xgrow-tab-item nav-item nav-link"
                href="https://xgrow-docs.vercel.app/docs/integrations/Webhooks" target="_blank"
                aria-selected="false">
                Documentação
            </a>
        </div>
    @endif

    <div class="tab-content py-3" id="nav-tabContent">
        @include('elements.alert')

        <div class="tab-pane fade show active" id="nav-actions"
            role="tabpanel" aria-labelledby="nav-actions-tab">
            @include('apps::actions.includes.tab-actions', ['integration' => $integration, 'actions' => $actions])
        </div>

        @if ($integration->type === Modules\Integration\Enums\TypeEnum::WEBHOOK)
            <div class="tab-pane fade show" id="nav-logs"
                role="tabpanel" aria-labelledby="nav-logs-tab">
                @include('apps::actions.includes.tab-logs', ['integration' => $integration])
            </div>
        @endif
    </div>

{{-- Form new action by integration --}}
@include('apps::actions.forms.activecampaign')
@include('apps::actions.forms.cademi')
@include('apps::actions.forms.infusion')
@include('apps::actions.forms.kajabi')
@include('apps::actions.forms.mailchimp')
@include('apps::actions.forms.octadesk')
@include('apps::actions.forms.smartnotas')
@include('apps::actions.forms.webhook')

@include('apps::actions.includes.delete')
@include('apps::actions.modals.logs')

@include('elements.toast')

@endsection
