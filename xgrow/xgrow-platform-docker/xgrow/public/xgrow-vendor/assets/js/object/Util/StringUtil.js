export default class StringUtil
{
	constructor()
	{

	}

	/**
	 * Set input mask
	 * @param {String} selector
	 * @param {String} mask
	 * @param {Object} options
	 */
	static mask(selector, mask, options = {})
	{
		$(selector).mask(mask, options);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static maskCPF(selector, options = {})
	{
		StringUtil.mask(selector, '999.999.999-99', options);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static maskCNPJ(selector, options = {})
	{
		StringUtil.mask(selector, '99.999.999/9999-99', options);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static maskCEP(selector, options = {})
	{
		StringUtil.mask(selector, '99.999-999', options);
	}

	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static maskPhone(selector, options = {})
	{
		const phoneMask = (val) => { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; };
		StringUtil.mask
		(
			selector,
			phoneMask,
		{ onKeyPress: function (val, e, field, options) { field.mask(phoneMask.apply({}, arguments), options); }}
		);
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static maskNumber(selector, options = {})
	{
		$(selector).on('input', (e) => { $(e.target).val(e.target.value.replace(/[^0-9]/g, '')); });
	}
}
