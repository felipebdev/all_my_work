'use strict';
import CEP from '../../js/object/Net/Service/CEP.js';
import StringUtil from '../../js/object/Util/StringUtil.js';

const log = console.log;

const cepInputInputHandler = async (e) =>
{
	const value = e.target.value;

	$(e.target).attr('disabled', 'disabled');

	const cepData = await CEP.getAddress(value);

	if (cepData)
	{
		if (cepData.logradouro) $('#address').val(cepData.logradouro || '');
		if (cepData.bairro) $('#district').val(cepData.bairro || '');
		if (cepData.localidade) $('#city').val(cepData.localidade || '');
		if (cepData.uf) $('#state').val(cepData.uf || '').trigger('change');
	}

	$(e.target).removeAttr('disabled');
}

const start = () =>
{

	$('input[name=type_person]').click(
	function () {
		if ($(this).val() === 'F') {
			$('#row_cnpj').addClass('d-none');
			$('#row_cpf').removeClass('d-none');
		} else {
			$('#row_cpf').addClass('d-none');
			$('#row_cnpj').removeClass('d-none');
		}
	}
	);

	$('#change_password').click(
	function () {
		if ($(this).prop('checked'))
			$('#row_password').removeClass('d-none');
		else
			$('#row_password').addClass('d-none');
	}
	);

	const taxTransaction = parseFloat($("#tax_transaction").val()).toFixed(2);
	$("#tax_transaction").val(taxTransaction);
	$('#tax_transaction').mask('##0.00', {reverse: true});

	var campo = $(".email");
	campo.keyup(function (e) {
		e.preventDefault();
		campo.val($(this).val().toLowerCase());
	});

	$('#cep-input').on('input', cepInputInputHandler);

	StringUtil.maskCEP('#cep-input');
	StringUtil.maskCNPJ('#cnpj');
	StringUtil.maskCPF('#cpf');
	StringUtil.maskNumber('.number');

	const $approveDocumentCheckbox = $('[name="check_document_status"]');

	$approveDocumentCheckbox.click((e) => { $approveDocumentCheckbox.val($approveDocumentCheckbox.val() === '0' ? 1 : 0); log($approveDocumentCheckbox.val()) });

}

$(document).ready(start);
