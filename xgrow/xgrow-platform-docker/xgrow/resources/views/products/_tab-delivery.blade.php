@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const product = @json($product->id);
        const getAllDeliveries = @json(route('products.get.all.deliveries'));
        const setDeliveryURL = @json(route('products.set.delivery'));
        const productInfoURL = @json(route('products.info', ':id'));
        const accessLink = @json(env('APP_URL_LEARNING_AREA', 'https://learningarea.xgrow.com') . '/' . $product->platform_id);
        const platform_id = @json($product->platform_id);
        const clearSubscribersCache = @json(route('products.subscriber.clear.cache'));
        const contentAPI = @json(config('learningarea.url'));

        const attachContent = @json(route('products.attach.content.graphql'));
        const detachContent = @json(route('products.detach.content.graphql'));
        const listContents = @json(route('products.list.content.graphql'));
    </script>
    <script src="{{ asset('js/bundle/products-create-delivery.js') }}"></script>
@endpush

@include('elements.status-modal')

<div class="tab-pane fade {{ Route::current()->getName() === 'products.delivery' ? 'show active' : '' }}"
    id="nav-delivery" role="tabpanel" aria-labelledby="nav-delivery">
    <div id="deliveryApp">
        <div class="xgrow-card card-dark p-0 mt-4">
            <div class="xgrow-card-body p-3">
                <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                    Modelo de entrega do produto
                </h5>

                <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-check form-switch">
                                {!! Form::checkbox('chk-only-sale', null, null, [
                                    'id' => 'chk-only-sale',
                                    'class' => 'form-check-input',
                                    'v-model' => 'onlySell',
                                    '@change' => 'syncOnlySell',
                                ]) !!}
                                {!! Form::label('chk-only-sale', 'Vou apenas vender', ['class' => 'form-check-label']) !!}
                            </div>
                        </div>
                    </div>

                    @include('products._delivery-external')
                    @include('products._delivery-internal')
                </div>

            </div>
            {!! Form::model($product, [
                'route' => ['products.info', $product->id],
                'method' => 'GET',
                'id' => 'submitDelivery',
            ]) !!}
            <div class="xgrow-card-footer p-3 border-top mt-4 justify-content-end">
                <button class="xgrow-button" @click.prevent="saveForm()">
                    Próximo
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
