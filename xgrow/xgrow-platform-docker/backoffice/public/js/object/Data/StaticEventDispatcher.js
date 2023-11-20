import EventDispatcher from './EventDispatcher.js';
/**
 *
 * @type {EventDispatcher}
 * @private
 */
const
	/**
	 *
	 * @type {EventDispatcher}
	 * @private
	 */
	_eventDispatcher = new EventDispatcher();
/**
 * @class {StaticEventDispatcher}
 */
export default class StaticEventDispatcher
{
	/**
	 * @constructor
	 */
	constructor()
	{
		throw new Error(`${this.constructor.name} must not be instantiated`);
	}
	/**
	 *
	 * @returns {*}
	 */
	static on()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.on.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	static one()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.one.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	static off()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.off.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	static trigger()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.trigger.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {Window}
	 */
	static get target()
	{
		return _eventDispatcher.target;
	}

	/**
	 *
	 * @param value
	 */
	static set target(value)
	{
		_eventDispatcher.target = value;
	}

	/**
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	static get $target()
	{
		return _eventDispatcher.$target;
	}

	/**
	 *
	 * @param value
	 */
	static set $target(value)
	{
		_eventDispatcher.$target = value;
	}

	/**
	 *
	 * @returns {boolean}
	 */
	static get interactive()
	{
		return _eventDispatcher.interactive;
	}

	/**
	 *
	 * @param value
	 */
	static set interactive(value)
	{
		_eventDispatcher.interactive = value;
	}
}
