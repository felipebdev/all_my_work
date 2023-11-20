/**
 * @class {Cast}
 */
export default class Cast
{
	/**
	 * @constructor
	 */
	constructor()
	{
		throw new Error(`${this.constructor.name} must not be instantiated`);
	}
	static boolean(value)
	{
		if(!value || value === false || value === 'false' || value === 0 || value === '0' || value === 'no' || value === 'off') return false;
		return true;
	}
}
