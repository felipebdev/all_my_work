import Input from './Input.js';
/**
 * @class {InputNumber}
 */
export default class InputCPF extends Input
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

		this.on('input change', e =>
		{
			if(this.value === '.' || this.value === '-' || this.value === '.-' || this.value === '/-' || this.value === './-')
			{
				this.value = '';
				return false;
			}
		});
	}
}
