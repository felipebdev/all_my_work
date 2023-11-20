import Input from './Input.js';
import InputRadio from './InputRadio.js';
import Cast from '../Data/Cast.js';
/**
 * @class {InputRadio}
 */
export default class InputCheckBox extends Input
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
		this.value = Cast.boolean(this.$selector.attr('value')) || this.$selector.attr('checked') === 'checked';
		this.on('click', e => { this.toggle(); });
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	toggle()
	{
		if(this.checked) this.uncheck();
		else this.check();

		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	check()
	{
		this.$selector.attr('checked', 'checked').attr('value', '1');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	uncheck()
	{
		this.$selector.removeAttr('checked').attr('value', '0');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get checked()
	{
		return this.$selector.attr('checked') === 'checked' || this.$selector.attr('value') === '1';
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this.checked;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		this._value = Cast.boolean(value);
		if(this._value) this.check();
		else this.uncheck();
	}
}
