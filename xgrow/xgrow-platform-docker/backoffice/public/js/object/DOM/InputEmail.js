import Input from './Input.js';
/**
 * @class {InputCurrency}
 */
export default class InputEmail extends Input
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
	}
}
