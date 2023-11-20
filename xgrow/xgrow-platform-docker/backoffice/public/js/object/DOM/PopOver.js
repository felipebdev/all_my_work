import DOMComponent from './DOMComponent.js';
/**
 * @class PopOver
 */
export default class PopOver extends DOMComponent
{
	/**
	 *
	 * @param selector
	 * @param options
	 */
	constructor(selector = null, options = {})
	{
		super(selector, options);
		this._$popOver = $(selector).popover(options);
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	show()
	{
		this._$popOver.popover('show');
		return this;
	}
	hide()
	{
		this._$popOver.popover('hide');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	toggle()
	{
		this._$popOver.popover('toggle');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	dispose()
	{
		this._$popOver.popover('dispose');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	enable()
	{
		this._$popOver.popover('enable');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	disable()
	{
		this._$popOver.popover('disable');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	toggleEnabled()
	{
		this._$popOver.popover('toggleEnabled');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	update()
	{
		this._$popOver.popover('update');
		return this;
	}
	/**
	 *
	 * @param selector
	 * @param options
	 * @returns {*|jQuery}
	 */
	static create(selector, options = {})
	{
		return $(selector).popover(options);
	}
}
