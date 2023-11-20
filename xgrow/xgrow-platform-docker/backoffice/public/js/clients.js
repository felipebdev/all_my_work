'use strict';

import { AjaxForm } from './classes.min.js';

window.log = console.log;

console.clear();

const start = () =>
{
	log('>>> clients started <<<')

	const
		/**
		 *
		 * @type {Form}
		 */
		form = new AjaxForm('#create-form', { strictResponse:true }),
		inputF = form.getInputByID('input-document-f'),
		inputJ = form.getInputByID('input-document-j'),
		inputCPF = form.getInputByID('input-cpf'),
		inputCNPJ = form.getInputByID('input-cnpj'),
		updateForm = () =>
		{
			if(inputJ.checked)
			{
				form.schema = 1;

				inputCNPJ.enableAllValidation();
				inputCPF.disableAllValidation();
			}
			else
			{
				form.schema = 0;

				inputCNPJ.disableAllValidation();
				inputCPF.enableAllValidation();
			}
		},
		documentClickHandler = (e) =>
		{
			updateForm();
		};

	inputF.on('click', documentClickHandler);
	inputJ.on('click', documentClickHandler);

	updateForm();
/*
	form.value =
	{
		"document-f": true,
		"document-j": "",
		"cpf": "77777777777",
		"cnpj": "",
		"document-approval": true,
		"name": "nome",
		"surname": "sobrenome",
		"email": `email-${Math.floor(Math.random() * 7777777)}@email-${Math.floor(Math.random() * 7777777)}.com`,
		"password": "*tSouza777",
		"company-name": "nome da empresa",
		"fantasy-name": "nome fantasia",
		"company-url": "company.url",
		"cep": "31140410",
		"address": "",
		"address-number": "",
		"address-complement": "",
		"district": "",
		"city": "",
		"state": "",
		"percentage": "1",
		"tax": "1,50",
		"default-discount": "1",
		"bank": "341",
		"agency": "12345",
		"account": "9876543",
		"receiver-code": "",
		"card-message": "lorem ipsum"
	};
*/

};

$(document).ready(() => { start(); });
