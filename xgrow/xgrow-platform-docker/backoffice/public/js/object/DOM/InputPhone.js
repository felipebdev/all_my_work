import Input from './Input.js';
/**
 * @class {InputPhone}
 */
export default class InputPhone extends Input
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
		this.$selector.attr('data-validation', 'phone').attr('data-mask', 'phone');
	}
}
