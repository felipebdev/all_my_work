@extends('templates.xgrow.main')

@push('after-styles')
    <style>
        select[readonly] {
            pointer-events: none;
            touch-action: none;
        }

        option[disabled] {
            color: var(--contrast-gray);
            font: var(--text-medium-regular);
            border-color: var(--input-bg-disabled) !important;
            background-color: var(--input-bg-disabled);
        }

        #image, #order_bump_image, #upsell_image {
            width: 164px;
            height: 128px;
            border-radius: 5px;
            object-fit: cover;
        }
    </style>
@endpush

@push('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#use_promotional_price').change(function () {
                if ($(this).is(':checked')) {
                    $('.promotional_price').removeClass('d-none');
                    $('#promotional_price').attr('required', 'required');
                    $('#promotional_periods').attr('required', 'required');
                } else {
                    $('.promotional_price').addClass('d-none');
                    $('#promotional_price').val('');
                    $('#promotional_price').removeAttr('required');
                    $('#promotional_periods').removeAttr('required');
                }
            });
            $('#type_plan').change(function () {
                if ($(this).children('option:selected').val() === 'P') {
                    $('#installment').show();
                    $('#installment').attr('required', true);
                    $('#div_installment').removeClass('d-none');
                    $('#div_category').removeClass('col-lg-12');
                    $('#div_link').addClass('d-none');
                    $('#div_period').addClass('d-none');
                    $('#installment').focus();
                    $('#divUnlimitedSale').removeClass('d-none');
                    $('#divUnlimitedSale').addClass('d-flex');
                    $('#div_category').addClass('col-lg-6 col-md-6 col-sm-12');

                    $('#div-payment-type-sell').show();
                    $('#div-payment-type-subscription').hide();
                    $('#div-subscription-fields').hide();
                    $('#div-promotional-price').hide();
                    $('#div-payment-type-subscription input').attr('disabled', true);
                    $('#div-payment-type-sell input').removeAttr('disabled');
                }
                if ($(this).children('option:selected').val() === 'R') {
                    $('#installment').val('');
                    $('#installment').hide();
                    $('#installment').removeAttr('required');
                    $('#div_installment').addClass('d-none');
                    $('#div_category').addClass('col-lg-12');
                    $('#div_link').removeClass('d-none');
                    $('#div_period').removeClass('d-none');
                    $('#ckbx-unlimited_sale').val(0);
                    $('#divUnlimitedSale').addClass('d-none');
                    $('#divUnlimitedSale').removeClass('d-flex');
                    $('#div_category').removeClass('col-lg-6 col-md-6 col-sm-12');

                    $('#div-payment-type-sell').hide();
                    $('#div-payment-type-subscription').show();
                    $('#div-subscription-fields').show();
                    $('#div-promotional-price').show();
                    $('#div-payment-type-sell input').attr('disabled', true);
                    $('#div-payment-type-subscription input').removeAttr('disabled');
                }
                if ($(this).children('option:selected').val() === '') {
                    $('#installment').val('');
                    $('#installment').hide();
                    $('#installment').removeAttr('required');
                    $('#div_installment').addClass('d-none');
                    $('#div_category').addClass('col-lg-12');
                    $('#div_link').addClass('d-none');
                    $('#div_period').addClass('d-none');
                    $('#ckbx-unlimited_sale').val(0);
                    $('#divUnlimitedSale').addClass('d-none');
                    $('#divUnlimitedSale').removeClass('d-flex');
                    $('#div_category').removeClass('col-lg-6 col-md-6 col-sm-12');
                }
            });
        });
        $('#ckbx-style-1-2').on('click', function () {
            const checked = $(this).is(':checked');
            if (checked === true) {
                $.ajax({
                    type: 'GET',
                    url: "{{ URL::route('plans.email-on') }}",
                    dataType: 'json',
                    data: {
                        'checked': checked,
                        'plan_id': $('#plan_id').val()
                    },
                    success: function (data) {
                        if (data.status === 'error') {
                            $('#ckbx-style-1-2').prop('checked', false);
                            toastr['error'](data.message);
                        }
                    },
                    error: function (data) {
                        if (data.status === 'error') {
                            $('#ckbx-style-1-2').prop('checked', false);
                            toastr['error'](data.message);
                        }
                    }
                });
            }
        });
        $('#integration_id').change(function () {
            const integration_id = $(this).children('option:selected').val();
            $.ajax({
                type: 'GET',
                url: '/plans/verify-gateway',
                dataType: 'json',
                data: {
                    'integration_id': integration_id
                },
                success: function (data) {
                    if (data.status === 'success' && data.id_webhook === 4) {
                        $('#div_custom').removeClass('d-none');
                        $('.content_html').summernote({
                            toolbar: [
                                ['font', ['bold', 'underline', 'clear']],
                                ['fontname', ['fontname']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['insert', ['link']]
                            ],
                            height: 200, // set editor height
                            minHeight: null, // set minimum height of editor
                            maxHeight: null, // set maximum height of editor
                            focus: false // set focus to editable area after initializing summernote
                        });
                    } else {
                        $('#div_custom').addClass('d-none');
                    }
                },
                error: function () {
                    $('#div_custom').addClass('d-none');
                }
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            const hash = location.hash.replace(/^#/, '');
            if (hash) {
                $('.nav-tabs a[href="#' + hash + '"]').tab('show');
            }

            $('.percent').mask('##0,00%', {
                reverse: true
            });

            $('#chk-terms-exists').on('change', function (e) {
                if ($(this).is(':checked')) {
                    $('#div-terms').removeClass('d-none');
                    $('#terms').attr('required', 'required');
                } else {
                    $('#div-terms').addClass('d-none');
                    $('#terms').removeAttr('required', 'required');
                }
            });

            const isOrderBumpEnabled = @json(isset($plan->order_bump_plan_id) || old('order_bump_plan_id') !== null);
            const isUpsellEnabled = @json(isset($plan->upsell_plan_id) || old('upsell_plan_id') !== null);

            if (!isOrderBumpEnabled) {
                $('#div-orderbump :input').attr('disabled', true);
            }

            if (!isUpsellEnabled) {
                $('#div-upsell :input').attr('disabled', true);
            }

            $('#chk-orderbump-exists').on('change', function (e) {
                if ($(this).is(':checked')) {
                    $('#div-orderbump :input').removeAttr('disabled').attr('required', 'required');
                } else {
                    $('#div-orderbump :input').attr('disabled', true).removeAttr('required');
                }
            });

            $('#chk-upsell-exists').on('change', function (e) {
                if ($(this).is(':checked')) {
                    $('#div-upsell :input').removeAttr('disabled').attr('required', 'required');
                } else {
                    $('#div-upsell :input').attr('disabled', true).removeAttr('required');
                }
            });

            // $('#type_plan option:first').attr('disabled', true);
            // $('#installment option:first').attr('disabled', true);
            // $('#currency option:first').attr('disabled', true);
            // $('.slc-recurrence option:first').attr('disabled', true);
            $('#chk-charge-until').on('click', function (e) {
                if ($(this).is(':checked')) {
                    $('#div-unlimited-billing').removeClass('d-none');
                    $('#div-unlimited-billing input, #div-unlimited-billing select').removeAttr('disabled');
                    $('#div-billing').addClass('d-none');
                    $('#div-billing input, #div-billing select').attr('disabled', true);
                } else {
                    $('#div-unlimited-billing').addClass('d-none');
                    $('#div-unlimited-billing input, #div-unlimited-billing select').attr('disabled', true);
                    $('#div-billing').removeClass('d-none');
                    $('#div-billing input, #div-billing select').removeAttr('disabled');
                }
            });

            $('#switch-all-payment-type').on('change', function (e) {
                if ($(this).is(':checked')) {
                    $('#div-payment-type-sell input[type=checkbox]').prop('checked', true);
                } else {
                    $('#div-payment-type-sell input[type=checkbox]').prop('checked', false);
                }
            });

            const typePayment = '{{ old('type_plan', $plan->type_plan) ?? null }}';
            if (typePayment === 'P') {
                $('#div_installment').removeClass('d-none');
                $('#div_category').removeClass('col-lg-12');
                $('#div-subscription-fields').hide();
                $('#div-payment-type-subscription').hide();
                $('#div-payment-type-sell').show();
                $('#div-promotional-price').hide();
                $('#div-payment-type-subscription input').attr('disabled', true);
                $('#div-payment-type-sell input').removeAttr('disabled');
                $('#div_category').addClass('col-lg-6 col-md-6 col-sm-12');
            } else if (typePayment === 'R') {
                $('#div_installment').addClass('d-none');
                $('#div_category').addClass('col-lg-12');
                $('#div-subscription-fields').show();
                $('#div-payment-type-subscription').show();
                $('#div-payment-type-sell').hide();
                $('#div-promotional-price').show();
                $('#div-payment-type-sell input').attr('disabled', true);
                $('#div-payment-type-subscription input').removeAttr('disabled');
                $('#div_category').removeClass('col-lg-6 col-md-6 col-sm-12');

            }

            const chargeUntilEnable =
                '{{ (isset($plan->charge_until) && old('charge_until', $plan->charge_until) == 0) || (!isset($plan->charge_until)) ? true : false }}';

            if (Boolean(chargeUntilEnable)) {
                $('#div-unlimited-billing').removeClass('d-none');
                $('#div-unlimited-billing input, #div-unlimited-billing select').removeAttr('disabled');
                $('#div-billing').addClass('d-none');
                $('#div-billing input, #div-billing select').attr('disabled', true);
                $('#chk-charge-until').prop('checked', true);
            } else {
                $('#div-billing').removeClass('d-none');
                $('#div-billing input, #div-billing select').removeAttr('disabled');
                $('#div-unlimited-billing').addClass('d-none');
                $('#div-unlimited-billing input, #div-unlimited-billing select').attr('disabled', true);
                $('#chk-charge-until').prop('checked', false);
            }

            const usePromotionalPriceEnable =
                '{{ isset($plan->use_promotional_price) && old('use_promotional_price', $plan->use_promotional_price) != 0 ? true : false }}';
            if (Boolean(usePromotionalPriceEnable)) {
                $('.promotional_price').removeClass('d-none');
            } else {
                $('.promotional_price').addClass('d-none');
            }

            const checkoutUrlTermsPriceEnable =
                '{{ isset($plan->checkout_url_terms) || old('checkout_url_terms', $plan->checkout_url_terms) !== null ? true : false }}';
            if (Boolean(checkoutUrlTermsPriceEnable)) {
                $('#chk-terms-exists').prop('checked', true);
                $('#div-terms').removeClass('d-none');

            } else {
                $('#chk-terms-exists').prop('checked', false);
                $('#div-terms').addClass('d-none');
            }

            const checkoutSupport = '{{ old('checkout_support', $plan->checkout_support) ?? null }}'
            const checkoutSupportPlatform = '{{ old('checkout_support_platform', $plan->checkout_support_platform) ?? null }}';
            if (Boolean(checkoutSupportPlatform)) {
                setCheckoutPlatform(checkoutSupportPlatform, checkoutSupport);
            }

            $('#slc-checkout-support-platform').on('change', function () {
                setCheckoutPlatform(this.value);
            });

            const price = '{{ old('price', $plan->price) ?? 0 }}';
            checkMaxInstallments(price);
        });
        $(function () {
            $('#price, #promotional_price').maskMoney({
                decimal: ',',
                thousands: '.',
                precision: 2,
            });
            $('#price, #promotional_price').trigger('mask.maskMoney');

            $('#price').on('blur', function () {
                const price = $(this).val().replace('.', '').replace(',', '.') || 0;
                checkMaxInstallments(price);
            });

            const SPMaskBehavior = function (val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function (val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $('.ipt-phone').mask(SPMaskBehavior, spOptions);
            $('.ipt-country-code').mask('+9000');
        });

        function setCheckoutPlatform(checkoutSupportPlatform, checkoutSupport = null) {
            if (checkoutSupportPlatform === 'jivochat') {
                $('#lbl-checkout-support').html('ID do Jivochat').removeClass('d-none');
                $('#checkout_support').removeClass('d-none');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
            } else if (checkoutSupportPlatform === 'octadesk') {
                $('#lbl-checkout-support').html('ID do Octadesk').removeClass('d-none');
                $('#checkout_support').removeClass('d-none');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').removeClass('d-none');
            } else if (checkoutSupportPlatform === 'mandeumzap') {
                $('#lbl-checkout-support').html('Link do Mande Um Zap').removeClass('d-none');
                $('#checkout_support').removeClass('d-none');
                $('#div-checkout-whatsapp').addClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
            } else if (checkoutSupportPlatform === 'whatsapplink') {
                $('#lbl-checkout-support').html('Link do Whatsapp').addClass('d-none');
                $('#checkout_support').addClass('d-none');
                $('#div-checkout-whatsapp').removeClass('d-none');
                $('#div-checkout-octadesk').addClass('d-none');
                splitWhatsappLink(checkoutSupport);
            }
        }

        function generateWhatsappLink() {
            let countryCode = $('#ipt-checkout-whatsapp-country-code').val();
            let phone = $('#ipt-checkout-whatsapp-phone').val();
            let message = $('#ipt-checkout-whatsapp-message').val();
            if (!countryCode) {
                $('#ipt-checkout-whatsapp-country-code').focus();
                return;
            }

            if (!phone) {
                $('#ipt-checkout-whatsapp-phone').focus();
                return;
            }

            if (!message) {
                $('#ipt-checkout-whatsapp-message').focus();
                return;
            }

            phone = phone.replace(/\D/g, '');
            message = encodeURIComponent(message);
            const link = `https://api.whatsapp.com/send?phone=${countryCode}-${phone}&text=${message}`;
            $('#checkout_support').val(link);
        }


        function splitWhatsappLink(link) {
            if (!link) return;
            const url = new URL(link);
            const params = new URLSearchParams(url.search);
            const [fullPhone = '', message = ''] = [...params.values()];
            const [countryCode = '', phone = ''] = (fullPhone.includes('-')) ? fullPhone.split('-') : ['', fullPhone];
            $('#ipt-checkout-whatsapp-country-code').val(countryCode).addClass('mui--is-not-empty');
            $('#ipt-checkout-whatsapp-phone').val(phone).addClass('mui--is-not-empty');
            $('#ipt-checkout-whatsapp-message').val(message).addClass('mui--is-not-empty');
        }

        function checkMaxInstallments(price) {
            const maxInstallment = Math.floor(price / 5);
            $('#installment option').each(function () {
                const installment = $(this).val();
                $(this).prop('disabled', false);

                if (installment > maxInstallment) {
                    $(this).prop('disabled', true);
                }
            });
        }
    </script>
@endpush

@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/topic_add.css') }}" rel="stylesheet">
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item mx-2"><a href="/plans">Produtos</a></li>
            <li class="breadcrumb-item active mx-2">
                <span>Adicionar/Editar Produto</span>
            </li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link {{ !Request::get('delivery') ? 'active' : '' }}" id="nav-plan-tab" data-bs-toggle="tab" href="#nav-plan"
           role="tab" aria-controls="nav-plan" aria-selected="true">Produto</a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-checkout-tab" data-bs-toggle="tab" href="#nav-checkout"
           role="tab" aria-controls="nav-plan" aria-selected="false">Order bump/Upsell</a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-checkout-config-tab" data-bs-toggle="tab"
           href="#nav-checkout-config" role="tab" aria-controls="nav-plan" aria-selected="false">Configuração
            checkout</a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-greeting-tab" data-bs-toggle="tab" href="#nav-greeting"
           role="tab" aria-controls="nav-plan" aria-selected="false">Mensagem de agradecimento</a>

       @if ($plan->id > 0)
<a class="xgrow-tab-item nav-item nav-link {{ Request::get('delivery') ? 'active' : '' }}" id="nav-delivery-tab" data-bs-toggle="tab" href="#nav-delivery"
              role="tab" aria-controls="nav-plan" aria-selected="false">Entregas</a>
       @endif
    </div>


    @if ($plan->id > 0)
        {!! Form::model($plan, ['method' => 'put', 'enctype' => 'multipart/form-data', 'route' => ['plans.update', 'id' => $plan->id], 'novalidate' => true]) !!}
    @else
        {!! Form::model($plan, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['plans.store'], 'novalidate' => true]) !!}
    @endif
    {{ csrf_field() }}

    <div class="tab-content" id="nav-tabContent">
    @include('elements.alert')
    <!-- Tab Plano -->
    @include('plans._tab-plan')
    <!-- Tab Checkout -->
    @include('plans._tab-checkout')
    <!-- Tab Checkout Config -->
    @include('plans._tab-checkout-config')
    <!-- Tab Mensagem Agradecimento -->
    @include('plans._tab-greeting')
    @include('elements.toast')
    @include('up_image.modal-xgrow', ['restrictAcceptedFormats' => 'image/*'])
    {!! Form::close() !!}

    <!-- Tab Entregas Não usar dentro do Form -->
        @if ($plan->id > 0)
            @include('plans._tab-delivery')
       @endif
    </div>


@endsection
