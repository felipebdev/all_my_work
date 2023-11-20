
{{--<form class="" id="formSubscriber" method="POST" action="{{ route('getnet.card.store') }}">--}}
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
                        <input type="text" class="form-control" id="cardholder_identification" name="cardholder_identification" value="" placeholder="0000 0000 0000 0000" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cardholder_name">Nome *</label>
                        <input type="text" class="form-control" id="cardholder_name" name="cardholder_name" value="" placeholder="Como está escrito no cartão" required>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="expiration">Validade *</label>
                        <input type="text" class="form-control" id="expiration" name="expiration" value="" placeholder="MM/AA" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="security_code">Código de Segurança *</label>
                        <input type="text" class="form-control" id="security_code" name="security_code" value="" placeholder="CVV" required>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <input type="hidden" class="form-control" id="platform_id" name="platform_id" value="{{ $platform_id }}" >
        <input type="hidden" class="form-control" id="plan_id" name="plan_id" value="{{ base64_encode($plan->id) }}" >
        <input type="hidden" class="form-control" id="subscriber_id" name="subscriber_id" value="{{ $subscriber->id }}" >
        <input type="hidden" class="form-control" id="course_id" name="course_id" value="{{ $course_id ?? 0 }}" >
    </div>
    <div class="row">
        <div class="col-md-12" style="text-align: right;">
            <button type="submit" class="btn btn-lg btn-success shadow p-3 mb-5">Concluir assinatura</button>
        </div>
    </div>
{{--</form>--}}
