/**
 *
 * @type {string[]}
 */
const MASKS = ['cpf', 'cnpj', 'cep', 'phone', 'slug', 'alphaNumeric', 'currency'];
/**
 * @class {Mask}
 */
export default class Mask
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
	 * @param selector
	 * @param maskName
	 * @param options
	 * @returns {*}
	 */
	static byName(selector, maskName, options = {})
	{
		if(MASKS.indexOf(maskName) === -1) return;
		return Mask[maskName](selector, options);
	}
	/**
	 * Set input mask
	 * @param {String} selector
	 * @param {String} mask
	 * @param {Object} options
	 */
	static mask(selector, mask, options = {})
	{
		return $(selector).mask(mask, options);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static currency(selector, options = {})
	{
		return Mask.mask(selector, '#.##0,00', { reverse: true, ...options });
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static cpf(selector, options = {})
	{
		return Mask.mask(selector, '999.999.999-99', options);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static cnpj(selector, options = {})
	{
		return Mask.mask(selector, '99.999.999/9999-99', options);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static cep(selector, options = {})
	{
		return Mask.mask(selector, '99.999-999', options);
	}

	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static phone(selector, options = {})
	{
		const phoneMask = (val) => { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; };
		return Mask.mask
		(
		selector,
		phoneMask,
		{ onKeyPress: function (val, e, field, options) { field.mask(phoneMask.apply({}, arguments), options); }}
		);
	}
	/**
	 *
	 * @param selector
	 * @returns {*|jQuery}
	 */
	static slug(selector)
	{
		return $(selector).on('input', (e) => { $(e.target).val(e.target.value.replace(/[^a-z0-9\/._-]/gi, '')); });
	}
	/**
	 *
	 * @param selector
	 * @returns {*|jQuery}
	 */
	static alphaNumeric(selector)
	{
		return $(selector).on('input', (e) => { $(e.target).val(e.target.value.replace(/[^a-z0-9]/gi, '')); });
	}

	/**
	 *
	 * @param selector
	 * @param options
	 * @returns {*|jQuery}
	 */
	static number(selector, options = {})
	{
		return $(selector).on('input', (e) => { $(e.target).val(e.target.value.replace(/[^0-9\/.,+\-*:]/g, '')); });
	}

	/**
	 *
	 * @param selector
	 * @param options
	 * @returns {*|jQuery}
	 */
	static strictNumber(selector, options = {})
	{
		return $(selector).on('input', (e) => { $(e.target).val(e.target.value.replace(/[^0-9]/g, '')); });
	}
}
