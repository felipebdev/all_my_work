import DOMComponent from './DOMComponent.js';
import PopOver from './PopOver.js';
import Mask from '../Data/Mask.js';
import Validator from '../Data/Validator.js';
import API from '../Net/API.js';

/**
 * @class Input
 */
export default class Input extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, { autoMask:true, forbiddenKeys:[], autoBuild:true, hideValidationMessagesOnFocus: true, validateOnBlur:false, ...options });
		/**
		 * @type {string}
		 * @private
		 */
		this._id = id;
		/**
		 *
		 * @type {string}
		 * @private
		 */
		this._name = String((name || id) || '').replace('#', '');
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isValidationEnabled = true;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isServerValidationEnabled = true;
		/**
		 *
		 * @type {string}
		 * @private
		 */
		this._state = 'idle';
		/**
		 *
		 * @type {Element}
		 * @private
		 */
		this._$parent = this.$selector.closest(this.options.parentSelector || '.input-parent');
		/**
		 *
		 * @type {API}
		 * @private
		 */
		this._api = new API();
		/**
		 *
		 * @type {number}
		 * @private
		 */
		this._formSchema = 0;
		/**
		 *
		 * @private
		 */
		this._$spinner = this.$parent.find(this.options.spinnerSelector || '.input-spinner');
		/**
		 *
		 * @private
		 */
		this._$label = this.$parent.find('label');
		/**
		 *
		 * @type {*|number}
		 */
		this.schema = !isNaN(this.$selector.data('schema')) ? this.$selector.data('schema') : -1;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._hasMask = false;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._validateOnInput = false;
		/**
		 *
		 * @type {null}
		 * @private
		 */
		this._validateOnInputHandler = null;
		/**
		 *
		 * @type {null}
		 * @private
		 */
		this._bufferValue = $(id).attr('value');
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isCleared = false;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isRestored = false;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isBuilt = false;
		/**
		 *
		 * @type {string}
		 * @private
		 */
		this._dataType = 'text';
		/**
		 *
		 * @type {{}}
		 * @private
		 */
		this._dataTypeParams = {};
		/**
		 *
		 * @type {number}
		 * @private
		 */
		this._maxLength = 0;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._maxLengthBadgeEnabled = false;

		const $group = this.$selector.parents('[data-form-group]');

		if($group.length) this.groupName = $($group[0]).attr('data-name');

		this.hideSpinner(0);

		this.addAttributes(this.options.attributes);

		if(this.options.hideValidationMessagesOnFocus) this.$selector.on('keydown mousedown', e => { this.hideAllValidationMessages(); });
		if(this.options.validateOnBlur) this.$selector.on('blur', e => { this.validate(); });

		if(this.options.autoBuild || !this.options.hasOwnProperty('autoBuild')) this.build();
	}
	/**
	 *
	 * @returns {Input}
	 */
	async build()
	{
		if(this._isBuilt) return this;
		this._isBuilt = true;

		this._parsePopOver();
		this._parseMaxLength();
		this._parseClientValidation();
		this._parseServerValidation();
		this._parseForbiddenKeys();
		this._parseDataType();

		await this.update();

		return this.enableMaxLengthBadge();
	}

	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseClientInfos()
	{
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseServerInfos()
	{
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseForbiddenKeys()
	{
		this._forbiddenKeys = this.options.forbiddenKeys;

		if(this._forbiddenKeys && this._forbiddenKeys.length)
		{
			this.on('keydown', e =>
			{
				if(this._forbiddenKeys.indexOf((e.keyCode || e.which)) !== -1)
				{
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
			});
		}

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	parseSchema()
	{
		if(this.schema !== -1) this[this._formSchema === this._schema ? 'show' : 'hide'](0);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parsePopOver()
	{
		const content = this.$selector.data('tip');

		if(content)
		{
			this._popOver = new PopOver
			(
				this.selector,
				{
					content,
					trigger:'manual',
					placement:'top',
					template:`<div class="popover input-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>`
				}
			);

			this.$selector.on('mouseover click focus', e => { this._popOver.show(); });
			this.$selector.on('blur mouseout', e => { this._popOver.hide(); });
		}

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseDataType()
	{
		const
			dataType = this.$selector.data('type').split('(');

		this._dataType = String(dataType[0] || dataType || '').trim();
		this._dataTypeParams = String(dataType[1] || '').trim().replace(')', '');

		if(this.options.autoMask && this._dataType && this._dataType !== 'text') this.mask(this._dataType);

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseMaxLength()
	{
		this._maxLength = Number(this.$selector.attr('maxlength') || 0);

		if(!isNaN(this._maxLength) && this._maxLength > 0)
		{
			this._$maxLengthBadge = this._$parent.find('.maxlength-badge').fadeOut(0);

			this.on('input', (e) =>
			{
				if(!this._maxLengthBadgeEnabled) return;
				this.showMaxLengthBadge();
				clearTimeout(this._badgeTimeOut);
				this._badgeTimeOut = setTimeout(() => { this.hideMaxLengthBadge(); }, 1000);
			});
		}

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	enableMaxLengthBadge()
	{
		this._maxLengthBadgeEnabled = true;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	disableMaxLengthBadge()
	{
		this._$maxLengthBadge.fadeOut(0);
		this._maxLengthBadgeEnabled = false;
		return this;
	}
	/**
	 *
	 * @param validation
	 * @returns {*[]}
	 * @private
	 */
	_parseValidationRules(validation)
	{
		return Validator.parseRulesByAttribute(validation);
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseClientValidation()
	{
		this._validation = [];

		if(this.$selector.data('validation')) this._validation = this._parseValidationRules(this.$selector.data('validation'));

		this._$validationContainer = this.$parent.find('.validation-container.client-validation').slideUp(0);

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseServerValidation()
	{
		this._serverValidation = [];

		if(this.$selector.data('server-validation')) this._serverValidation = this._parseValidationRules(this.$selector.data('server-validation'));

		this._$serverValidationContainer = this.$parent.find('.validation-container.server-validation').slideUp(0);

		return this;
	}
	/**
	 *
	 * @returns {Promise<*>}
	 */
	async update()
	{
		return this.trigger({ type:'input' });
	}
	/**
	 *
	 * @param method
	 * @param url
	 * @param data
	 * @returns {Promise<*>}
	 */
	async request(method, url, data = null)
	{
		const response = await this._api[method](url, data);
		return response.data;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	showSpinner(duration = 500)
	{
		this._$spinner.fadeIn(duration);
		this.disable();
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	hideSpinner(duration = 500)
	{
		this._$spinner.fadeOut(duration);
		this.enable();
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	show(duration = 500)
	{
		this.$parent.fadeIn(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	hide(duration = 500)
	{
		this.$parent.fadeOut(duration);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	enable()
	{
		this.$parent.removeClass('disabled').removeAttr('disabled');
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	disable()
	{
		this.$parent.addClass('disabled').attr('disabled');
		this.$selector.blur();
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	focus()
	{
		this.scrollY().$selector.focus();
		return this;
	}
	/**
	 *
	 * @param name
	 * @param options
	 * @returns {*}
	 */
	mask(name, options = {})
	{
		this._hasMask = true;
		return Mask.byName(this.$selector, name, options);
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	showMaxLengthBadge(duration = 500)
	{
		if((this._maxLength - this.value.length) === 0) return this;
		this._$maxLengthBadge.text(`caracteres restantes: ${this._maxLength - this.value.length}`).fadeIn(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	hideMaxLengthBadge(duration = 500)
	{
		this._$maxLengthBadge.fadeOut(duration);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	showAllValidationMessages()
	{
		return this.showValidationMessages().showServerValidationMessages();
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideAllValidationMessages()
	{
		return this.hideValidationMessages().hideServerValidationMessages();
	}
	/**
	 *
	 * @returns {Input}
	 */
	showValidationMessages()
	{
		this._$validationContainer.slideDown(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideValidationMessages()
	{
		this._$validationContainer.slideUp(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	showServerValidationMessages()
	{
		this._$serverValidationContainer.slideDown(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideServerValidationMessages()
	{
		this._$serverValidationContainer.slideUp(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	showInfos()
	{
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideInfos()
	{
		return this;
	}
	/**
	 *
	 * @param name
	 * @returns {Input}
	 */
	showInfo(name)
	{
		return this;
	}
	/**
	 *
	 * @param name
	 * @returns {Input}
	 */
	hideInfo(name)
	{
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	validate()
	{
		this.hideValidationMessages();

		if(!this._isValidationEnabled || this.isOptional) return true;

		const validationResults = [];

		this._$validationContainer.find('.validation-message').fadeOut(0);

		for(const rule of this._validation)
		{
			try
			{
				const name = rule.rule, currentValidation = Validator[name](this.value, rule.params);
				this._$validationContainer.find(`[data-rule="${name}"]`)[currentValidation ? 'fadeOut' : 'fadeIn'](0);

				validationResults.push(currentValidation);
			}
			catch (e)
			{
				log(e);
				throw new Error(`Validation error, rule: ${rule.rule} not exists`);
			}
		}

		const isValid = validationResults.indexOf(false) === -1;

		this[isValid ? 'hideValidationMessages' : 'showValidationMessages'](500);

		return isValid;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	serverValidate(validation)
	{
		this.hideServerValidationMessages();

		if(!this._isServerValidationEnabled) return true;

		const validationResults = [];

		this._$serverValidationContainer.find('.validation-message').fadeOut(0);

		for(const rule of validation)
		{
			const
			name = rule.rule,
			currentValidation = rule.result;

			this._$serverValidationContainer.find(`[data-rule="${name}"]`)[currentValidation ? 'fadeOut' : 'fadeIn'](0);

			validationResults.push(currentValidation);
		}

		const isValid = validationResults.indexOf(false) === -1;

		this[isValid ? 'hideServerValidationMessages' : 'showServerValidationMessages'](500);

		return isValid;
	}
	/**
	 *
	 * @param restore
	 * @returns {Input}
	 */
	enableAllValidation(restore = true)
	{
		return this.enableValidation(restore).enableServerValidation(restore);
	}
	/**
	 *
	 * @param clear
	 * @returns {Input}
	 */
	disableAllValidation(clear = true)
	{
		return this.disableValidation(clear).disableServerValidation(clear);
	}
	/**
	 *
	 * @param restore
	 * @returns {Input}
	 */
	enableValidation(restore = true)
	{
		if(restore) this.restore();
		this._isValidationEnabled = true;
		return this;
	}
	/**
	 *
	 * @param clear
	 * @returns {Input}
	 */
	disableValidation(clear = true)
	{
		if(clear) this.clear();
		this._isValidationEnabled = false;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	enableServerValidation(restore = true)
	{
		if(restore) this.restore();
		this._isServerValidationEnabled = true;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	disableServerValidation(clear = true)
	{
		if(clear) this.clear();
		this._isServerValidationEnabled = false;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	restore()
	{
		if(this._isRestored) return this;
		this._isRestored = true;
		this.value = this._bufferValue || '';
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	clear()
	{
		if(this._isCleared) return this;
		this._isCleared = true;
		this._bufferValue = this.value;
		this.value = '';
		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return super.value;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		super.value = value ? value.substring(0, this._maxLength || value.length) : value;
	}
	/**
	 *
	 * @returns {*}
	 */
	get $parent()
	{
		return this._$parent;
	}
	/**
	 *
	 * @returns {string}
	 */
	get dataType()
	{
		return this._dataType;
	}
	/**
	 *
	 * @returns {number}
	 */
	get formSchema()
	{
		return this._formSchema;
	}
	/**
	 *
	 * @param value
	 */
	set formSchema(value)
	{
		this._formSchema = value;
		this.parseSchema();
	}
	/**
	 *
	 * @returns {*|number}
	 */
	get schema()
	{
		return this._schema;
	}
	/**
	 *
	 * @param value
	 */
	set schema(value)
	{
		this._schema = value;
		this.parseSchema();
	}
	/**
	 *
	 * @param value
	 */
	set interactive(value)
	{
		super.interactive = value;
		this.$parent[value ? 'removeClass' : 'addClass']('no-interaction');
	}
	/**
	 *
	 * @returns {*}
	 */
	get groupName()
	{
		return this.$selector.attr('data-group');
	}
	/**
	 *
	 * @param value
	 */
	set groupName(value)
	{
		this._groupName = value;
		this.$selector.attr('data-group', value);
	}
	/**
	 *
	 * @returns {*}
	 */
	get $label()
	{
		return this._$label;
	}
	/**
	 *
	 * @param value
	 */
	set $label(value)
	{
		this._$label = value;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get hasMask()
	{
		return this._hasMask;
	}
	/**
	 *
	 * @returns {*}
	 */
	get unformattedValue()
	{
		if(!this._hasMask) return this.value;
		return this.$selector.cleanVal();
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isValidationEnabled()
	{
		return this._isValidationEnabled;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isServerValidationEnabled()
	{
		return this._isServerValidationEnabled;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get validateOnInput()
	{
		return this._validateOnInput;
	}
	/**
	 *
	 * @param value
	 */
	set validateOnInput(value)
	{
		if(this._validateOnInputHandler && value) return;
		else
		{
			this._validateOnInputHandler = (e) =>
			{
				this.validate();
			};

			this.on('keydown input blur focus', this._validateOnInputHandler);
		}

		if(!this._validateOnInputHandler && !value) return;
		else this.off('keydown input blur focus', this._validateOnInputHandler);

		this._validateOnInput = value;
	}
	get isOptional()
	{
		const
			isRequired = this.$selector.attr('required') === 'required',
			isEmpty = this.value === '' || !String(this.value || '').length,
			hasDefaultValue = this.value === this.$selector.attr('value') || this.value === this.$selector.attr('data-value');

		return !isRequired && (isEmpty || hasDefaultValue);
	}
	/**
	 *
	 * @returns {null}
	 */
	get bufferValue()
	{
		return this._bufferValue;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isCleared()
	{
		return this._isCleared;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isRestored()
	{
		return this._isRestored;
	}
	/**
	 *
	 * @returns {API}
	 */
	get api()
	{
		return this._api;
	}
	/**
	 *
	 * @returns {*}
	 */
	get $spinner()
	{
		return this._$spinner;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isBuilt()
	{
		return this._isBuilt;
	}
	/**
	 *
	 * @returns {{}}
	 */
	get dataTypeParams()
	{
		return this._dataTypeParams;
	}

	/**
	 *
	 * @returns {number}
	 */
	get maxLength()
	{
		return this._maxLength;
	}
}
