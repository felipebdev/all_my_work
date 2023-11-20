/**
 *
 * @type {{SUCCESS: string, ERROR: string, INFO: string, WARNING: string, QUESTION: string}}
 */
const ICONS =
{
	'SUCCESS': 'success',
	'ERROR': 'error',
	'WARNING': 'warning',
	'INFO': 'info',
	'QUESTION': 'question'
};
/**
 *
 * @type {{createSuccessText: string, updateErrorText: string, serverErrorText: string, serverErrorTitle: string, errorTitle: string, sendingRequestText: string, successTitle: string, createErrorText: string, validationErrorText: string, requestErrorText: string, updateSuccessText: string}}
 */
const MESSAGES =
{
	successTitle:'<h3>Sucesso</h3>',
	errorTitle:'<h3>Erro</h3>',
	serverErrorTitle:'<h3>Erro de servidor</h3>',
	createSuccessText:'<p>Registro criado com sucesso!</p>',
	updateSuccessText:'<p>Registro atualizado com sucesso!</p>',
	createErrorText:'<p>Falha ao criar o registro, tente novamente mais tarde!</p>',
	updateErrorText:'<p>Falha ao atualizar o registro, tente novamente mais tarde!</p>',
	validationErrorText:'<p>O formulário possui erro (s), favor corrigi-lo (s) antes de prosseguir.</p>',
	requestErrorText:'<p>Erro de servidor, tente novamente mais tarde!</p>',
	sendingRequestText:'<p>Enviando sua solicitação, por favor aguarde...</p>',
	serverErrorText:'<p>Favor informar à equipe de desenvolvedores.</p>',
	warningTitle: '<p>Aviso</p>'
};
/**
 *
 * @type {{iconColor: string, customClass: {popup: string}}}
 */
const COLOR_OPTIONS =
{
	iconColor: 'white',
	customClass: { popup: 'colored-toast' },
}
/**
 * @class {SweetAlert2}
 */
export default class SweetAlert2
{
	constructor()
	{
		throw new Error('SweetAlert2 must not be instantiated');
	}
	/**
	 *
	 * @returns {*}
	 */
	static showLoading(text = SweetAlert2.MESSAGES.sendingRequestText, completeHandler = () => { })
	{
		return Swal.fire
		({
			html: text,
			didOpen: () => { Swal.showLoading() },
			willClose: () => { Swal.hideLoading(); },
			didClose: () => { completeHandler(); },
			icon: 'info',
			showConfirmButton: false,
			allowOutsideClick: false,
			allowEscapeKey: false
		});
	}
	/**
	 *
	 * @returns {*}
	 */
	static hideLoading()
	{
		return Swal.hideLoading();
	}

	/**
	 *
	 * @param icon
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alert(icon, title, html, options = {}, completeHandler = () => { })
	{
		return Swal.fire({ ...options, ...{ icon, title, html, didClose: () => { completeHandler(); } } });
	}

	/**
	 *
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alertSuccess(title = MESSAGES.successTitle, html = MESSAGES.createSuccessText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.alert('success', title, html, options, completeHandler);
	}
	/**
	 *
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alertWarning(title = MESSAGES.warningTitle, html = '', options = {}, completeHandler = () => { })
	{
		return SweetAlert2.alert('error', title, html, options, completeHandler, completeHandler);
	}
	/**
	 *
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alertError(title = MESSAGES.errorTitle, html = MESSAGES.createErrorText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.alert('error', title, html, options, completeHandler, completeHandler);
	}

	/**
	 *
	 * @param icon
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toast(icon, title, options = {}, completeHandler = () => { })
	{
		return Swal.mixin({
			toast: true,
			position: 'bottom-end',
			timer: 5000,
			timerProgressBar: true,
			animation: true,
			showConfirmButton: false,
			showCloseButton:true,
			showCancelButton: false,
			allowEscapeKey: true,
			allowOutsideClick: true,
			didClose: () => { completeHandler(); },
			didOpen: (toast) =>
			{
				toast.addEventListener('mouseenter', Swal.stopTimer)
				toast.addEventListener('mouseleave', Swal.resumeTimer)
			}
		}).fire
		({
			...options,
			...{ icon, title }
		});
	}

	/**
	 *
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toastSuccess(title = MESSAGES.createSuccessText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.toast('success', title, { ...COLOR_OPTIONS, ...options }, completeHandler);
	}

	/**
	 *
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toastError(title = MESSAGES.createErrorText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.toast('error', title, { ...COLOR_OPTIONS, ...options }, completeHandler);
	}

	/**
	 *
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toastValidationError(title = MESSAGES.validationErrorText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.toast('error', title, { ...COLOR_OPTIONS, ...options }, completeHandler);
	}
	/**
	 *
	 * @returns {{errorText: string, errorTitle: string, successTitle: string, successText: string, validationErrorText: string, requestErrorText: string}}
	 * @constructor
	 */
	static get MESSAGES()
	{
		return MESSAGES;
	}
}
