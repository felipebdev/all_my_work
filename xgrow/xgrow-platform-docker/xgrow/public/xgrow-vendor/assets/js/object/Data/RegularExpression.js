/**
 * @const POST_CODE
 * @type {RegExp}
 * @default /[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}/i
 */
const POST_CODE = /[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}/i;
/**
 * @const CURRENCY
 * @default /^\$?-?(((\d{1,3},?)(\d{3},?)+|\d{1,3})|\d+)(\.\d{1,2})?(\,\d{1,2})?$/
 */
const CURRENCY = /^\$?-?(((\d{1,3},?)(\d{3},?)+|\d{1,3})|\d+)(\.\d{1,2})?(\,\d{1,2})?$/;
/**
 * @const COMMENTS
 * @default /\/\*[\s\S]*?\*\/|\/\/.*\/g
 */
const COMMENTS = /\/\*[\s\S]*?\*\/|\/\/.*/g;
/**
 * @const DIGITS
 * @default /^[0-9]+$/
 */
const DIGITS = /^[0-9]+$/;
/**
 * @const LETTERS
 * @default /^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ]+$/
 */
const LETTERS = /^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ]+$/;
/**
 * @const LETTERS2
 * @default /^[a-z A-Z]+$/
 */
const LETTERS2 = /^[a-z A-Z]+$/;
/**
 * @const ALPHA_NUMERIC
 * @default /^[ a-zA-Z0-9]+$/
 */
const ALPHA_NUMERIC = /^[ a-zA-Z0-9]+$/;
/**
 * @const INTEGER
 * @default /^-?\d+$/
 */
const INTEGER = /^-?\d+$/;
/**
 * @const POSITIVE_INTEGER
 * @default /^\d+$/
 */
const POSITIVE_INTEGER = /^\d+$/;
/**
 * @const NEGATIVE_INTEGER
 */
const NEGATIVE_INTEGER = /^-\d+$/;
/**
 * @const NUMBER
 * @default /^-?\d*\.?\d+$/
 */
const NUMBER = /^-?\d*\.?\d+$/;
/**
 * @const POSITIVE_NUMBER
 * @default /^\d*\.?\d+$/
 */
const POSITIVE_NUMBER = /^\d*\.?\d+$/;
/**
 * @const NEGATIVE_NUMBER
 * @default /^-\d*\.?\d+$/
 */
const NEGATIVE_NUMBER = /^-\d*\.?\d+$/;
/**
 * @const US_CANADIAN_ZIP
 * @default /(^\d{5}(-\d{4})?$)|(^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$)/
 */
const US_CANADIAN_ZIP = /(^\d{5}(-\d{4})?$)|(^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$)/;
/**
 * @const BRAZILIAN_ZIP
 * @default /^[0-9]{5}-[0-9]{3}$/
 */
const BRAZILIAN_ZIP = /^[0-9]{5}-[0-9]{3}$/;
/**
 * @const EMAIL
 * @default /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
 */
const EMAIL = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
/**
 * @const HEX
 * @default /^#?([a-f0-9]{6}|[a-f0-9]{3})$/
 */
const HEX = /^#?([a-f0-9]{6}|[a-f0-9]{3})$/;
/**
 * @const URL
 * @default /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/
 */
const URL = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
/**
 * @const IPV4
 * @default /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/
 */
const IPV4 = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
/**
 * @const IPV6
 * @default /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/
 */
const IPV6 = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/;
/**
 * @class RegularExpression
 */
export default class RegularExpression
{
	constructor() {}

	/**
	 *
	 * @param {string} value
	 * @param {*} pattern
	 * @returns {boolean}
	 */
	static validate(value, pattern)
	{
		return (new RegExp(pattern)).test(value);
	}
}
