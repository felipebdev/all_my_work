@extends('templates.xgrow.main')
@php
    $errorToast = '';
@endphp
@if ($errors->any())
    @foreach ($errors->all() as $error)
        @php
            $errorToast = $error;
        @endphp
    @endforeach
@endif
@push('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const productType = @json($product->type);
        const charges = @json($plan->charge_until);
        const paymentMethodCreditCard = @json($plan->payment_method_credit_card);
        const initialPlanRecurrence = parseInt(@json($plan->recurrence));
        const errorMessage = '{{ $errorToast }}';
        const minPriceValue = 4;

        $("#price, #promotional_price").maskMoney({
            decimal: ",",
            thousands: ".",
            precision: 2
        });
        $("#price, #promotional_price").trigger("mask.maskMoney");

        $("#price").on("blur", function() {
            const price = $(this).val().replace(".", "").replace(",", ".") || 0;
            checkMaxInstallments(price, productType, $('#recurrence').val());
            minPrice();
            equalPrice();
        });

        $("#promotional_price").on("blur", function() {
            const promocionalPrice = $(this).val().replace(".", "").replace(",", ".") || 0;
            minPromocionalPrice();
            equalPrice();
        });

        const SPMaskBehavior = function(val) {
                return val.replace(/\D/g, "").length === 11 ? "(00) 00000-0000" : "(00) 0000-00009";
            },
            spOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };
        $(".ipt-phone").mask(SPMaskBehavior, spOptions);
        $(".ipt-country-code").mask("+9000");

        function chkMinInstallments() {
            const installment = $('#installment')
            if (!installment.val())
                installment.val(1)
        }

        function checkMaxInstallments(price, paramProductType, planRecurrence) {
            let installmentsByPrice = Math.floor(price / minPriceValue);
            let maxInstallments = installmentsByPrice;

            if (paramProductType === 'R') {
                const maxInstallmentsByRecurrence = {
                    1: 1,
                    7: 1,
                    30: 1,
                    60: 2,
                    90: 3,
                    180: 6,
                    360: 12
                }

                const installmentsByRecurrence = maxInstallmentsByRecurrence[parseInt(planRecurrence)]

                maxInstallments = installmentsByRecurrence < installmentsByPrice ? installmentsByRecurrence :
                    installmentsByPrice
            }

            $("#installment option").each(function() {
                const installment = $(this).val();
                if (installment > maxInstallments) {
                    $(this).prop("disabled", true);
                    $(this).prop("selected", false);
                } else {
                    $(this).prop("disabled", false);
                    $(this).prop("selected", "selected");
                }
            });

            const installmentNew = parseInt($("#installment").val())
            const installmentDefault = parseInt($('#installmentDefault').val())

            if (installmentNew >= installmentDefault) {
                $("#installment").val(installmentDefault).change()
            }
        }

        $("#switch-all-payment-type").on("change", function(e) {
            const isChecked = $(this).is(":checked")
            $(".paid_plan, #chk-orderbump-exists").prop("checked", isChecked);
            if (isChecked)
                $("#payment_method_free").prop("checked", false);
            showBilletDays()
        });

        $("#payment_method_free").on("change", () => {
            const isChecked = $("#payment_method_free").is(":checked")
            togglePaymentMethod(isChecked)
        });

        function togglePaymentMethod(isChecked) {
            const paidPlan = $(".paid_plan");
            paidPlan.prop("checked", false);
            paidPlan.prop("disabled", isChecked);
            showBilletDays()
            orderBump().disableOrderBump(isChecked)
        }

        function chkTrigger(chkId, divId) {
            const elems = $("#" + divId + " :input");
            $("#" + chkId).is(":checked") ? elems.removeAttr("disabled").attr("required", "required") : elems.attr(
                "disabled", true).removeAttr("required");

        }

        $("#chk-charge-until").change(function() {
            if ($("#chk-charge-until").is(":checked")) {
                $("#divChargeUntil").hide(500);
                $("#charge_until").val(0);
            } else {
                $("#divChargeUntil").show(500);
            }
        });

        function chkChargeUntil() {
            if (parseInt(charges) === 0) {
                $("#chk-charge-until").prop("checked", true);
            } else {
                $("#chk-charge-until").prop("checked", false);
            }

            if ($("#chk-charge-until").is(":checked")) {
                $("#divChargeUntil").hide(500);
            } else {
                $("#divChargeUntil").show(500);
            }
        }

        $("#use_promotional_price, #chk-freedays, #chk-charge-until").change(function() {
            chkTrigger("use_promotional_price", "div-promotional_price");
        });

        function verifyCHKs() {
            chkTrigger("use_promotional_price", "div-promotional_price");
            chkChargeUntil();
            showBilletDays();
        }

        function divDisposes() {
            if (`${productType}` === "P") {
                $("#div-subscription").hide();
                $("#div-test-period").hide();
                $("#div-installment").show();

                $(".payment-single").show();
            } else {
                $("#div-subscription").show();
                $("#div-test-period").show();

                if (productType === 'R' && paymentMethodCreditCard === 1) {
                    $("#div-installment").show();
                } else {
                    $("#div-installment").hide();
                }

                $(".payment-single").hide();
                //$("#payment_method_credit_card").prop("checked", true);
            }
        }

        $("#frmPlanSale").submit(function(e) {
            e.preventDefault();
            if (!$("#payment_method_free").is(":checked")) {

                if (!$("#payment_method_credit_card").is(":checked") &&
                    !$("#payment_method_boleto").is(":checked") &&
                    !$("#payment_method_pix").is(":checked") &&
                    !$("#payment_method_multiple_cards").is(":checked") &&
                    !$('#payment_method_multiple_means').is(':checked') &&
                    !$("#unlimited_sale").is(":checked")
                ) {
                    errorToast("Erro ao salvar informações",
                        "Você precisa ao menos selecionar uma forma de pagamento.");
                    return false;
                }

                if (minPrice()) return false;
                if (equalPrice()) return false;
                if (chkMinInstallments()) return false;
                if (minPromocionalPrice()) return false;
                if (billetDaysLimit()) return false;
            }

            e.currentTarget.submit();
        });

        const paymentMethods = $(".form-check-input.sell")

        paymentMethods.change(function() {
            checkSelectedPaymentMethods()
        });

        function checkSelectedPaymentMethods() {
            let allChecked = true
            paymentMethods.each(function(index, element) {
                if (!$(element).prop('checked') && paymentMethods.length !== (index + 1)) {
                    allChecked = false
                }
            })
            $("#switch-all-payment-type").prop("checked", allChecked);
        }

        function minPrice() {
            const price = document.getElementById("price");
            const lblPrice = document.getElementById("lblPrice");
            const changePrice = price.value.replace(".", "").replace(",", ".");
            const isMinor = parseFloat(changePrice) < minPriceValue;
            if (isMinor) {
                lblPrice.classList.remove("d-none");
                errorToast("Verifique!", "O valor mínimo do produto é de R$" + minPriceValue + ",00.");
            }
            lblPrice.classList.add("d-none");
            return isMinor;
        }

        function equalPrice() {
            const price = document.getElementById("price");
            const promocionalPrice = document.getElementById("promotional_price");
            const treatPrice = price.value.replace(".", "").replace(",", ".");
            const treatPromocionalPrice = promocionalPrice.value.replace(".", "").replace(",", ".");

            const sameValue = parseFloat(treatPrice) == parseFloat(treatPromocionalPrice);
            if (sameValue) {
                lblPrice.classList.remove("d-none");
                errorToast("Verifique!", "O valor do produto não pode ser igual ao valor diferenciado.");
            }
            lblPrice.classList.add("d-none");
            return sameValue;
        }

        function minPromocionalPrice() {
            isMinor = false;
            if ($("#use_promotional_price").is(":checked")) {
                const promocionalPrice = document.getElementById("promotional_price");
                const lblPromocionalPrice = document.getElementById("lbl_promocional_price");
                const changePromocionalPrice = promocionalPrice.value.replace(".", "").replace(",", ".");
                const isMinor = parseFloat(changePromocionalPrice) < minPriceValue;

                if (isMinor) {
                    lblPromocionalPrice.classList.remove("d-none");
                    errorToast("Verifique!", "O valor diferenciado mínimo do produto é de R$" + minPriceValue + ",00.");
                }
                lblPromocionalPrice.classList.add("d-none");

                return isMinor;
            }
            return isMinor;
        }

        function showBilletDays() {
            if ($("#payment_method_boleto").is(":checked")) {
                @json($plan->checkout_payout_limit) ===
                    null ? $("#checkout_payout_limit").val(2) : "";
                $("#billetDays").show(500);
            } else {
                $("#billetDays").hide(500);
            }
        }

        function billetDaysLimit() {
            const minimum = @json(app()->environment('local', 'testing', 'develop') ? 1 : 2);
            if ($("#payment_method_boleto").is(":checked") && ($("#checkout_payout_limit").val() < 2 || $(
                    "#checkout_payout_limit").val() > 10)) {
                errorToast("Erro ao salvar informações",
                    "A quantidade de dias do vencimento do boleto deve ser entre ' + minimum + ' e 10 dias.");
                return true;
            }
        }

        $("#payment_method_boleto").change(function() {
            showBilletDays();
        });

        $("#checkout_payout_limit").on("blur", function() {
            billetDaysLimit();
        });

        document.getElementById('installment').addEventListener('change', function() {
            const newDefault = $('#installment').val()
            $('#installmentDefault').val(newDefault)
        })

        if (productType === 'R') {
            document.getElementById('payment_method_credit_card').addEventListener('change', () => {
                const element = $("#payment_method_credit_card")

                if (element.prop('checked')) {
                    $("#div-installment").show();
                } else {
                    $("#div-installment").hide();
                }
            });

            document.getElementById('recurrence').addEventListener('change', () => {
                const recurrenceValue = parseInt($('#recurrence').val())
                let price = document.getElementById("price").value;
                price = price.replace(".", "").replace(",", ".") || 0;

                checkMaxInstallments(price, productType, recurrenceValue);
            });
        }

        checkSelectedPaymentMethods();
        let price = document.getElementById("price").value;
        price = price.replace(".", "").replace(",", ".") || 0;

        checkMaxInstallments(price, productType, initialPlanRecurrence);

        verifyCHKs();
        divDisposes();

        function checkError(error) {
            errorToast("Erro ao salvar informações", error);
        }
        $(document).ready(function() {
            console.log(errorMessage)
            if (errorMessage != '')
                checkError(errorMessage);

            const isChecked = $("#payment_method_free").is(":checked")
            if (isChecked)
                togglePaymentMethod(isChecked)
        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><a href="/products">Produtos</a></li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link active" id="sales-plan-tab" data-bs-toggle="tab"
            href="javascript:void(0)" role="tab" aria-controls="sales-plan" aria-selected="false">Plano de venda</a>
    </div>

    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-sales-plan" role="tabpanel" aria-labelledby="nav-sales-plan">
            {!! Form::model($plan, [
                'method' => 'put',
                'enctype' => 'multipart/form-data',
                'id' => 'frmPlanSale',
                'route' => ['products.post.new.plan', $plan->id],
            ]) !!}
            <div class="xgrow-card card-dark p-0 mt-4">
                <div class="xgrow-card-body p-3">
                    <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                        Plano de vendas
                    </h5>

                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12 mb-3">
                            <div class="xgrow-form-control">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                                    style="margin-top:-3px;">
                                    {!! Form::text('name', $plan->name, [
                                        'id' => 'name',
                                        'autocomplete' => 'off',
                                        'spellcheck' => 'false',
                                        'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
                                        'required',
                                    ]) !!}
                                    {!! Form::label('name', 'Nome do Plano') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3"
                                style="margin-top:-3px;">
                                {!! Form::select('currency', $currency, null, [
                                    'id' => 'currency',
                                    'required',
                                    'class' => 'xgrow-select form-check-input',
                                    'readonly' => true,
                                ]) !!}
                                {!! Form::label('currency', 'Tipo de moeda:') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('price', $plan->price, [
                                    'id' => 'price',
                                    'autocomplete' => 'off',
                                    'spellcheck' => 'false',
                                    'class' => 'mui--is-not-empty mui--is-untouched mui--is-pristine',
                                    'maxlength' => 10,
                                ]) !!}
                                {!! Form::label('price', 'Valor do produto') !!}
                            </div>
                            <p class="xgrow-medium-italic d-none" id="lblPrice" style="margin-top:-15px">
                                O valor mínimo do produto é de R$4,00.
                            </p>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div id="div-installment"
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::select('installment', $installments, $plan->installment, [
                                    'id' => 'installment',
                                    'class' => 'xgrow-select',
                                ]) !!}
                                {!! Form::label('installment', 'Nº de parcelas:') !!}
                                <input type="hidden" id="installmentDefault" value="{{ $plan->installment }}">
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12 mt-5">
                            <h6>Forma de pagamento</h6>
                            <div id="div-payment-type-sell">
                                <div class="d-flex flex-wrap">
                                    @if (config('app.env') != 'production')
                                        <div class="form-check form-switch me-3">
                                            {!! Form::checkbox('payment_method_free', false, $plan->payment_method_free, [
                                                'class' => 'form-check-input',
                                                'id' => 'payment_method_free',
                                            ]) !!}
                                            {!! Form::label('payment_method_free', 'Produto gratuito', ['class' => 'form-check-label']) !!}
                                        </div>
                                    @endif
                                    <div class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_credit_card', true, $plan->payment_method_credit_card, [
                                            'class' => 'form-check-input sell paid_plan',
                                            'id' => 'payment_method_credit_card',
                                        ]) !!}
                                        {!! Form::label('payment_method_credit_card', 'Cartão', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div id="method_boleto" class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_boleto', true, $plan->payment_method_boleto, [
                                            'class' => 'form-check-input sell paid_plan',
                                            'id' => 'payment_method_boleto',
                                        ]) !!}
                                        {!! Form::label('payment_method_boleto', 'Boleto', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div id="method_pix" class="form-check form-switch me-3">
                                        {!! Form::checkbox('payment_method_pix', true, $plan->payment_method_pix, [
                                            'class' => 'form-check-input sell paid_plan',
                                            'id' => 'payment_method_pix',
                                        ]) !!}
                                        {!! Form::label('payment_method_pix', 'Pix', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3 payment-single">
                                        {!! Form::checkbox('payment_method_multiple_cards', true, $plan->payment_method_multiple_cards, [
                                            'class' => 'form-check-input sell paid_plan',
                                            'id' => 'payment_method_multiple_cards',
                                        ]) !!}
                                        {!! Form::label('payment_method_multiple_cards', 'Múltiplos cartões', ['class' => 'form-check-label']) !!}
                                    </div>
                                    @if (app()->environment('local', 'testing', 'develop'))
                                        <div class="form-check form-switch me-3 payment-single">
                                            {!! Form::checkbox('payment_method_multiple_means', true, $plan->payment_method_multiple_means, [
                                                'class' => 'form-check-input paid_plan',
                                                'id' => 'payment_method_multiple_means',
                                            ]) !!}
                                            {!! Form::label('payment_method_multiple_means', 'Multimeios (Cartões de crédito + Boleto)', [
                                                'class' => 'form-check-label',
                                            ]) !!}
                                        </div>
                                    @endif
                                    <div class="form-check form-switch me-3 payment-single">
                                        {!! Form::checkbox('unlimited_sale', true, $plan->unlimited_sale, [
                                            'class' => 'form-check-input sell paid_plan',
                                            'id' => 'unlimited_sale',
                                        ]) !!}
                                        {!! Form::label('unlimited_sale', 'Parcelamento sem limite Xgrow', ['class' => 'form-check-label']) !!}
                                    </div>
                                    <div class="form-check form-switch me-3 payment-single">
                                        {!! Form::checkbox('payment_all', true, $plan->payment_all, [
                                            'class' => 'form-check-input sell paid_plan',
                                            'id' => 'switch-all-payment-type',
                                        ]) !!}
                                        <label class="form-check-label" for="switch-all-payment-type">Todos</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-4 mt-4" id="billetDays" style="display: none">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::number('checkout_payout_limit', $plan->checkout_payout_limit, [
                                    'id' => 'checkout_payout_limit',
                                    'autocomplete' => 'off',
                                    'spellcheck' => 'false',
                                    'class' => 'mui--is-not-empty mui--is-untouched mui--is-pristine',
                                ]) !!}
                                {!! Form::label('checkout_payout_limit', 'Prazo de vencimento do boleto (em dias)') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-8 col-lg-8"></div>

                        @include('products.subscription')
                        @include('products.test-period')
                        @include('products.orderbump')
                        @include('products.upsell')
                        @include('products.greeting')
                    </div>
                </div>
                <div class="xgrow-card-footer p-3 border-top mt-4 justify-content-end">
                    <button class="xgrow-button" type="submit">
                        Salvar
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @include('elements.toast')
    @include('elements.confirmation-modal')
@endsection
