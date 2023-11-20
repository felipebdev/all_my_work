/**
 * @class {InputGroup}
 */
import DOMComponent from './DOMComponent.js';
/**
 *
 * @type {{}}
 */
const GROUPS = {};
/**
 * @class {InputGroup}
 */
export default class InputGroup extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 * @param inputs
	 */
	constructor(id, name, options = {}, inputs = [])
	{
		super(id, options);

		this._name = name;
		this._selector = name;
		this._$selector = $(`[data-name="${name}"]`);
		this._$inputs = inputs.length ? inputs : this._$selector.find(`[data-form-input]`);
		this._group = this.$selector.data('name');

		GROUPS[this._group] = this;

		const clickHandler = (e) =>
		{
			e.stopImmediatePropagation();
			e.stopPropagation();

			this.value = $(e.target).val() || $(e.currentTarget).val();
		};

		this._$inputs.forEach((e, i) =>
		{
			$(e).attr('name', `${this._name}[]`).on('change', clickHandler);//.attr('data-group', this._group)
		});

		this._options = options;

	}

	/**
	 *
	 * @param name
	 * @returns {*}
	 */
	static get(name)
	{
		return GROUPS[name];
	}
	/**
	 *
	 * @param input
	 * @returns {InputGroup}
	 */
	select(input)
	{
		const
			inputs = GROUPS[input.groupName].inputs,
			indexOf = inputs.indexOf(input);
			inputs.forEach((e, i) => { log('InputGroup.uncheck', i); e.uncheck(); });

		log('InputGroup.check', indexOf);

		if(indexOf > -1) inputs[indexOf].check();

		return this;
	}
	/**
	 *
	 * @returns {{}}
	 */
	get options()
	{
		return this._options;
	}
	/**
	 *
	 * @returns {jQuery|HTMLElement}
	 */
	get $selector()
	{
		return this._$selector;
	}
	/**
	 *
	 * @returns {*}
	 */
	get inputs()
	{
		return this._$inputs;
	}
	/**
	 *
	 * @returns {*}
	 */
	get group()
	{
		return this._group;
	}
	/**
	 *
	 * @param value
	 */
	set group(value)
	{
		this._group = value;
	}
}
