import EventDispatcher from '../Data/EventDispatcher.js';
/**
 * @class {DOMComponent}
 */
export default class DOMComponent extends EventDispatcher
{
	/**
	 *
	 * @param {String} selector
	 * @param {Object} [options = {}]
	 */
	constructor(selector = null, options = {})
	{
		super(selector);
		/**
		 *
		 * @type {String}
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
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._$selector = $(selector);
		/**
		 *
		 * @private
		 */
		this._value = this._$selector.val();
		/**
		 *
		 * @private
		 */
		this._id = this._$selector.attr('id');
		/**
		 *
		 * @private
		 */
		this._name = this._$selector.attr('name') || this._$selector.attr('data-name');
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._optional = this.$selector.attr('data-optional') === 'data-optional';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._required = this.$selector.attr('required') === 'required';

		this.changeCallBack = () => {};
		this.$selector.data('instance', this);

		this.addAttributes(options.attributes);
	}
	/**
	 *
	 * @param duration
	 * @returns {DOMComponent}
	 */
	show(duration = 500)
	{
		this.$selector.fadeIn(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {DOMComponent}
	 */
	hide(duration = 500)
	{
		this.$selector.fadeOut(duration);
		return this;
	}
	/**
	 *
	 * @param attributes
	 * @returns {DOMComponent}
	 */
	addAttributes(attributes = null)
	{
		if(!attributes) return this;
		let p;
		for(p in attributes) this.$selector.attr(p, attributes[p]);
		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	get instance()
	{
		return this.$selector.data('instance');
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	enable()
	{
		this._$selector.removeClass('disabled').removeAttr('disabled');
		return this;
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	disable()
	{
		this._$selector.addClass('disabled').attr('disabled');
		return this;
	}

	/**
	 *
	 * @param offset
	 * @returns {DOMComponent}
	 */
	scrollY(offset = 0)
	{
		this.$selector.scrollTop(this.y + offset);
		//window.scroll(0, this.y + this.innerHeight + offset);
		return this;
	}
	/**
	 *
	 * @returns {String}
	 */
	get selector()
	{
		return this._selector;
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
	 * @returns {Object}
	 */
	get options()
	{
		return this._options;
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this._$selector.val();
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		this._value = value;
		this._$selector.val(value);
	}
	/**
	 *
	 * @returns {string}
	 */
	get state()
	{
		return this._state;
	}
	/**
	 *
	 * @param value
	 */
	set state(value)
	{
		this._state = value;
		this._$selector.removeClass('disabled').removeClass('valid').removeClass('invalid').addClass(value);

		if(value === 'enabled') this.enable();
		else if(value === 'disabled') this.disable();
	}
	/**
	 *
	 * @returns {string}
	 */
	get id()
	{
		return String(this._id || '').replace('#', '');
	}
	/**
	 *
	 * @param value
	 */
	set id(value)
	{
		this._id = value;
	}
	/**
	 *
	 * @returns {*}
	 */
	get name()
	{
		return this._name;
	}
	/**
	 *
	 * @param value
	 */
	set name(value)
	{
		this._name = value;
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get interactive()
	{
		return super.interactive;
	}

	/**
	 *
	 * @param value
	 */
	set interactive(value)
	{
		super.interactive = value;
		this.$selector[value ? 'removeClass' : 'addClass']('no-interaction');
	}

	/**
	 *
	 * @returns {*}
	 */
	get x()
	{
		return this.$selector.offset().left;
	}

	/**
	 *
	 * @returns {*}
	 */
	get y()
	{
		return this.$selector.offset().top;
	}

	/**
	 *
	 * @returns {*}
	 */
	get width()
	{
		return this.$selector.width();
	}

	/**
	 *
	 * @returns {*}
	 */
	get height()
	{
		return this.$selector.height();
	}

	/**
	 *
	 * @returns {*}
	 */
	get innerWidth()
	{
		return this.$selector.innerWidth();
	}

	/**
	 *
	 * @returns {*}
	 */
	get innerHeight()
	{
		return this.$selector.innerHeight();
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get optional()
	{
		return this._optional;
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get required()
	{
		return this._required;
	}

	/**
	 * @returns {string}
	 */
	get outerHTML()
	{
		return this.$selector[0].outerHTML;
	}
}
