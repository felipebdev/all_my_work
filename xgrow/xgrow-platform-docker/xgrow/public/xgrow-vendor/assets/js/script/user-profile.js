'use strict';
import UserProfileAPI from '../object/Net/UserProfileAPI.js';
import Uploader from '../object/File/Uploader.js';
import Form from '../object/DOM/Form.js';
import Tabs from '../object/DOM/Tabs.js';
import CEP from '../object/Service/CEP.js';
import StringUtil from '../object/Util/StringUtil.js';

//TODO: Ao ser redirecionado pela primeira vez o usuário verá uma lightbox de feedback dizendo: seu cadastro foi concluído...
const start = () =>
{
	let
	_currentForm = null;

	const
	api = new UserProfileAPI(),
	log = console.log,
	forms =
	{
		'personal-data': new Form('#personal-data-form', false),
		'upload-documents': new Form('#upload-documents-form', false),
		'account-data': new Form('#account-data-form', false),
		'address': new Form('#address-form', false)
	},
	formNames = ['personal-data', 'upload-documents', 'account-data', 'address'],
	tabs = new Tabs('#user-profile-tab'),
	$tabContent = $('#user-profile-tab-content'),
	$inputPersonType = $('#input-person-type'),
	cepInputInputHandler = async (e) =>
	{
		const value = e.target.value;

		$(e.target).attr('disabled', 'disabled');

		const cepData = await CEP.getAddress(value);

		forms['address'].showSubmitSpinner();

		if (cepData)
		{
			if (cepData.logradouro) forms['address'].inputs['address'].val(cepData.logradouro || '');
			if (cepData.bairro) forms['address'].inputs['neighborhood'].val(cepData.bairro || '');
			if (cepData.localidade) forms['address'].inputs['city'].val(cepData.localidade || '');
			if (cepData.uf) forms['address'].inputs['address-state'].val(cepData.uf || '').trigger('change');
		}

		forms['address'].hideSubmitSpinner();
		$(e.target).removeAttr('disabled');
	},
	changeFormsState = (state) =>
	{
		let p;
		for (p in forms) forms[p].state = state;
	},
	getFormsData = () =>
	{
		return { ...forms['personal-data'].value, ...forms['upload-documents'].value, ...forms['account-data'].value, ...forms['address'].value };
	},
	serverValidation = (rules) =>
	{
		let p;
		for (p in forms) forms[p].showValidationMessages(rules);
	},
	submitForms = async () =>
	{
		changeFormsState('awaiting');

		let success = false;

		try
		{
			const response = await api.update(getFormsData());
			const responseData = response.data;
			const validation = responseData.validation;
			const isAllValid = responseData.isAllValid;

			if(!isAllValid)
			{
				serverValidation(validation);
				errorToast('Erro!', `Falha ao atualizar o perfil, favor corrigir os erros apresentados`);

				/*
				switch (tabs.currentIndex)
				{
					case 0:



						break;

					case 1:


						break;

					case 2:


						break;

					case 3:


						break;
				}

				 */
			}
			else
			{
				tabs.next();
				successToast('Sucesso!', "Informações salvas com sucesso!");
				checkIsDone(response);
			}

			changeFormsState('enabled');
			success = true;
		}
		catch (e)
		{
			errorToast('Erro!', `Falha ao atualizar o perfil, tente novamente mais tarde`);
			changeFormsState('enabled');
		}

		update();
	},
	update = () =>
	{
		if(uploader0.val() && uploader1.val()) $('#document-feedback-bar').removeAttr('hidden');
		else $('#document-feedback-bar').attr('hidden', 'hidden');
	},
	updatePersonTypeSelect = (e = null) =>
	{
		const id = Number(forms['personal-data'].inputs['person-type'].val());

		if (id === 2)
		{
			$('.cnpj-content').addClass('hidden');
			$('.cpf-content').removeClass('hidden');
			forms['personal-data'].inputs['fantasy-name'].attr('readonly', 'readonly');
		}
		else
		{
			$('.cnpj-content').removeClass('hidden');
			$('.cpf-content').addClass('hidden');
			forms['personal-data'].inputs['fantasy-name'].removeAttr('readonly');
		}
	},
	Toast = Swal.mixin
	({
		toast: true,
		position: 'bottom-end',
		showConfirmButton: false,
		timer: 3500,
		timerProgressBar: true,
		didOpen: (toast) =>
		{
			toast.addEventListener('mouseenter', Swal.stopTimer);
			toast.addEventListener('mouseleave', Swal.resumeTimer);
		}
	}),
	setUploadStatus = (status, face) =>
	{
		const $feedBack = $(`#upload-${face}-feedback`);

		$feedBack.find('.success').attr('hidden', 'hidden');
		$feedBack.find('.error').attr('hidden', 'hidden');

		if(status !== 'success' && status !== 'error') return;

		$feedBack.find(`.${status}`).removeAttr('hidden');
	},
	uploader0 = new Uploader
	(
		'#input-front-uploader',
		{
			fileRenameFunction: (file) => { return `frente${file.extension}`; },
			urlParam: 'front',
			labelIdle: 'Arraste e solte a frente do documento ou<br /><span class="filepond"> clique aqui e selecione uma imagem do seu dispositivo </span>',
			onAddFile:() =>
			{
				setUploadStatus('iddle', 'front');
			},
			onResponse:(response) =>
			{
				setUploadStatus(response.isValid ? 'success' : 'error', 'front');
				if(!response.isValid) uploader0.removeFiles();

				update();
				checkIsDone(response);
			},
			onError:(error) =>
			{
				log('front error')
				log(error)
			}
		}
	),
	uploader1 = new Uploader
	(
		'#input-back-uploader',
		{
			fileRenameFunction: (file) => { return `verso${file.extension}`; },
			urlParam: 'back',
			labelIdle: 'Arraste e solte o verso do documento ou<br /><span class="filepond"> clique aqui e Selecione uma imagem do seu dispositivo </span>',
			onAddFile:() =>
			{
				setUploadStatus('iddle', 'back');
			},
			onResponse:(response) =>
			{
				//log('back response')
				//log(response);
				setUploadStatus(response.isValid ? 'success' : 'error', 'back');
				if(!response.isValid) uploader1.removeFiles();

				update();

				checkIsDone(response);
			},
			onError:(error) =>
			{
				log('back error')
				log(error)
			}
		}
	),
	checkIsDone = (response) =>
	{
		if(response.registrationComplete)
		{
			$('form').addClass('disabled').attr('disabled', 'disabled');
			window.location.href = '/platforms?register_complete=true';
		}
	};

	$(window).on('form:submit', (e) => { submitForms(); });

	$inputPersonType.on('select2:select', updatePersonTypeSelect);
	updatePersonTypeSelect();

	$('.cep').on('input', cepInputInputHandler);
	$('select.select-2:not(.manual-start, .no-search)').addClass('select2-fix').select2();
	$('select.select-2.no-search').addClass('select2-fix').select2({minimumResultsForSearch: -1});

	StringUtil.maskCEP('.cep');
	StringUtil.maskCNPJ('.cnpj');
	StringUtil.maskCPF('.cpf');
	StringUtil.maskPhone('.phone');
	StringUtil.maskNumber('.number');

	tabs.change.add((currentTab, lastHash) =>
	{
		log('tab change', currentTab, lastHash);
		_currentForm = formNames[currentTab];
	});

	tabs.update();

	log(getFormsData());

	$('#full-screen-spinner').fadeOut(1000);
};

$(document).ready(start);
