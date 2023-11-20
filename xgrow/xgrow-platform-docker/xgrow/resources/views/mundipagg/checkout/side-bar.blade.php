@section('js-payment')
    @parent
    <script>
        function changeInstallment(val) {
            $("#installmentSelected").val(val);
        }
    </script>
@endsection
<div class="card">
    <div class="card-header" style="text-align: right;background-color: #F1F1F1;">
        <div data-v-4371cce6="" data-v-2665559e="" class="secure-purchase-badge without-product">
        </div>
        <svg data-v-4371cce6="" width="22" height="26" viewBox="0 0 22 26" xmlns="http://www.w3.org/2000/svg" class="secure-purchase-badge__shield">
            <path data-v-4371cce6="" d="M21.284 5.3s3.65 16.194-10.176 20.243C-2.718 21.494.93 5.3.93 5.3L11.108.644 21.284 5.3zM10.605 18.67l6.42-6.378-1.764-1.751-4.656 4.626-3.124-3.104-1.763 1.751 4.887 4.856z" fill="#5AC857" fill-rule="evenodd" class="secure-purchase-badge__shield-path"></path>
        </svg>
        <span style="color: #000;">Compra 100% Segura</span>
    </div>
    <div class="card-body" style="background-color: #FFF;" >
        <h2 class="card-title">@if($plan->type_plan === 'R') Plano @endif {{ $plan->name }}</h2>
        <hr>
        <div class="product-info">
            <div class="product-text">
                <p class="product-description">
                    @if( strlen(strip_tags($plan->description)) > 0 )
                        {!! $plan->description !!}
                    @else
                        {!! 'Este é um produto digital, você receberá os dados para acessá-lo via internet.' !!}
                    @endif</p>
            </div>
            @isset($image_logo)
            <div class="div-log" style="background-image:url({{ config('app.url').'/uploads/'.$image_logo }})"></div>
            @endisset

            <hr>
            <h2 class="card-title">R$ {{ number_format($plan->price, 2, ',', '.') }} @if($plan->type_plan === 'R') {{ $plan->getDescription($plan->recurrence) }} @endif</h2>
            @if($plan->type_plan === 'P')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group @if(isset($subscriber->id)) d-none @endif">
                            <select class="custom-select" name="installment" required onchange="changeInstallment(this.value);">
                                @for($i = 1; $i <= $plan->installment; $i++)
                                    @if($i === 1)
                                        <option value="{{ $i }}">1 x {{ number_format($plan->getInstallmentValue($plan->price, $i), 2, ',', '.') }} (sem juros)</option>
                                    @else
                                        <option value="{{ $i }}">{{ $i }} x R$ {{ number_format($plan->getInstallmentValue($plan->price, $i), 2, ',', '.') }} *</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="payment-methods-icons" style="padding: 5px;">
            <img src="/images/brands/mastercard.svg" alt="MASTER_CARD" style="flex-basis:50%;" width="40">
            <img src="/images/brands/visa.svg" alt="VISA" style="flex-basis: 50%;" width="40">
            <img src="/images/brands/dinners.svg" alt="VISA" style="flex-basis: 50%;" width="40">
            <img src="/images/brands/jcb.svg" alt="VISA" style="flex-basis: 50%;" width="40">
            <img src="/images/brands/elo.svg" alt="VISA" style="flex-basis: 50%;" width="40">
            <img src="/images/brands/hipercard.svg" alt="VISA" style="flex-basis: 50%;" width="40">
            <img src="/images/brands/amex.svg" alt="VISA" style="flex-basis: 50%;" width="40">
        </div>
    </div>
</div>
