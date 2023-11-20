/**
 * @class {EventDispatcher}
 */
export default class EventDispatcher
{
	/**
	 * @constructor
	 * @param target
	 */
	constructor(target = window)
	{
		/**
		 *
		 * @type {Window}
		 * @private
		 */
		this._target = target;
		/**
		 *
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._$target = $(target);
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._interactive = true;
	}
	/**
	 *
	 * @returns {*}
	 */
	on()
	{
		if(!this._interactive) return this;
		this._$target.on.apply(this._$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	one()
	{
		if(!this._interactive) return this;
		this._$target.one.apply(this._$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	off()
	{
		if(!this._interactive) return this;
		this._$target.off.apply(this._$target, [].slice.call(arguments));

		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	trigger()
	{
		if(!this._interactive) return this;
		this._$target.trigger.apply(this._$target, [].slice.call(arguments));

		return this;
	}

	get target()
	{
		return this._target;
	}

	set target(value)
	{
		this._target = value;
	}

	get $target()
	{
		return this._$target;
	}

	set $target(value)
	{
		this._$target = value;
	}

	get interactive()
	{
		return this._interactive;
	}

	set interactive(value)
	{
		this._interactive = value;
	}
}
