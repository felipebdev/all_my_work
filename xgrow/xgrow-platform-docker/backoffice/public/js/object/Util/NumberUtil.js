/**
 * @class {NumberUtil}
 */
export default class NumberUtil
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
	 * @param length
	 * @param decimals
	 * @param punctuation
	 * @returns {string}
	 */
	static format(length, decimals = 2, punctuation = '.')
	{
		let
			maskLength = (length - (decimals +1)),
			maskDecimals = punctuation + Array(decimals).fill('0').join(''),
			mask = '',
			counter = 0;

		maskLength = Math.floor(maskLength + ((maskLength % 3 !== 0) ? 1 : 0));

		if(maskLength > 0)
		{
			while (--maskLength)
			{
				mask += '0';
				if(++counter === 3)
				{
					mask += punctuation;
					counter = 0;
				}
			}
		}

		return mask.split('').reverse().join('') + maskDecimals;
	}
}
