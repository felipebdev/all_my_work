/**
 * @class DynamicObject
 */
export default class DynamicObject
{
	/**
	 *
	 * @param {string|HTMLElement} selector
	 * @param {Object} [options = {}]
	 */
	constructor(selector, options = {})
	{
		/**
		 *
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._selector = selector;
		/**
		 *
		 * @type {Object}
		 * @private
		 */
		this._options = options;
		/**
		 *
		 * @type {Signal}
		 * @private
		 */
		this._change = new signals.Signal();
		/**
		 *
		 * @type {Signal}
		 * @private
		 */
		this._click = new signals.Signal();
		/**
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._$element = $(this._selector);//.on('change', (e) => { this._change.dispatch(this._$element, e); }).on('click', (e) => { this._click.dispatch(this._$element, e); });
	}
	/**
	 * Return dom element(s)
	 * @returns {String}
	 */
	get selector()
	{
		return this._selector;
	}
	/**
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	get $element()
	{
		return this._$element;
	}
	/**
	 * Return options
	 * @returns {Object}
	 */
	get options()
	{
		return this._options;
	}

	/**
	 * Change signal
	 * @returns {Signal}
	 */
	get change()
	{
		return this._change;
	}

	/**
	 * Click signal
	 * @returns {Signal}
	 */
	get click()
	{
		return this._click;
	}
}
