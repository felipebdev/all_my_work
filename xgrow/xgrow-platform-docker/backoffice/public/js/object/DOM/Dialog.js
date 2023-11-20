import StaticEventDispatcher from '../Data/StaticEventDispatcher.js';
/**
 *
 * @type {EventDispatcher}
 */
const
	/**
	 *
	 * @type {{closeButtonText: string, denyButtonText: string, confirmButtonText: string, cancelButtonText: string}}
	 */
	MESSAGE =
	{
		confirm: 'confirmar',
		deny: 'negar',
		cancel: 'cancelar',
		close: 'fechar'
	},
	/**
	 *
	 * @type {{SUCCESS: string, ERROR: string, INFO: string, WARNING: string, QUESTION: string}}
	 */
	ICON =
	{
		'SUCCESS': 'success',
		'ERROR': 'error',
		'WARNING': 'warning',
		'INFO': 'info',
		'QUESTION': 'question'
	},
	/**
	 *
	 * @type {{SUCCESS: string, ERROR: string, INFO: string, WARNING: string, QUESTION: string}}
	 */
	TITLE =
	{
		'SUCCESS': `<h1>sucesso!</h1>`,
		'ERROR': `<h1>erro!</h1>`,
		'WARNING': `<h1>aviso!</h1>`,
		'INFO': `<h1>informação!</h1>`,
		'QUESTION': `<h1>pergunta!</h1>`
	},
	/**
	 *
	 * @type {{closeButtonText: string, denyButtonText: string, confirmButtonText: string, cancelButtonText: string}}
	 */
	DEFAULTS =
	{
		showConfirmButton: true,
		showCloseButton: true,
		showCancelButton: true,
		showDenyButton: true,
		allowEscapeKey:true,
		allowEnterKey:true,
		backdrop: true,
		allowOutsideClick: true,
		confirmButtonText: MESSAGE.confirm,
		denyButtonText: MESSAGE.deny,
		cancelButtonText: MESSAGE.cancel
	},
	/**
	 *
	 * @type {{backdrop: boolean, showCloseButton: boolean, showCancelButton: boolean}}
	 */
	ALERT_DEFAULTS =
	{
		...DEFAULTS,
		showConfirmButton: true,
		showCloseButton: true,
		showCancelButton: false,
		showDenyButton: false,
	},
	/**
	 *
	 * @type {{backdrop: boolean, showCloseButton: boolean, showCancelButton: boolean}}
	 */
	PIN_ALERT_DEFAULTS =
	{
		...ALERT_DEFAULTS,
		showConfirmButton: false,
		showCloseButton: false,
		showCancelButton: false,
		showDenyButton: false,
		allowEscapeKey:false,
		allowEnterKey:false,
		backdrop: true,
		allowOutsideClick: false
	};
/**
 * @class {Dialog}
 */
export default class Dialog extends StaticEventDispatcher
{
	constructor()
	{
		super();
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alert(options = {})
	{
		return Swal.fire({ ...ALERT_DEFAULTS, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertSuccess(options = {})
	{
		return Dialog.alert({ icon:ICON.SUCCESS, title:TITLE.SUCCESS, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertError(options = {})
	{
		return Dialog.alert({ icon:ICON.ERROR, title:TITLE.ERROR, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertWarning(options = {})
	{
		return Dialog.alert({ icon:ICON.WARNING, title:TITLE.WARNING, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertInfo(options = {})
	{
		return Dialog.alert({ icon:ICON.INFO, title:TITLE.INFO, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertQuestion(options = {})
	{
		return Dialog.alert({ icon:ICON.QUESTION, title:TITLE.QUESTION, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertSuccess(options = {})
	{
		return Dialog.alert({ icon:ICON.SUCCESS, title:TITLE.SUCCESS, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertError(options = {})
	{
		return Dialog.alert({ icon:ICON.ERROR, title:TITLE.ERROR, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertWarning(options = {})
	{
		return Dialog.alert({ icon:ICON.WARNING, title:TITLE.WARNING, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertInfo(options = {})
	{
		return Dialog.alert({ icon:ICON.INFO, title:TITLE.INFO, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertQuestion(options = {})
	{
		return Dialog.alert({ icon:ICON.QUESTION, title:TITLE.QUESTION, ...options, ...PIN_ALERT_DEFAULTS });
	}
}
