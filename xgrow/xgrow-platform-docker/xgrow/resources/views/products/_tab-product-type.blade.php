@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/products.css') }}" rel="stylesheet">
@endpush

@push('jquery')
    <script>
        $('.btnPlan').click(function (e) {
            const type = e.currentTarget.dataset.plan;
            const typeField = $('#type');
            type === 'single' ? typeField.val('P') : typeField.val('R');
            $('#informations-tab').removeClass('d-none');
            $('#informations-tab').addClass('active');
        })
    </script>
@endpush

<div class="tab-pane fade {{(!Request::get('plan') && !Request::get('type')) ? 'show active' : '' }}" id="nav-product-type" role="tabpanel" aria-labelledby="nav-product-type">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Escolha o tipo do seu produto
            </h5>
            <div class="row justify-content-center my-5">
                <div class="col-sm-12 col-md-6 col-lg-3 my-3">
                    <a href="javascript:void(0)" class="product-card-link nextPage btnPlan" data-plan="single">
                        <div class="product-card">
                            <div class="product-card-img">
                                <img src="{{url('xgrow-vendor/assets/img/products/sales_once.svg')}}" alt="Venda única">
                            </div>
                            <div class="product-card-info">
                                <h1>Venda Única</h1>
                                <hr>
                                <p>Produto com<br>pagamento único <br> ou gratuito</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-3">
                    <a href="javascript:void(0)" class="product-card-link nextPage btnPlan" data-plan="subscription">
                        <div class="product-card">
                            <div class="product-card-img">
                                <img src="{{url('xgrow-vendor/assets/img/products/subscriptions.svg')}}"
                                     alt="Assinatura">
                            </div>
                            <div class="product-card-info">
                                <h1>Assinaturas</h1>
                                <hr>
                                <p>Produto de<br>recorrência</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
