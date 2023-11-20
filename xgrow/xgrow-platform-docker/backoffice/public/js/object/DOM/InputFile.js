import Input from './Input.js';
/**
 * @class {InputFile}
 */
export default class InputFile extends Input
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
	}
}
