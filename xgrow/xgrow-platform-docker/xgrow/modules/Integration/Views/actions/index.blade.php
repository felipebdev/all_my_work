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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>

    @include('apps::actions.includes.scripts')
    <script>
        let allProducts = [];
        const integration = @json($integration);

        $(document).ready(function() {

            getProducts();

            getMetadata(integration.type);

            $('.btn-open-modal').click(function(e) {
                e.preventDefault();
                $('input[type=text]').not('.ipt-not-clear').val('').removeClass('mui--is-not-empty');
                $('select').not('.ipt-not-clear').val('');
                const provider = $(this).data('provider');
                const modal = $(`#modal-action-${provider}`);
                const slcProducts = modal.find('.slc-products');
                const slcPlans = modal.find('.slc-plans');
                setOptions(slcProducts, allProducts, []);
                setOptions(slcPlans, [], []);
                modal.addClass('active');
            });

            $('.btn-close-modal').click(function(e) {
                e.preventDefault();
                $(this).closest('.modal-integration').removeClass('active');
            });

            $('.slc-plans').select2({
                allowClear: true,
                placeholder: 'Planos'
            });

            $('.slc-products').select2({
                allowClear: true,
                placeholder: 'Produtos'
            });

            $('.btn-action-edit').on('click', async function(e) {
                try {
                    const url = $(this).data('url');
                    const provider = $(this).data('provider');
                    //$('#action_id').val($(this).data('action_id'));
                    await setProducts(url, provider);
                    const products = getProductsSelected(provider);
                    const {
                        data: {
                            plans: allPlans
                        }
                    } = await axios.post('/api/plans/get-plans-by-product', {
                        products
                    });
                    const {
                        data,
                        data: {
                            plans
                        }
                    } = await axios.get(url, {
                        params: {
                            "_token": "{{ csrf_token() }}"
                        }
                    });
                    const modal = $(`#modal-action-${provider}`);
                    setPlans(modal, allPlans, plans)
                    integrations[provider](data); //apps::actions.includes.scripts
                    modal.addClass('active');
                } catch (error) {
                    errorToast('Algum erro aconteceu!', error);
                }
            });

            $('#modal-integration-delete').on('show.bs.modal', function(e) {
                const url = $(e.relatedTarget).data('url');
                $('#frm-modal-delete').attr('action', url);
            });

        });

        async function changeProduct(provider) {
            const plans = getPlansSelected(provider);
            const products = getProductsSelected(provider);
            const {
                data: {
                    plans: allPlans
                }
            } = await axios.post('/api/plans/get-plans-by-product', {
                products: [...products]
            });
            const modal = $(`#modal-action-${provider}`);
            setPlans(modal, allPlans, plans);
        }

        async function setProducts(url, provider) {
            try {
                const {
                    data: {
                        products
                    }
                } = await axios.get(url, {
                    params: {
                        "_token": "{{ csrf_token() }}"
                    }
                });

                const modal = $(`#modal-action-${provider}`);
                const slcProducts = modal.find('.slc-products');
                setOptions(slcProducts, allProducts, products);
            } catch (error) {
                errorToast('Algum erro aconteceu!', error);
            }
        }

        function setPlans(modal, allPlans, plans) {
            const slcPlans = modal.find('.slc-plans');
            setOptions(slcPlans, allPlans, [...plans]);
        }

        function getProductsSelected(provider) {
            const products = $(`#${provider}-products option:selected`)
            return products.map((_, option) => option.value);
        }

        function getPlansSelected(provider) {
            const plans = $(`#${provider}-plans option:selected`)
            return plans.map((_, option) => option.value);
        }

        function setOptions(element, all, options) {
            element.empty();
            all.forEach(items => {
                let selected = false;
                if (options.some(option => option == items.id)) selected = true;
                element.append(new Option(items.name, items.id, false, selected));
            });
        }

        async function getPlans() {
            try {
                const {
                    data: {
                        plans = []
                    } = {}
                } = await axios.get('/api/plans/get-all');
                allPlans = plans;
            } catch (error) {}
        }

        async function getProducts() {
            try {
                const {
                    data: {
                        products = []
                    } = {}
                } = await axios.get('/products/list');
                allProducts = products;
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
            <a class="xgrow-tab-item nav-item nav-link active" id="nav-actions-tab" data-bs-toggle="tab" href="#nav-actions"
                role="tab" aria-controls="nav-actions" aria-selected="true">
                Ações
            </a>
            <a class="xgrow-tab-item nav-item nav-link" id="nav-logs-tab" data-bs-toggle="tab" href="#nav-logs"
                role="tab" aria-controls="nav-logs" aria-selected="false">
                Histórico
            </a>
            <a class="xgrow-tab-item nav-item nav-link" href="https://integracoes.xgrow.com/docs/webhook" target="_blank"
                aria-selected="false">
                Documentação
            </a>
        </div>
    @endif

    <div class="tab-content py-3" id="nav-tabContent">
        @include('elements.alert')

        <div class="tab-pane fade show active" id="nav-actions" role="tabpanel" aria-labelledby="nav-actions-tab">
            @include('apps::actions.includes.tab-actions', [
                'integration' => $integration,
                'actions' => $actions,
            ])
        </div>

        @if ($integration->type === Modules\Integration\Enums\TypeEnum::WEBHOOK)
            <div class="tab-pane fade show" id="nav-logs" role="tabpanel" aria-labelledby="nav-logs-tab">
                @include('apps::actions.includes.tab-logs', ['integration' => $integration])
            </div>
        @endif
    </div>

    {{-- Form new action by integration --}}
    @include('apps::actions.forms.activecampaign')
    @include('apps::actions.forms.cademi')
    @include('apps::actions.forms.hubspot')
    @include('apps::actions.forms.infusion')
    @include('apps::actions.forms.kajabi')
    @include('apps::actions.forms.mailchimp')
    @include('apps::actions.forms.octadesk')
    @include('apps::actions.forms.rdstation')
    @include('apps::actions.forms.smartnotas')
    @include('apps::actions.forms.wisenotas')
    @include('apps::actions.forms.webhook')
    @include('apps::actions.forms.pipedrive')
    @include('apps::actions.forms.leadlovers')
    @include('apps::actions.forms.mautic')
    @include('apps::actions.forms.voxuy')
    @include('apps::actions.forms.enotas')
    @include('apps::actions.forms.memberkit')
    @include('apps::actions.forms.builderall')
    @include('apps::actions.forms.notazz')

    @include('apps::actions.includes.delete')
    @include('apps::actions.modals.logs')

    @include('elements.toast')
@endsection
