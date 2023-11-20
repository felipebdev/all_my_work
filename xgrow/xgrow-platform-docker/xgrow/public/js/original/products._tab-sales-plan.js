// const pType = @json($product->type);
// const charges = @json($plan->charge_until);

$('#price, #promotional_price').maskMoney({
  decimal: ',',
  thousands: '.',
  precision: 2,
});
$('#price, #promotional_price').trigger('mask.maskMoney');

$('#price').on('blur', function () {
  const price = $(this).val().replace('.', '').replace(',', '.') || 0;
  checkMaxInstallments(price);
  minPrice();
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

function checkMaxInstallments(price) {
  const maxInstallment = Math.floor(price / 5);
  $('#installment option').each(function () {
    const installment = $(this).val();
    if (installment > maxInstallment) {
      $(this).prop('disabled', true);
      $(this).prop('selected', false);
    } else {
      $(this).prop('disabled', false);
      $(this).prop('selected', 'selected');
    }
  });
}

$('#switch-all-payment-type').on('change', function (e) {
  if ($(this).is(':checked')) {
    $('#div-payment-type-sell input[type=checkbox]').prop('checked', true);
  } else {
    $('#div-payment-type-sell input[type=checkbox]').prop('checked', false);
  }
});

function chkTrigger(chkId, divId) {
  const elems = $('#' + divId + ' :input');
  $('#' + chkId).is(':checked') ? elems.removeAttr('disabled').attr('required', 'required') : elems.attr('disabled', true).removeAttr('required');

}

$('#chk-charge-until').change(function () {
  if ($('#chk-charge-until').is(':checked')) {
    $('#divChargeUntil').hide(500);
    $('#charge_until').val(0);
  } else {
    $('#divChargeUntil').show(500);
  }
});

function chkChargeUntil() {
  if (parseInt(charges) === 0) {
    $('#chk-charge-until').prop('checked', true);
  } else {
    $('#chk-charge-until').prop('checked', false);
  }

  if ($('#chk-charge-until').is(':checked')) {
    $('#divChargeUntil').hide(500);
  } else {
    $('#divChargeUntil').show(500);
  }
}

$('#use_promotional_price, #chk-freedays, #chk-charge-until').change(function () {
  chkTrigger('use_promotional_price', 'div-promotional_price');
});

function verifyCHKs() {
  chkTrigger('use_promotional_price', 'div-promotional_price');
  chkChargeUntil();
}

function divDisposes() {
  if (`${pType}` === "P") {
    $('#div-subscription').hide();
    $('#div-test-period').hide();
    $('#div-installment').show();

    $('.payment-single').show();
  } else {
    $('#div-subscription').show();
    $('#div-test-period').show();
    $('#div-installment').hide();

    $('.payment-single').hide();
    $('#payment_method_credit_card').prop('checked', true);
  }
}

$("#frmPlanSale").submit(function (e) {
  e.preventDefault();
  if (!$('#payment_method_credit_card').is(':checked') &&
    !$('#payment_method_boleto').is(':checked') &&
    !$('#payment_method_pix').is(':checked') &&
    !$('#payment_method_multiple_cards').is(':checked') &&
    !$('#unlimited_sale').is(':checked')) {
    errorToast('Erro ao salvar informações', 'Você precisa ao menos selecionar uma forma de pagamento.');
    return false;
  }
  if (minPrice()) {
    return false;
  }
  e.currentTarget.submit();
});

function minPrice() {
  const price = document.getElementById('price');
  const lblPrice = document.getElementById('lblPrice');
  const changePrice = price.value.replace('.', '').replace(',', '.')
  const isMinor = parseFloat(changePrice) < 5;
  if(isMinor){
    lblPrice.classList.remove('d-none');
    errorToast('Verifique!', 'O valor mínimo do produto é de R$5,00.');
  }
  lblPrice.classList.add('d-none');
  return isMinor;
}

function maxInstallments() {
  let price = document.getElementById('price').value;
  price = price.replace('.', '').replace(',', '.') || 0;
  checkMaxInstallments(price);
}

maxInstallments();
verifyCHKs();
divDisposes();
