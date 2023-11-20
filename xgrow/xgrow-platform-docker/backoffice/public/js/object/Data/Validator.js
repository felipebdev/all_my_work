/**
 * @class {Validator}
 */
export default class Validator
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
	 * @param value
	 * @param rule
	 * @param params
	 * @returns {boolean}
	 */
	static validate(value, rule, params)
	{
		return Validator[rule](value, params);
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static currency(value = '', params = null)
	{
		return !(/[^0-9\/.,]/.test(value));
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static cep(value = '', params = null)
	{
		if(!value || value === '') return false;
		return (value.length === 8 || value.length === 10);
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static cpf(value = '', params = null)
	{
		if(!value || value === '' || (value.length !== 11 && value.length !== 14) || !(/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/.test(value))) return false;

		const cpf = value.replace(/[\s.-]*/igm, '');

		let
			sum = 0,
			rest,
			i;

		if (cpf === '00000000000' || cpf === '11111111111' || cpf === '22222222222' || cpf === '33333333333' || cpf === '44444444444' || cpf === '55555555555' || cpf === '66666666666' || cpf === '77777777777' || cpf === '88888888888' || cpf === '99999999999') return false;

		for (i = 1; i <= 9; ++i) sum = sum + parseInt(cpf.substring(i -1, i)) * (11 - i);

		rest = (sum * 10) % 11;

		if ((rest === 10) || (rest === 11))  rest = 0;

		if (rest !== parseInt(cpf.substring(9, 10)) ) return false;

		sum = 0;

		for (i = 1; i <= 10; ++i) sum += parseInt(cpf.substring(i -1, i)) * (12 - i);

		rest = (sum * 10) % 11;

		if ((rest === 10) || (rest === 11))  rest = 0;
		if (rest !== parseInt(cpf.substring(10, 11) )) return false;

		return true;
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static cnpj(value = '', params = null)
	{
		if(!value || value === '' || (value.length !== 14 && value.length !== 18) || !(/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/.test(value))) return false;

		const cnpj = value.replace(/[^\d]+/g,'');

		if (cnpj === '00000000000000' || cnpj === '11111111111111' || cnpj === '22222222222222' || cnpj === '33333333333333' || cnpj === '44444444444444' || cnpj === '55555555555555' || cnpj === '66666666666666' || cnpj === '77777777777777' || cnpj === '88888888888888' || cnpj === '99999999999999') return false;
		let
			i,
			size = cnpj.length - 2,
			numbers = cnpj.substring(0, size),
			digits = cnpj.substring(size),
			sum = 0,
			pos = size - 7;

		for (i = size; i >= 1; --i)
		{
			sum += numbers.charAt(size - i) * pos--;
			if (pos < 2) pos = 9;
		}

		if (String(sum % 11 < 2 ? 0 : 11 - sum % 11) !== digits.charAt(0)) return false;

		size = size + 1;
		numbers = cnpj.substring(0,size);
		sum = 0;
		pos = size - 7;

		for (i = size; i >= 1; --i)
		{
			sum += numbers.charAt(size - i) * pos--;
			if (pos < 2) pos = 9;
		}

		if (String(sum % 11 < 2 ? 0 : 11 - sum % 11) !== digits.charAt(1)) return false;

		return true;
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static required(value = null, params = null)
	{
		return Boolean(value && value.length);
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static strictNumber(value = '', params = null)
	{
		return !(/[^0-9]/.test(value));
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static number(value = '', params = null)
	{
		if(isNaN(value) || !String(value).length) return false;
		return !(/[^0-9\/.,+\-*:]/.test(value));
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static email(value = '', params = null)
	{
		return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value);
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static length(value = '', params = null)
	{
		return Validator.required(value) && value.length > params -1;
	}
	/**
	 *
	 * @param value
	 * @param params
	 */
	static match(value = '', params = null)
	{
		return (value && params) && (value === params);
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static minchars(value = '', params = null)
	{
		return Validator.required(value) && value.length > params -1;
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static maxchars(value = '', params = null)
	{
		return Validator.required(value) && value.length < params -1;
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static passwordconfirm(value = '', params = null)
	{
		return Validator.required(value) && value === $(params).val();
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean}
	 */
	static atleast(value = null, params = null)
	{
		if(!Array.isArray(value)) return false;
		return value.length >= Number(params);
	}
	/**
	 *
	 * @param value
	 * @param params
	 * @returns {boolean|*}
	 */
	static strongpassword(value = '', params = null)
	{
		const result = Validator.passwordComposition(value, 0);
		return result.lowerCaseLetter && result.upperCaseLetter && result.digit && result.specialCharacter;
	}
	/**
	 *
	 * @param password
	 * @param minimumLength
	 * @returns {{upperCaseLetter: boolean, minimumLength: boolean, length: number, lowerCaseLetter: boolean, digit: boolean, specialCharacter: boolean}|{upperCaseLetter: boolean, minimumLength: boolean, length, lowerCaseLetter: boolean, digit: boolean, specialCharacter: boolean}}
	 */
	static passwordComposition(password, minimumLength = 0)
	{
		if(!password || !password.length) return { lowerCaseLetter: false, upperCaseLetter: false, digit: false, specialCharacter: false, length:0, minimumLength: false };
		return { lowerCaseLetter:/[a-z]/.test(password), upperCaseLetter:/[A-Z]/.test(password), digit:/\d/.test(password), specialCharacter:/[^A-Za-z0-9]/.test(password), length:password.length, minimumLength: minimumLength === 0 ? true : password.length > minimumLength -1 };
	}
	/**
	 *
	 * @param validation
	 * @returns {*[]}
	 */
	static parseRulesByAttribute(validation)
	{
		const result = [];

		validation = validation.split('|');

		for(const validationRule of validation)
		{
			const [rule, params] = validationRule.split(':');
			result.push({ rule: rule.trim(), params: (params || '').trim().split(',') });
		}

		return result;
	}
}
