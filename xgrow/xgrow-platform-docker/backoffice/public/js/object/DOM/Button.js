import DOMComponent from './DOMComponent.js';
/**
 * @class {Button}
 */
export default class Button extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param options
	 */
	constructor(id, options = [])
	{
		super(id, options);

		this._$spanText = this.$selector.find('.button-content');
		this._$spinner = this.$selector.find('.spinner').animate({ opacity:0 }, 0);
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	showSpinner(duration = 500)
	{
		if(this.$selector.innerHeight() > this._$spinner.innerHeight()) this._$spinner.css({ top:(this.$selector.innerHeight() - this._$spinner.innerHeight()) >> 1 });
		this._$spanText.animate({ opacity:0 }, duration);
		this._$spinner.animate({ opacity:1 }, duration);
		this.$selector.attr('data-state', 'spinner');
		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	hideSpinner(duration = 500)
	{
		this._$spinner.animate({ opacity:0 }, duration);
		this._$spanText.animate({ opacity:1 }, duration);
		this.$selector.removeAttr('data-spinner');
		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	showText(duration = 500)
	{
		this._$spanText.animate({ opacity:1 }, duration);
		this._$spinner.animate({ opacity:0 }, duration);
		this.$selector.attr('data-state', 'text');
		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	hideText(duration = 500)
	{
		this._$spanText.animate({ opacity:0 }, duration);
		return this;
	}
}
