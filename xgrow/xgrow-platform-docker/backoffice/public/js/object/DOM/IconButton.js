import DOMComponent from './DOMComponent.js';

/**
 * @class {IconButton}
 */
export default class IconButton extends DOMComponent
{
	/**
	 *
	 */
	constructor(icon0, icon1)
	{
		super();

		this._isSwitched = false;

		this._$icon0 = $(icon0).css('cursor', 'pointer');
		this._$icon1 = $(icon1).css('cursor', 'pointer').animate({ opacity:0 }, 0);

		const clickHandler = (e) =>
		{
			const duration = 500;

			if(!this._isSwitched)
			{
				this._$icon0.animate({ opacity:0 }, duration);
				this._$icon1.animate({ opacity:1 }, duration);
			}
			else
			{
				this._$icon0.animate({ opacity:1 }, duration);
				this._$icon1.animate({ opacity:0 }, duration);
			}

			this._isSwitched = !this._isSwitched;

			this.changeCallBack();
		}

		this._$icon0.parent().css('min-width', this._$icon0.parent().width()).css('cursor', 'pointer').on('click', clickHandler);

	}
}
