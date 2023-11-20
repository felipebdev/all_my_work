@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/products.css') }}" rel="stylesheet">
@endpush

<div class="tab-pane fade {{Route::current()->getName() === 'products.info' ? 'show active' : ''}}" id="nav-last-flow"
     role="tabpanel" aria-labelledby="nav-last-flow">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Seu produto foi cadastrado com sucesso.
            </h5>

            <div class="ef-bg-product">
                <div class="ef-bg2-product">
                    <div class="ef-product">
                        <div class="ef-product-image">
                            <img
                                src="{{isset($product->image) ? $product->image->filename : url('xgrow-vendor/assets/img/big-file.png')}}
                                    " alt="sample image">
                        </div>
                        <div class="ef-product-description">
                            <h1>{{$product->name}}</h1>

                            <span>{{$keywords ?? ''}}</span>

                            <p>{{Str::limit($product->description, 150)}}</p>
                        </div>
                        <div class="ef-product-price">
                            <div class="ef-price">
                                <h1>R$ {{number_format($plan->price, 2, ',', '.')}}</h1>
                                <p>À VISTA OU {{$plan->installment}}X de
                                    R$ {{number_format(($plan->price != 0 ? $plan->getInstallmentValue($plan->price, $plan->installment) : 1), 2, ',', '.')}}</p>
                            </div>
                            <div class="ef-payments">
                                @if($plan->payment_method_credit_card)
                                    <span>Crédito</span>
                                @endif
                                @if($plan->payment_method_boleto)
                                    <span> - Boleto</span>
                                @endif
                                @if($plan->payment_method_pix)
                                    <span> - PIX</span>
                                @endif
                                @if($plan->payment_method_multiple_cards)
                                    <span> - Múltiplos Cartões</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="xgrow-card-footer p-3 border-top mt-4 justify-content-end flex-wrap gap-3">
            <div class="col-sm-12 d-none">
                <span>Personalize ainda mais suas vendas e apresentação do seu produto</span>
            </div>
            <div class="d-flex gap-3 flex-wrap d-none">
                <a href="#!" class="xgrow-upload-btn-lg xgrow-outline-btn btn">Página do produto</a>
                <a href="#!" class="xgrow-upload-btn-lg xgrow-outline-btn btn">Configurações avançadas</a>
            </div>

            <div>
                <button class="xgrow-button"
                        onclick="window.location.href='{{route('products.edit-plan', $product->id)}}'">
                    Finalizar Cadastro
                </button>
            </div>
        </div>
    </div>
</div>
