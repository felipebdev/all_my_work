import DOMComponent from './DOMComponent.js';
import Button from '../DOM/Button.js';
import Input from '../DOM/Input.js';
import InputPassword from '../DOM/InputPassword.js';
import InputRadio from '../DOM/InputRadio.js';
import InputCheckBox from '../DOM/InputCheckBox.js';
import InputFile from '../DOM/InputFile.js';
import InputCEP from '../DOM/InputCEP.js';
import InputCPF from '../DOM/InputCPF.js';
import InputCNPJ from '../DOM/InputCNPJ.js';
import InputNumber from '../DOM/InputNumber.js';
import InputFloat from '../DOM/InputFloat.js';
import InputDouble from '../DOM/InputDouble.js';
import Select from '../DOM/Select.js';
import Select2 from '../DOM/Select2.js';
import SweetAlert2 from '../DOM/SweetAlert2.js';
import API from '../Net/API.js';
import InputCurrency from './InputCurrency.js';
import InputPhone from './InputPhone.js';
import InputEmail from './InputEmail.js';
import InputURL from './InputURL.js';
import FormAlerts from './FormAlerts.js';
/**
 * @class {AjaxForm}
 */
export default class AjaxForm extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param inputs
	 * @param options
	 */
	constructor(id, options = {})
	{
		super(id, { strictResponse:true, inputOptions:{}, ...options });
		/**
		 *
		 * @type {*[]}
		 * @private
		 */
		this._inputs = [];
		/**
		 *
		 * @type {{}}
		 * @private
		 */
		this._inputsObject = {};
		/**
		 *
		 * @type {*[]}
		 * @private
		 */
		this._groups = [];
		/**
		 *
		 * @private
		 */
		this._action = this.$selector.attr('action');
		/**
		 *
		 * @private
		 */
		this._method = this.$selector.attr('method');
		/**
		 *
		 * @type {API}
		 * @private
		 */
		this._api = new API('', {}, options.strictResponse);
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isSubmitting = false;
		/**
		 *
		 * @type {*|string}
		 * @private
		 */
		this._feedBackType = this.$selector.attr('data-feed-back-type') || '';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._clearAfterCreate = this.$selector.attr('data-clear-after-create') === 'data-clear-after-create';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._clearAfterUpdate = this.$selector.attr('data-clear-after-update') === 'data-clear-after-update';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isGET = this._method === 'get';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isPOST = this._method === 'post';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isPUT = this._method === 'put';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isDELETE = this._method === 'delete';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isPATCH = this._method === 'patch';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isHEAD = this._method === 'head';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isOPTIONS = this._method === 'options';
		/**
		 *
		 * @type {FormAlerts}
		 * @private
		 */
		this._messagesTop = null;
		this.$selector.addClass('clearfix');
		this._$htmlBody = $('html, body');

		this._createByType();
		this._parseInfos();

/*
		this.$selector.find('[data-form-group]').each((i, e) =>
		{
			const
				$element = $(e),
				name = $element.data('name'),
				id = e.id || name,
				inputs = [];

			let
				options = {};

			this._inputs.forEach((e1, i1) => { if(name === e1.groupName) inputs.push(e1); });

			this._groups.push(new InputGroup(`#${id || name}`, name, options, inputs));
		});
*/
		this._submit = new Button(this.$selector.find('button[type="submit"]'));

		this.$selector.on('submit', async (e) =>
		{
			e.preventDefault();
			await this.submit();
		});

		this.schema = this.$selector.data('schema') || 0;

		if(this._inputs.length) this._inputs[0].focus();
	}
	_parseInfos()
	{
		const id = '#' + $($(`[data-target="#${this.id}"]`)[0]).attr('id');

		if($(id).length) this._messagesTop = new FormAlerts(id);
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 * @private
	 */
	_createByType()
	{
		log('Form inputs:');
		log('--------------------------------------------------------');

		this.$selector.find('[data-form-input]').each((i, e) =>
		{
			const
			$element = $(e),
			id = e.id,
			name = e.name,
			idOrName = id || name;

			let
			InputClass = Input,
			options = this.options.inputOptions ? this.options.inputOptions[idOrName] || {} : {};

			switch($element.attr('data-type'))
			{
				case 'password':

					InputClass = InputPassword;

					break;

				case 'radio':

					InputClass = InputRadio;

					break;

				case 'checkbox':

					InputClass = InputCheckBox;

					break;

				case 'email':

					InputClass = InputEmail;

					break;

				case 'file':

					InputClass = InputFile;

					break;

				case 'url':

					InputClass = InputURL;

					break;

				case 'number':

					InputClass = InputNumber;

					break;

				case 'float':

					InputClass = InputFloat;

					break;

				case 'double':

					InputClass = InputDouble;

					break;

				case 'currency':

					InputClass = InputCurrency;

					break;

				case 'phone':

					InputClass = InputPhone;

					break;

				case 'select':

					InputClass = Select;

					break;

				case 'select2':

					InputClass = Select2;

					break;

				case 'cpf':

					InputClass = InputCPF;

					break;

				case 'cnpj':

					InputClass = InputCNPJ;

					break;

				case 'cep':

					if($element.is('select')) InputClass = Select;
					else InputClass = InputCEP;

					break;
			}

			if(InputClass)
			{
				const
					className = String(InputClass).split('{')[0].trim().replace('class', '').split('extends')[0].trim(),
					instance = new InputClass(`#${idOrName}`, name, options);

				log(`${idOrName}: [${className}]`, options);

				this._inputs.push(instance);
				this._inputsObject[idOrName] = instance;


			}
		});

		log('--------------------------------------------------------');

		return this;
	}
	/**
	 *
	 */
	async submit()
	{
		if(this._isSubmitting) return;
		this._isSubmitting = true;

		if(!this.validate())
		{
			this._isSubmitting = false;
			this.enableInputs().toast(SweetAlert2.MESSAGES.validationErrorText);
			return;
		}

		this.disableInputs().hideAllValidationMessages();
		this._submit.showSpinner();

		SweetAlert2.showLoading();

		log('form value');
		log(this.value);

		const
			isFeedBackModal = this._feedBackType === 'modal',
			serverErrorTitle = SweetAlert2.MESSAGES.serverErrorTitle,
			serverErrorText = SweetAlert2.MESSAGES.serverErrorText,
			serverErrorOptions = { showConfirmButton: false, showCloseButton:true, showCancelButton: false };

		let errorInfo = '';

		const
			response = await this._api[this._method](this._action, this.value),
			header = response.header || { status: { code: 500 } },
			success = response.success,
			validation = response.validation.result,
			hasValidation = validation && Object.keys(validation).length,
			body = response.body,
			exception = body ? body.exception : null,
			messages = body ? body.formMessages : null;

		log('success', success);
		log('validation:', hasValidation, validation);
		log('body', body);
		log('messages', messages);
		log('exception', exception);

		if(messages) this._messagesTop.addMessages(messages);

		try
		{
			if(body && exception && exception.errorInfo) errorInfo = `<pre class="error-description">${exception.errorInfo.join('\n')}</pre>`;

			if(success)
			{
				if(isFeedBackModal) this.alertSuccess(null, null, {}, () => { this._$htmlBody.animate({ scrollTop: 0 }, 500); }).trigger({ type: 'success', success: true, error: false, response });

				if((this._clearAfterCreate && this._method === 'post') || (this._clearAfterUpdate && this._method === 'put')) this.clear().trigger({ type: 'clear' });

			}
			else
			{
				if(hasValidation) if(!this.serverValidate(validation)) this.toast(SweetAlert2.MESSAGES.validationErrorText);

				if(!hasValidation && isFeedBackModal) this.alertError().trigger({ type: 'error', success: false, error: true, response });
			}

			if(header.status.code === 500 && isFeedBackModal) this.alertError(serverErrorTitle, `${serverErrorText}${errorInfo}`, serverErrorOptions).trigger({ type: 'error', success: false, error: true });
		}
		catch (e)
		{
			if(isFeedBackModal) this.alertError(serverErrorTitle, `${serverErrorText}${errorInfo}`, serverErrorOptions).trigger({ type: 'error', success: false, error: true });
		}

		this.enableInputs();
		this._submit.showText();

		this._isSubmitting = false;
		SweetAlert2.hideLoading();

		this.trigger({ type: 'response' });

		return this;
	}

	/**
	 *
	 * @param message
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	toast(message, options = { position: 'bottom-end', timer: 7770 }, completeHandler = (result) => { return result; })
	{
		SweetAlert2.toastValidationError(message, options, completeHandler);
		return this;
	}

	/**
	 *
	 * @param icon
	 * @param title
	 * @param text
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	alert(icon, title, text, options = {}, completeHandler = (result) => { return result; })
	{
		SweetAlert2.alert(icon, title, text, options, completeHandler);
		return this;
	}

	/**
	 *
	 * @param title
	 * @param message
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	alertSuccess(title = null, message = null, options = {}, completeHandler = (result) => { return result; })
	{
		SweetAlert2.alertSuccess(title || SweetAlert2.MESSAGES.successTitle, message || SweetAlert2.MESSAGES[this._isPOST ? 'createSuccessText' : 'updateSuccessText'], options, completeHandler);
		return this;
	}
	/**
	 *
	 * @param title
	 * @param message
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	alertError(title = null, message = null, options = {}, completeHandler = (result) => { return result; })
	{
		SweetAlert2.alertError(title || SweetAlert2.MESSAGES.errorTitle, message || SweetAlert2.MESSAGES[this._isPOST ? 'createErrorText' : 'updateErrorText'], options, completeHandler);
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	showAllValidationMessages()
	{
		this._inputs.forEach(input => input.showAllValidationMessages());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	hideAllValidationMessages()
	{
		this._inputs.forEach(input => input.hideAllValidationMessages());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	clear()
	{
		this._inputs.forEach(input => input.clear());
		this.enableInputs();
		return this;
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	enable()
	{
		this.enableInputs().enableSubmit();
		return super.enable();
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	disable()
	{
		this.disableInputs().disableSubmit();
		return super.disable();
	}

	/**
	 *
	 * @returns {AjaxForm}
	 */
	enableInputs()
	{
		this._inputs.forEach(input => input.enable());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	disableInputs()
	{
		this._inputs.forEach(input => input.disable());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	enableSubmit()
	{
		this._submit.enable();
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	disableSubmit()
	{
		this._submit.disable();
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	validate()
	{
		let isMoved = false;

		return this._inputs.map((input) =>
		{
			const validation = input.validate();

			if(!isMoved && !validation)
			{
				isMoved = true;
				input.focus();
			}

			return validation;

		}).indexOf(false) === -1;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	serverValidate(validation)
	{
		return this._inputs.map((input) => { return input.serverValidate(validation[input.id.replace('input-', '')]); }).indexOf(false) === -1;
	}
	/**
	 *
	 * @param id
	 * @returns {null|*}
	 */
	getInputByID(id)
	{
		let i;
		const total = this._inputs.length;

		for(i = 0; i < total; ++i) if(this._inputs[i].id === id) return this._inputs[i];

		return null;
	}
	/**
	 *
	 * @param id
	 * @returns {null|*}
	 */
	getGroupByID(id)
	{
		let i;
		const total = this._groups.length;

		for(i = 0; i < total; ++i) if(this._groups[i].id === id) return this._groups[i];

		return null;
	}
	/**
	 *
	 * @param name
	 * @returns {null|*}
	 */
	getInputByName(name)
	{
		let i;
		const total = this._inputs.length;

		for(i = 0; i < total; ++i) if(this._inputs[i].name === name) return this._inputs[i];

		return null;
	}
	/**
	 *
	 * @returns {*}
	 */
	get action()
	{
		return this._action;
	}
	/**
	 *
	 * @param value
	 */
	set action(value)
	{
		this._action = value;
		this.$selector.attr('action', value);
	}
	/**
	 *
	 * @returns {*}
	 */
	get method()
	{
		return this._method;
	}
	/**
	 *
	 * @param value
	 */
	set method(value)
	{
		this._method = value;
		this.$selector.attr('method', value);
	}
	/**
	 *
	 * @returns {*}
	 */
	get inputs()
	{
		return this._inputs;
	}
	/**
	 *
	 * @returns {*[]}
	 */
	get data()
	{
		return this._inputs.map((input) => { return input.value; });
	}
	/**
	 *
	 * @param value
	 */
	set data(value)
	{
		let i;
		const total = value.length;

		for(i = 0; i < total; ++i) this._inputs[i].value = value[i];
	}
	/**
	 *
	 * @returns {{}}
	 */
	get value()
	{
		const result = {};

		this._inputs.forEach((e, i) => { result[((e.id || e.name) || '').replace('input-', '').replace('#', '')] = e.value; });

		return result;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		let p;
		for(p in value)
		{
			//log(p, '=', value[p]);
			const input = this._inputsObject['input-' + p];
			//log('input', input)
			input.value = value[p];
			input.update();
		}
	}
	/**
	 *
	 * @returns {number}
	 */
	get schema()
	{
		return this._schema;
	}
	/**
	 * Show and hide inputs according the schema number
	 * @param value
	 */
	set schema(value)
	{
		//log('form schema', value)
		this._schema = value;
		this.$selector.attr('data-schema', value);
		this._inputs.forEach(input => input.formSchema = value);
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isSubmitting()
	{
		return this._isSubmitting;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isGET()
	{
		return this._isGET;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isPOST()
	{
		return this._isPOST;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isPUT()
	{
		return this._isPUT;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isDELETE()
	{
		return this._isDELETE;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isPATCH()
	{
		return this._isPATCH;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isHEAD()
	{
		return this._isHEAD;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isOPTIONS()
	{
		return this._isOPTIONS;
	}
}
