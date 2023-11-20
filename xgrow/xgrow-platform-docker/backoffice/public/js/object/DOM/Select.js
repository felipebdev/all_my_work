import Input from './Input.js';
/**
 * @class {Select}
 */
export default class Select extends Input
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, { ...{ autoMask:false }, ...options });
		this._multiple = this.$selector.attr('multiple') === 'multiple';
		if(this.$selector.attr('value')) this.value = this.$selector.attr('value');
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this.$selector.val();
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		this.$selector.val(value).change();
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get multiple()
	{
		return this._multiple;
	}
}
