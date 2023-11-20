import Input from './Input.js';
/**
 * @class {InputCurrency}
 */
export default class InputURL extends Input
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
		//this.$selector.attr('data-validation', 'email|unique:email').attr('data-mask', 'email');
	}
	/**
	 *
	 * @returns {string}
	 */
	get value()
	{
		return String(super.value || '').replace('https://', '').replace('http://', '').replace('www.', '')
	}
	set value(value)
	{
		super.value = value;
	}
}
