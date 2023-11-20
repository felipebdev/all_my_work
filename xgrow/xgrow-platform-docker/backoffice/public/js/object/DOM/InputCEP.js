import InputNumber from './InputNumber.js';
import CEP from '../Net/Service/CEP.js';

/**
 *
 * @type {{uf: string, logradouro: string, bairro: string, localidade: string}}
 */
const
	/**
	 *
	 * @type {{uf: string, logradouro: string, bairro: string, localidade: string}}
	 */
	FRAGMENTS_MAP =
	{
		'logradouro': 'address',
		'bairro': 'district',
		'localidade': 'city',
		'uf': 'state'
	};

/**
 * @class {InputCEP}
 */
export default class InputCEP extends InputNumber
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, { ...options, ...{ autoMask:false } });

		this._fragments = {};
		let p;

		if(!this.$selector.attr('data-cep-input'))
		{
			this._isCepInput = true;

			$(`[data-cep-input="${this.id}"]`).each((i, e) =>
			{
				const $element = $(e);
				this._fragments[$element.attr('data-fragment')] = $element;
			});

			this.on('keyup input', async e =>
			{
				await this.requestData();
			});

			this.mask(this.dataType);
		}
		else
		{
			this._isCepInput = false;
			for(p in this._fragments) this._fragments[p].val('');
		}
	}
	async update()
	{
		await this.requestData();
		return super.update();
	}
	/**
	 *
	 * @returns {Promise<InputCEP|boolean>}
	 */
	async requestData()
	{
		let p;

		if(this.value === '.' || this.value === '-' || this.value === '.-')
		{
			this.value = '';
			return false;
		}

		const length = (this.value.indexOf('.') > -1 || this.value.indexOf('-') > -1) ? 10 : 8;

		if(this.value.length === length)
		{
			this.showSpinner();
			const cepResult = await CEP.getAddress(this.value);

			if(cepResult)
			{
				for(p in cepResult) if(this._fragments[FRAGMENTS_MAP[p]]) this._fragments[FRAGMENTS_MAP[p]].val(cepResult[p]);

				this.hideSpinner();

				if(Object.keys(cepResult).length && this._fragments['number']) this._fragments['number'].focus();
			}
			else
			{
				this._clearInputs();
				this.hideSpinner();
			}
		}
		else this._clearInputs();

		return this;
	}
	_clearInputs()
	{
		let p;
		for(p in this._fragments) this._fragments[p].val('');
		this.hideSpinner();
		return this;
	}
}
