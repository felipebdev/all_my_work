import InputNumber from './InputNumber.js';

/**
 * @class {InputNumber}
 */
export default class InputFloat extends InputNumber
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
		this._punctuation = this.$selector.attr('data-punctuation');
		/**
		 *
		 * @private
		 */
		this._decimals = this.$selector.attr('data-decimals');
	}
}
