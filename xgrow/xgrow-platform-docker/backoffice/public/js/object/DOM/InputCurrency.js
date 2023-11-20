import Input from './Input.js';
/**
 * @class {InputCurrency}
 */
export default class InputCurrency extends Input
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
		this.$selector.attr('data-validation', 'currency').attr('data-mask', 'currency');
	}
}
