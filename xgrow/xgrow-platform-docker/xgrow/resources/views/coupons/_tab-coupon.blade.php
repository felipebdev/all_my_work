@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/dashboard_index.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script>
        $(function () {
            $("#slc-plans").select2({
                tags: "true",
                allowClear: true,
                placeholder: "Planos"
            }).change(function () {
                if ($("#value").val()) {
                    validateDiscount();
                }
                verifyPlans();
            });
        });

        function verifyPlans() {
            let items = $("#slc-plans").select2("data");
            items = items.map(i => i.id);
            if (items.length > 0) {
                axios.post(@json(route('coupons.verify')), {plans: items}
                ).then(function (res) {
                    res.data.response.hasSubscription ? $("#divAlert").show(500) : $("#divAlert").hide(300);
                }).catch(function (error) {
                    console.log(error);
                });
            } else {
                $("#divAlert").hide(300);
            }
        }

        $(".input-no-car-especial").keyup(function (e) {
            if ($("#value_type").val() == "V") {
                $(".input-no-car-especial").attr("maxlength", "");
                $(".input-no-car-especial").mask("#0,00", {
                    reverse: true
                });
            } else {
                $(".input-no-car-especial").mask("00,00", {
                    reverse: true
                });
            }
        });

        $("#value_type").change(function () {
            var ctype = $(this).find("option:selected").attr("value");
            if (ctype == "V") {
                $(".input-no-car-especial").attr("maxlength", "");
                $(".input-no-car-especial").unmask().mask("#0,00", {
                    reverse: true
                });
            } else {
                $(".input-no-car-especial").unmask().mask("00,00", {
                    reverse: true
                });
            }
        });

        const availablePlans = @json($plans -> pluck('price', 'id'));

        function getMaxAllowedValueDiscount() {
            const prices = $("#slc-plans").find(":selected").map(function () {
                const value = $(this).val();
                return availablePlans[value] ?? 0;
            }).toArray();

            if (prices.length === 0) {
                return undefined;
            }

            const cheaper = prices.reduce((pastPrice, price) => {
                return pastPrice > price ? pastPrice : price;
            });

            const minCheckout = 5;
            const maxDiscount = cheaper - minCheckout;

            return maxDiscount > 0 ? maxDiscount : 0;
        }

        function validateDiscountValue(target) {
            target.setCustomValidity("");
            const max = getMaxAllowedValueDiscount();

            if (typeof max === "undefined") {
                let message = "Selecione um produto antes de colocar o desconto.";
                errorToast("Algum erro aconteceu!", `${message}`);
                return false;
            }

            const value = parseFloat(target.value.replace(",", ".").replace(" ", ""));
            if (value > max) {
                const currency = formatCoin(max, "BRL", false);
                let message = "Desconto maior que o permitido!";
                target.setCustomValidity(message);
                errorToast("Algum erro aconteceu!", `Veja mais em: ${message} Máximo: ${currency}`);
                return false;
            }
            return true;
        }

        function validateDiscountPercentage(target) {
            target.setCustomValidity("");
            const value = parseFloat(target.value.replace(",", ".").replace(" ", ""));
            if (value > 90) {
                let message = "Desconto máximo de 90%!";
                target.setCustomValidity(message);
                errorToast("Algum erro aconteceu!", `Veja mais em: ${message}`);
                return false;
            }
            return true;
        }

        function validateDiscount() {
            const target = $("#value").get(0);
            var ctype = $("#value_type").val();
            if (ctype === "V") {
                return validateDiscountValue(target);
            } else {
                return validateDiscountPercentage(target);
            }
        }

        $("#value").on("change", function (e) {
            validateDiscount();
        });

        $(".input-no-car-especial").on("paste", function (e) {
            var regex = new RegExp("^[ 0-9a-zA-Z\b]+$");
            var _this = this;
            setTimeout(function () {
                var texto = $(_this).val();
                if (!regex.test(texto)) {
                    $(_this).val("");
                }
            }, 100);
        });

        $(".codigo-cupom").on("keypress paste", function (event) {
            var regex = new RegExp("^[a-zA-Z0-9]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
        });

        $("#saveCoupon").click(function () {
            $("#saveCoupon").prop("disabled", true);
            $("#spnLoading").show();
            $("#frmCoupon").submit();
        });
    </script>
@endpush

<div class="tab-pane fade show {{ !Request::get('mailingtab') ? 'active' : '' }}" id="nav-coupon" role="tabpanel"
     aria-labelledby="nav-coupon-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        @if ($coupon->id > 0)
            {!! Form::model($coupon, ['method' => 'put', 'enctype' => 'multipart/form-data', 'route' => ['coupons.update', 'id' => $coupon->id], 'novalidate' => true, 'id' => 'frmCoupon']) !!}
        @else
            {!! Form::model($coupon, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['coupons.store'], 'novalidate' => true, 'id' => 'frmCoupon']) !!}
        @endif
        {{ csrf_field() }}

        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Cupom
            </h5>

            @include('elements.alert')
            <div class="row mt-3">

                <div class="col-md-12 col-sm-12" id="divAlert" style="display: none">
                    <div class="alert alert-warning d-flex align-items-center justify-content-between"
                         role="alert">
                        <div class="d-flex gap-1 align-items-center">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>
                                Esse é um produto de recorrência. O desconto aplicado com o cupom valerá apenas
                                para o primeiro pagamento dessa recorrência.<br>Caso você deseje cobrar o valor
                                promocional até o fim da recorrência, crie um novo plano com um valor diferenciado.<br>
                                <a href="{{route('products.index')}}" style="color: #FFd065;font-weight: 700">
                                    Ir para produtos
                                </a>
                            </span>
                        </div>
                        <div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                    onclick="$('#divAlert').hide(300)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <p class="xgrow-large-regular mb-3">Planos habilitados para uso do cupom</p>
                    <div class="xgrow-form-control mb-2">
                        <select id="slc-plans" class="xgrow-select" name="plans[]" multiple style="width: 100%">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @foreach (old('plans[]', $coupon->plans) as $p)
                                @if ($plan->id===$p->id)
                                selected='selected' @endif
                                    @endforeach>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <p class="xgrow-large-regular mb-3">Dados do cupom</p>
                    <div class="row mb-5">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('code', old('code', $coupon->code), [
    'id' => 'code',
    'required',
    'autocomplete' => 'off',
    'spellcheck' => 'false',
    'class' => 'mui--is-empty mui--is-untouched mui--is-pristine codigo-cupom',
    'maxlength' => 20,
    'style' => 'text-transform: uppercase',
]) !!}
                                {!! Form::label('code', 'Código') !!}
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div
                                class="xgrow-floating-input xgrow-form-control mui-textfield mui-textfield--float-label">
                                {!! Form::text('maturity', old('maturity', (is_null($coupon->maturity)) ? '' : date('d/m/Y', strtotime($coupon->maturity)) ), [
    'id' => 'ipt-maturity',
    'class' => 'custom-datepicker xgrow-datepicker',
    'data-provide' => 'datepicker',
    'autocomplete' => 'off',
]) !!}
                                {!! Form::label('maturity', 'Validade') !!}
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-1">
                                <textarea class="w-100 mui--is-empty mui--is-pristine mui--is-touched" rows="6"
                                          maxlength="120" id="description"
                                          name="description">{!! old('description', $coupon->description) !!}</textarea>
                                <label for="description">Escreva aqui uma breve descrição do cupom</label>
                            </div>
                            <p class="xgrow-medium-italic ms-2">Até 200 caracteres</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="row">
                        <p class="xgrow-large-regular mb-3">Desconto</p>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-lg-9 col-md-6 col-sm-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        {!! Form::text('value', old('value', $coupon->value), [
    'id' => 'value',
    'required',
    'autocomplete' => 'off',
    'spellcheck' => 'false',
    'class' => 'mui--is-empty mui--is-untouched mui--is-pristine input-no-car-especial',
]) !!}
                                        {!! Form::label('value', 'Desconto') !!}
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                        {!! Form::select('value_type', ['V' => 'R$', 'P' => '%'], old('value_type', $coupon->value_type), [
    'class' => 'xgrow-select',
    'id' => 'value_type',
]) !!}
                                        {!! Form::label('value_type', 'Tipo') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                        {!! Form::number('usage_limit', old('usage_limit', $coupon->usage_limit), [
    'id' => 'usage_limit',
    'min' => 1,
    'required',
    'autocomplete' => 'off',
    'spellcheck' => 'false',
    'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
]) !!}
                                        {!! Form::label('usage_limit', 'Nº do limite de usos') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4 align-items-center">
            <input class="xgrow-button" id="saveCoupon" type="button" value="Salvar">
            <span style="color:var(--green1);margin-left:1rem;display:none" id="spnLoading"><i
                    class="fas fa-spinner fa-spin"></i> Salvando...</span>
        </div>

        @include('elements.toast')
        {!! Form::close() !!}
    </div>
</div>
