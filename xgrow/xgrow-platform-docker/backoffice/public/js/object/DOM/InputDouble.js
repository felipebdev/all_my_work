import InputNumber from './InputNumber.js';
import NumberUtil from '../Util/NumberUtil.js';

/**
 * @class {InputNumber}
 */
export default class InputDouble extends InputNumber
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, options);
		/**
		 *
		 * @private
		 */
		this._punctuation = this.$selector.attr('data-punctuation') || '.';
		/**
		 *
		 * @private
		 */
		this._decimals = Number(this.$selector.attr('data-decimals')) || 2;
		/**
		 * @private
		 */
		this.on('keydown input', e => { this.value = e.target.value; });

		if(this.value && (this.maxLength - (this._decimals +1) > 0))
		{
			this.value += this._punctuation + Array(this._decimals).fill('0').join('');
			//if(this.value.charAt(this.value.length-1) === this._punctuation) this.value = String(this.value).substr(0, this.value.length-1);
		}

		this.$selector.mask(NumberUtil.format(this.maxLength, this._decimals, this._punctuation), { reverse: true });
	}

}
