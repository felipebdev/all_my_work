@section('js-payment')
    <script src="https://checkout.mundipagg.com/transparent.js"
            data-mundicheckout-app-id="{{$public_key}}">
    </script>
    <script>
        function success(data) {
            console.log(data);
            @isset($platform->pixel_id)
            fbq('track', 'Purchase', {currency: "BRL", value:"{{$plan->price}}"});
            @endisset
            return true;
        };
        function fail(error) {
            var message = "Não foi possível finalizar o pedido"
            $("#error").html('');
            if( error.statusCode == 422 ) {
                $("#error").append(message+": Verifique os dados do cartão informados e tente novamente");
                let objErrors = Object.entries(error.errors);
                for (var prop in objErrors) { $("#error").append("<br>"+objErrors[prop][1][0]); };
            }
            else {
                $("#error").append(message+": "+error.message);
            }
            $("#error").show();
            $(window).scrollTop(0);
            console.error(error);
        };
        MundiCheckout.init(success, fail)
    </script>
@endsection
<form id="form-payment" action="{{route('mundipagg.checkout.save', [$platform_id, base64_encode($plan->id), $course_id])}}" method="POST" data-mundicheckout-form>
    @csrf
    <div class="card text-left">
        <div class="card-header">
            <i class="fa fa-credit-card fa-5x" aria-hidden="true"></i>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cardholder_identification">Número do cartão de crédito *</label>
                        <span data-mundicheckout-input="brand"></span>
                        <input type="number" class="form-control" id="card-number"
                               name="card-number" value="" placeholder="0000 0000 0000 0000" required
                               min="0" max="9999999999999999" maxlength="16"
                               data-mundicheckout-input="number">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cardholder_name">Nome *</label>
                        <input type="text" class="form-control" id="holder-name" name="holder-name" value=""
                               placeholder="Como está escrito no cartão" required
                               data-mundicheckout-input="holder_name">
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expiration">Mês de validade *</label>
                        <input type="number" class="form-control" id="card-exp-month" name="card-exp-month" value=""
                               min="1" max="12" step="1"
                               placeholder="MM" required data-mundicheckout-input="exp_month">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expiration">Ano de validade *</label>
                        <input type="number" class="form-control" id="card-exp-year" name="card-exp-year" value=""
                               min="20" max="99" step="1"
                               placeholder="MM" required data-mundicheckout-input="exp_year">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="security_code">Código de Segurança *</label>
                        <input type="number" class="form-control" id="cvv" name="cvv" value=""
                               min="1" max="9999" step="1"
                               placeholder="CVV" required data-mundicheckout-input="cvv">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="hidden_fields" class="row" style="display: none"></div>
    <div class="row">
        <div class="col-md-12" style="text-align: right;">
            <button type="submit" class="btn btn-lg btn-success shadow p-3 mb-5 finalize-checkout_ ">Concluir assinatura</button>
        </div>
    </div>
</form>
