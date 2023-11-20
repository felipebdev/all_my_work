'use strict';

import { AjaxForm, Input, Dialog } from './classes.min.js';

window.log = console.log;

console.clear();

const start = () =>
{
	log('>>> platforms started <<<')
	//
	const
		form = new AjaxForm
		(
			'#create-form',
			{
				strictResponse:true,
				inputOptions:
				{
					'slug': { forbiddenKeys: [108] }
				}
			}
		),
		showAlert = () =>
		{
			return Dialog.alertWarning({ html:`<h3>É necessário ter ao menos um cliente cadastrado para prosseguir!</h3>` });
		},
		slugInput = new Input('#slug', null, { forbiddenKeys: [108] }),
		urlInput = new Input('#platform_url');

	let
		_isAuto = urlInput.value === '';

	if(urlInput.value && !slugInput.value) slugInput.value = urlInput.value;

	slugInput.on('input', (e) =>
	{
		if(_isAuto) urlInput.value = `${window.APP_URL_LEARNING_AREA}/${slugInput.value}`
	});

	urlInput.on('input', (e) =>
	{
		_isAuto = false;
	});
/*
	if(!window.existsClient)
	{
		form.disable().on('click', () => { showAlert(); });
		showAlert();
	}

 */
};

$(document).ready(() => { start(); });
