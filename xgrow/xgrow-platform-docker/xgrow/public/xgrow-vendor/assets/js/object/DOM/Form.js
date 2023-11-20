import Validator from './Validator.js';
/**
 * @class Form
 */
export default class Form
{
	/**
	 * constructor
	 * @param id
	 * @param autoSubmit
	 */
	constructor(id, autoSubmit = true)
	{
		this._autoSubmit = autoSubmit;
		this._state = 'enabled';

		this._$form = $(id).on('submit', (e) =>
		{
			e.preventDefault();
			e.stopPropagation();
			this.submit();
		});

		this._$inputs = {};

		this._$form.find(`[data-form-input]`).each((i, e) =>
		{
			const $input = $(e);
			this._$inputs[$input.attr('id').replace('input-', '')] = $input;
		});

		this._$submit = this._$form.find('[type="submit"]');

		this._$form.find('.input-validation-message').removeAttr('hidden').fadeOut(0);

		this._validator = new Validator(this._$inputs);

		//console.log(this._$form.attr('id'));
		//console.log(Object.keys(this._$inputs));
	}

	showSubmitSpinner()
	{
		this._$form.find('.submit-spinner').removeClass('hidden');
		this._$form.find('.submit-content').addClass('hidden');
	}

	hideSubmitSpinner()
	{
		this._$form.find('.submit-spinner').addClass('hidden');
		this._$form.find('.submit-content').removeClass('hidden');
	}

	enable()
	{
		this.hideSubmitSpinner();
		this._$form.removeClass('disabled');
	}

	disable()
	{
		this._$form.addClass('disabled');
	}

	awaiting()
	{
		this.disable();
		this.showSubmitSpinner();
	}

	validate()
	{
		/*
		console.log('this._$inputs', this._$inputs);
		let p, validation = [];
		for (p in this._$inputs)
		{
			const $input = $(this._$inputs[p]),
			required = $input[0].hasAttribute('required'),
			name = $input.attr('name').replace('input-', ''),
			value = $input.val();

			//console.log('name', name)

			if(required && !value)
			{
				this._$form.find(`[data-name="${name}"]`).fadeIn(500);
				validation.push(false);
			}
			else
			{
				this._$form.find(`[data-name="${name}"]`).fadeOut(500);
				validation.push(true);
			}
		}

		console.log('validation', validation, validation.indexOf(false));

		return validation.indexOf(false) === -1;
		*/

		//const v = this._validator.validate();
		//console.log('v', v);
		return true;
	}

	submit()
	{
		if(!this.validate()) return;

		if (!this._autoSubmit)
		{
			$(window).trigger({type: 'form:submit', value: this.value});
			return;
		}
	}
	showValidationMessages(rules = [])
	{
		return this._validator.showMessages(rules);
	}
	hideValidationMessages(rules = [])
	{
		return this._validator.hideMessages(rules);
	}
	get value()
	{
		let p,
		value = {};
		for (p in this._$inputs) value[p] = this._$inputs[p].val();
		return value;
	}

	get inputs()
	{
		return this._$inputs;
	}

	get state()
	{
		return this._state;
	}

	set state(state)
	{
		this._state = state;
		if (state === 'enabled') this.enable();
		else if (state === 'disabled') this.disable();
		else if (state === 'awaiting') this.awaiting();
	}

	/**
	 *
	 * @returns {Validator}
	 */
	get validator()
	{
		return this._validator;
	}
}
