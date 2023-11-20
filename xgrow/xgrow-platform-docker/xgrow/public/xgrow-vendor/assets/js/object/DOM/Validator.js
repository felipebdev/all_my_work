import '../Data/RegularExpression.js';

const RULES =
{
	required:null,
	cpf:null,
	cnpj:null,
	cep:null
};

const MESSAGES =
{
	required:'Este campo é obrigatório',
	cpf:'Insira um cpf válido',
	cnpj:'Insira um cnpj válido',
	cep:'Insira um cep válido'
};

const TEMPLATE = `<div data-name="{{name}}" data-rule="{{rule}}" class="input-validation-message warning" hidden>{{message}}</div>`;

const validationField = (inputName, ruleName) =>
{
	return TEMPLATE.replace('{{name}}', inputName).replace('{{rule}}', ruleName).replace('{{message}}', MESSAGES[ruleName]);
};

/**
 * @class Validator
 */
export default class Validator
{
	/**
	 * Constructor
	 */
	constructor(inputs)
	{
		this._isBuilded = false;
		this._build(inputs);
	}

	/**
	 *
	 * @param inputs
	 * @private
	 */
	_build(inputs)
	{
		if(this._isBuilded) return;
		this._isBuilded = true;

		let p;

		this._$inputs = {};
		this._rules = {};
		this._messages = {};

		for(p in inputs)
		{
			const $input = inputs[p];
/*
			if($input.attr('required') === 'required')
			{
				this._messages['required'] = $(validationField(p, 'required')).appendTo($input.closest('.form-group'));
				this._rules['required'] = [{ name:'required', params:'required' }];
			}
*/
			const rules = ($input.attr('data-validation') || '').split('|');

			for(const rule of rules)
			{
				const [name, params] = rule.split(':');
				this._rules[p] = [];
				if(name)
				{
					this._messages[p] = $(validationField(p, name)).appendTo($input.closest('.form-group'));
					this._rules[p].push({ name, params });
				}
			}

			this._$inputs[p] = $input;
		}

		this._$inputs = inputs;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	validate(rules = [])
	{
		if(!rules.length) this._build(this._$inputs);

		//if(!this._rules || !this._rules.length) return true;

		return this.showMessages(rules.length ? rules : this._rules);
	}
	showMessages(rules = [])
	{
		let rule,
		hasErrors = false;
		for (rule in rules)
		{
			console.log('rule ---=>', rule);
			if(!rules[rule] && this._messages.hasOwnProperty(rule))
			{
				this._messages[rule].fadeOut(0).fadeIn(500).removeAttr('hidden');
				hasErrors = true;
			}
		}

		return hasErrors;
	}
	hideMessages(rules = [])
	{
		let rule;
		for (rule in rules)
		{
			if(this._messages.hasOwnProperty(rule)) this._messages[rule].fadeOut(500);
		}
	}
	/**
	 *
	 * @returns {*[]}
	 */
	get rules()
	{
		return this._rules;
	}
	/**
	 *
	 * @param value
	 */
	set rules(value)
	{
		this._rules = value;
	}
}
