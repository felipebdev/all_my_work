import Input from './Input.js';
import IconButton from './IconButton.js';
/**
 * @class {InputPassword}
 */
export default class InputPassword extends Input
{
	constructor(id, name = null, options = {})
	{
		super(id, name, options);

		this._iconButton = new IconButton(this.$parent.find('.fa-eye'), this.$parent.find('.fa-eye-slash'));

		this._iconButton.changeCallBack = (e) =>
		{
			this.$selector.attr('type', this.$selector.attr('type') === 'text' ? 'password' : 'text');
		}
	}
}
