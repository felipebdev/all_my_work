/**
 * @class Form
 */
import DOMComponent from './DOMComponent.js';

/**
 * @class {Form}
 */
export default class Form extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param options
	 */
	constructor(id, options = {})
	{
		super(id, options);
		this._autoSubmit = options.autoSubmit || false;
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

		console.log(this._$form.attr('id'));
		console.log(Object.keys(this._$inputs));
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
		//console.log('this._$inputs', this._$inputs);
		let p, validation = [];
		for (p in this._$inputs)
		{
			const $input = $(this._$inputs[p]),
			required = $input[0].hasAttribute('required'),
			name = $input.attr('name'),
			value = $input.val();

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
	}

	submit()
	{
		if(this.options.validate && !this.validate()) return;

		if (!this._autoSubmit)
		{
			$(window).trigger({ type: 'form:submit', value: this.value });
			return;
		}
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
}
