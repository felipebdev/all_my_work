@extends('templates.xgrow.main')

@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/products.css') }}" rel="stylesheet">
    <style>
        .table-responsive {
            overflow-y: hidden !important;
        }

        .xgrow-table.table-responsive {
            min-height: 160px !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/product-links.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bundle/products-affiliates.css') }}">
    <link href="{{ asset('xgrow-vendor/plugins/summernote/summernote-lite.min.css') }}" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/plugins/summernote/summernote-xgrow.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        // Plans routes
        const listPlansUrl = @json(route('list.plans', $product->id));
        const favoritePlanDefault = @json($product->favorite_plan);
        const deleteRoute = @json(route('products.plans.destroy', ':id'));
        const favoritePlanRoute = @json(route('products.favorite.plan'));
        const changeStatusUrl = @json(route('products.update.status.plan', ':id'));
        const newPlanRoute = @json(route('products.new.plan', $product->id));
        const editPlanRoute = @json(route('products.edit.plan.product', ''));
        const allowEditPlanUrl = @json(route('plan.allow.change', ':id'))
        // Links
        const linkPlans = @json($plans);
        const productID = {{ $product->id }};
        const listProductLinksURL = @json(route('product.links.list', $product->id));
        const listPlansProductLinksURL = @json(route('product.links.list.plans', $product->id));
        const createProductLinksURL = @json(route('product.links.create'));
        const updateProductLinksURL = @json(route('product.links.update', 0));
        const deleteProductLinksURL = @json(route('product.links.delete', 0));
        // Deliveries
        const product = @json($product->id);
        const setDeliveryURL = @json(route('products.set.delivery'));
        const getAllDeliveries = @json(route('products.get.all.deliveries'));
        const accessLink = @json(env('APP_URL_LEARNING_AREA', 'https://learningarea.xgrow.com') . '/' . $product->platform_id);
        const kajabiImg = @json(asset('xgrow-vendor/assets/img/kajabi-icon.png'));
        const cademiImg = @json(asset('xgrow-vendor/assets/img/cademi-icon.png'));
        const attachContent = @json(route('products.attach.content.graphql'));
        const detachContent = @json(route('products.detach.content.graphql'));
        const listContents = @json(route('products.list.content.graphql'));
        const clearSubscribersCache = @json(route('products.subscriber.clear.cache'));
        const platform_id = @json($product->platform_id);
        const contentAPI = @json(config('learningarea.url'));

        // Affiliates
        const affiliateEnableURL = @json(route('products.update.affiliation.enabled', $product->id));
        const affiliateSettingsURL = @json(route('affiliate.create-or-update'));
        const affiliateProductId = @json($product->id);
        const affiliation_enabled = Boolean(@json($product->affiliation_enabled));
        // Upsell Generator
        const getAllCoproducers = @json(route('api.producers.get.all', $product->id));
        const getUpsellURL = @json(route('products.list'));
        const getPlansByProductURL = @json(route('list.plans', ':id'));
        const checkoutUrl = @json(config('app.url_checkout'));
        const oneClickScript = @json(env('ONE_CLICK_BUY_URL', 'https://checkoutv3.xgrow.com/one-click-buy/oneclick.min.js'));

        // CoProducers
        const saveCoproducer = @json(route('api.producers.send.invite', $product->id));
        const updateCoproducer = @json(route('api.producers.update', $product->id));
        const cancelContract = @json(route('api.producers.cancel.contract', $product->id));
    </script>
    <script src="{{ asset('xgrow-vendor/plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/plugins/summernote/lang/summernote-pt-BR.min.js') }}"></script>
    <script src="{{ asset('js/bundle/products-edit.js') }}"></script>
@endpush

@push('jquery')
    <script>
        $(document).ready(function() {
            $('#modal-hide').css('display', 'block');
            var hash = location.hash.replace(/^#/, ""); // ^ means starting, meaning only match the first hash
            if (hash) {
                $("#nav-" + hash + "-tab").click();
            }
        });

        function changeStatus(id) {
            const route = @json(route('products.update.status.plan', ':id'));
            const url = route.replace(/:id/g, id);
            axios.put(url).then(function(response) {
                successToast("Registro alterado!", "Ação feita com sucesso!");
            }).catch(function(error) {
                errorToast("Algum erro aconteceu!",
                    `Houve um erro ao alterar o registro: ${error.response.data.message}`);
            });
        }
    </script>
@endpush

@section('content')
    <div id="productEditApp">
        <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item"><a href="/products">Produtos</a></li>
                <li class="breadcrumb-item active mx-2"><span>Editar</span></li>
            </ol>
        </nav>

        <div class="xgrow-card card-dark mt-2">
            <div class="row d-none">
                <div class="header-cards d-flex justify-content-end">
                    <div>
                        <span>0</span><br />
                        <span class="text-featured">Alunos</span>
                    </div>

                    <div>
                        <span>0</span><br />
                        <span class="text-featured">Vendas</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end d-none">
                <p class="p-0 mb-2">Total de registros 0 entre 19/05/2021 e 18/06/2021</p>
            </div>
            <div class="ef-bg-product mt-4">
                <div class="ef-bg2-product">
                    <div class="ef-product">
                        <div class="ef-product-image d-flex align-items-center">
                            <img src="{{ isset($product->image) ? $product->image->filename : url('xgrow-vendor/assets/img/big-file.png') }}"
                                alt="sample image">
                        </div>
                        <div class="ef-product-description">
                            <h1>{{ $product->name }}</h1>
                            <span class="mb-1">{{ $keywords ?? '' }}</span>
                            <p>{{ $product->description ? Str::limit($product->description, 150) : 'Descrição não informada.' }}
                            </p>
                        </div>
                        <div class="ef-product-price">
                            <div class="ef-price">
                                <h1>R$ {{ number_format($plan->price, 2, ',', '.') }}</h1>
                                <p>À VISTA OU {{ $plan->installment }}X de
                                    R$
                                    {{ number_format($plan->price != 0 ? $plan->getInstallmentValue($plan->price, $plan->installment) : 1, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="ef-payments">
                                @if ($plan->payment_method_credit_card)
                                    <span>Crédito</span>
                                @endif
                                @if ($plan->payment_method_boleto)
                                    <span> - Boleto</span>
                                @endif
                                @if ($plan->payment_method_pix)
                                    <span> - PIX</span>
                                @endif
                                @if ($plan->payment_method_multiple_cards)
                                    <span> - Múltiplos Cartões</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nav nav-pills nav-fill gap-3 justify-content-center justify-content-md-between mt-3 " id="nav-tab"
                role="tablist">
                <li class="nav-item-buttom btn-block nav-item nav-link active" id="nav-plans-tab" data-bs-toggle="tab"
                    href="#nav-plans" role="tab" aria-controls="nav-plans" aria-selected="true">
                    <i class="fas fa-tag" style="margin-right: 5px"></i> Plano de vendas
                </li>
                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-links-tab" data-bs-toggle="tab"
                    href="#nav-links" role="tab" aria-controls="nav-links" aria-selected="false">
                    <i class="fas fa-link" style="margin-right: 5px"></i> Links
                </li>
                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-delivery-tab" data-bs-toggle="tab"
                    href="#nav-delivery" role="tab" aria-controls="nav-delivery" aria-selected="false">
                    <i class="fas fa-shopping-cart" style="margin-right: 5px"></i> Entregas
                </li>
                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-upsell-tab" data-bs-toggle="tab"
                    href="#nav-upsell" role="tab" aria-controls="nav-upsell" aria-selected="false">
                    <i class="fas fa-tag" style="margin-right: 5px"></i> Gerador de upsell
                </li>
                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-coproduction-tab" data-bs-toggle="tab"
                    href="#nav-coproduction" role="tab" aria-controls="nav-coproduction" aria-selected="false">
                    <i class="fas fa-users" style="margin-right: 5px"></i> Coprodutores
                </li>
                {{--                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-deliveries-tab" --}}
                {{--                    href="{{route('products.delivery', $plan->id)}}" --}}
                {{--                    onclick="window.location.href='{{route('products.delivery', $plan->id)}}'"> --}}
                {{--                    Entregas --}}
                {{--                </li> --}}
                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-affiliates-tab" data-bs-toggle="tab"
                    href="#nav-affiliates" role="tab" aria-controls="nav-affiliates" aria-selected="false">
                    <i class="fas fa-address-card" style="margin-right: 5px"></i> Afiliados
                </li>
                <li class="nav-item-buttom btn-block nav-item nav-link" id="nav-configs-tab" data-bs-toggle="tab"
                    href="#nav-configs" role="tab" aria-controls="nav-configs" aria-selected="false">
                    <i class="fas fa-cog" style="margin-right: 5px"></i> Configurações
                </li>
            </div>

            <div class="tab-content pt-4">
                @include('products.edit._tab-configs')
                <div class="tab-pane fade show active" id="nav-plans" role="tabpanel" aria-labelledby="nav-plans-tab">
                    <Plans />
                </div>
                <div class="tab-pane fade" id="nav-links" role="tabpanel" aria-labelledby="nav-links-tab">
                    <Links />
                </div>
                <div class="tab-pane fade" id="nav-delivery" role="tabpanel" aria-labelledby="nav-delivery-tab">
                    <Deliveries />
                </div>
                <div class="tab-pane fade affiliates" id="nav-affiliates" role="tabpanel"
                    aria-labelledby="nav-affiliates-tab">
                    <Affiliates />
                </div>
                <div class="tab-pane fade" id="nav-upsell" role="tabpanel" aria-labelledby="nav-upsell-tab">
                    <Upsell />
                </div>
                <div class="tab-pane fade" id="nav-coproduction" role="tabpanel" aria-labelledby="nav-coproduction-tab">
                    <Coproducers />
                </div>
            </div>
        </div>
        @include('elements.alert')
        @include('elements.toast')
        @include('elements.status-modal')
        @include('elements.confirmation-modal')
    </div>
@endsection
