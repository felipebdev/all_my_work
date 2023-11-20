import Input from './Input.js';
import InputGroup from './InputGroup.js';
/**
 * @class {InputRadio}
 */
export default class InputRadio extends Input
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

		this.$label.on('click', e =>
		{
			e.preventDefault();
			e.stopPropagation();
			$(`[name="${this.name}"]`).removeAttr('checked');
			this.$selector.attr('checked', 'checked').trigger({ type:'click' });
		});

		this.on('mousedown', e =>
		{
			$(`[name="${this.name}"]`).removeAttr('checked');
			this.$selector.attr('checked', 'checked');
		});

		if(this.checked) this.trigger({ type:'click' });
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
		this.$selector.attr('checked', 'checked');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	uncheck()
	{
		this.$selector.removeAttr('checked');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get checked()
	{
		return this.$selector.attr('checked') === 'checked';
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
		if(value) this.check();
		else this.uncheck();
	}
}
