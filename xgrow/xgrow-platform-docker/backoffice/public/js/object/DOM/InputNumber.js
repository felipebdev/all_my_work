import Input from './Input.js';
import Validator from '../Data/Validator.js';
import ASC2, { NUMBERS_AND_SIGNS } from '../Util/ASC2.js';

/**
 * @class {InputNumber}
 */
export default class InputNumber extends Input
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, options);

		this.on('keydown', e =>
		{
			const
				code = e.keyCode || e.which,
				key = String.fromCharCode(code);

			if(!Validator.number(key) && NUMBERS_AND_SIGNS.indexOf(code) === -1)
			{
				if(!ASC2.checkCutCopyAndPaste(e) && !ASC2.checkSelection(e))
				{
					e.preventDefault();
					e.stopPropagation();

					return false;
				}
			}
		});
	}
}
