/**
 * @class {Cast}
 */
export class Cast
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
/**
 *
 * @type {string[]}
 */
const MASKS = ['cpf', 'cnpj', 'cep', 'phone', 'slug', 'alphaNumeric', 'currency'];
/**
 * @class {Mask}
 */
export class Mask
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
/**
 * @class {Validator}
 */
export class Validator
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
/**
 *
 * @type {number}
 */
export const C = 67;
/**
 *
 * @type {number}
 */
export const V = 86;
/**
 *
 * @type {number}
 */
export const X = 88;
/**
 *
 * @type {number[]}
 */
export const ARROWS = [37, 38, 39, 40];
/**
 *
 * @type {number[]}
 */
export const NUMBERS = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105];
/**
 *
 * @type {number[]}
 */
export const POINT = [108, 190];
/**
 *
 * @type {number[]}
 */
export const PLUS = [43, 107];
/**
 *
 * @type {number[]}
 */
export const MINUS = [109, 173];
/**
 *
 * @type {number[]}
 */
export const COMMA = [44, 110];
/**
 *
 * @type {number}
 */
export const TAB = 9;
/**
 *
 * @type {number}
 */
export const BACKSPACE = 8;
/**
 *
 * @type {number}
 */
export const DELETE = 46;
/**
 *
 * @type {number}
 */
export const ENTER = 13;
/**
 *
 * @type {number}
 */
export const ESC = 27;
/**
 *
 * @type {number}
 */
export const CONTROL = 17;
/**
 *
 * @type {number}
 */
export const ALT = 18;
/**
 *
 * @type {number}
 */
export const SHIFT = 16;
/**
 *
 * @type {number}
 */
export const WINDOWS = 91;
/**
 *
 * @type {number}
 */
export const RIGHT_MOUSE_MENU = 93;
/**
 *
 * @type {number[]}
 */
export const NECESSARY_KEYS = [TAB, BACKSPACE, DELETE, ENTER, ESC, CONTROL, ALT, SHIFT, WINDOWS, RIGHT_MOUSE_MENU, ...ARROWS];
/**
 *
 * @type {(number|number)[]}
 */
export const NUMBERS_AND_SIGNS = [...NUMBERS, ...POINT, ...PLUS, ...MINUS, ...COMMA, ...NECESSARY_KEYS];
/**
 * @class {ASC2}
 */
export class ASC2
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
	 * @param event
	 * @returns {boolean}
	 */
	static checkSelection(event)
	{
		if(!event.ctrlKey && !event.shiftKey) return false;
		return ARROWS.indexOf(event.keyCode || event.which) !== -1;
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCutCopyAndPaste(event)
	{
		return ASC2.checkCut(event) || ASC2.checkCopy(event) || ASC2.checkPaste(event);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCopyAndPaste(event)
	{
		return ASC2.checkCopy(event) || ASC2.checkPaste(event);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCutAndPaste(event)
	{
		return ASC2.checkCut(event) || ASC2.checkPaste(event);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCopy(event)
	{
		if(!event.ctrlKey) return false;
		return (event.keyCode || event.which) === C;
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCut(event)
	{
		if(!event.ctrlKey) return false;
		return (event.keyCode || event.which) === X;
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkPaste(event)
	{
		if(!event.ctrlKey) return false;
		return (event.keyCode || event.which) === V;
	}
}
/**
 * @class {NumberUtil}
 */
export class NumberUtil
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
/**
 *
 * @type {string}
 */
const
	/**
	 *
	 * @type {string}
	 */
	LOCALE = 'pt-br',
	/**
	 *
	 * @type {{style: string, currency: string}}
	 */
	CURRENCY_OPTIONS = { style: 'currency', currency: 'BRL' };
/**
 * @class {StringUtil}
 */
export class StringUtil
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
	 * @param {string} string
	 * @param {string|null} characters
	 * @returns {string}
	 */
	static trim(string, characters= null)
	{
		return string.replace(new RegExp((!characters) ? '^\\s+|\\s+$' : '^' + characters + '+|' + characters + '+$', 'g'), '');
	};
	/**
	 *
	 * @param {string} string
	 * @param {string|null} characters
	 * @returns {string}
	 */
	static rtrim(string, characters= null)
	{
		return string.replace(new RegExp((!characters) ? '\\s+$' : characters + '+$'), '');
	};
	/**
	 *
	 * @param {string} string
	 * @param {string|null} characters
	 * @returns {string}
	 */
	static ltrim(string, characters= null)
	{
		return string.replace(new RegExp((!characters) ? '^\\s+' : '^' + characters + '+'), '');
	};
	/**
	 *
	 * @param value
	 * @returns {string}
	 */
	static sanitize(value = null)
	{
		return String(value || '').trim();
	};

	/**
	 *
	 * @param value
	 * @returns {string}
	 */
	static removeSpecialChars(value = null)
	{
		return String(value || '').replace(/[^a-zA-Z0-9]/g, '');
	};

	/**
	 *
	 * @param value
	 * @returns {string}
	 */
	static sanitizeCurrency(value = null)
	{
		return String(value || '').replaceAll(',', '.');
	};
	/**
	 *
	 * @param date
	 * @returns {string}
	 */
	static dateToMySQLFormat(date = null)
	{
		if (!date) return '';
		date = date.split('/');
		return `${String(date[2] || '').trim()}-${String(date[1] || '').trim()}-${String(date[0] || '').trim()}`;
	};

	/**
	 *
	 * @param date
	 * @returns {string}
	 */
	static dateToPTBRFormat(date = null)
	{
		if (!date) return '';
		date = date.split(' ');
		const days = (date[0] || '').split('-');
		date = String(days[2] || '').trim() + '/' + String(days[1] || '').trim() + '/' + String(days[0] || '').trim();

		return `<span class="date" data-value="${date}">${date}</span>`;
	};

	/**
	 *
	 * @param value
	 * @returns {string}
	 */
	static formatCPF(value = null)
	{
		if (!value) return '';
		value = (value).replace(/[^\d]/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
		return `<span class="document cpf" data-value="${value}">${value}</span>`;
	};

	/**
	 *
	 * @param value
	 * @returns {string}
	 */
	static formatCNPJ(value = null)
	{
		if (!value) return '';
		value = value.replace(/[^\d]/g, '').replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
		return `<span class="document cnpj" data-value="${value}">${value}</span>`;
	};
	/**
	 *
	 * @param value
	 * @returns {string}
	 */
	static formatCurrency(value = null)
	{
		if (!value) return '';
		value = String(value);
		const symbol = value.indexOf('R$') === -1 && value.indexOf('R$ ') === -1 ? '<span class="currency-symbol">R$</span> ' : '';
		value = String(Number(value).toFixed(2)).toLocaleString(LOCALE, CURRENCY_OPTIONS).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
		return '<span class="currency">' + (value ? symbol + `<span class="currency-value" data-value="${value}">${value}</span>` : '') + '</span>';
	};
	/**
	 *
	 * @param string
	 * @returns {string}
	 */
	static slug(string)
	{
		let

		from = 'åàáäâèéëêìíïîòóöôõùúüûñç·/_,:;',
		to = 'aaaaaeeeeiiiiooooouuuunc------';

		let
		i, total = from.length;

		string = string.replace(/<.*?>/g, '').trim().replaceAll(/^\s+|\s+$/g, '').toLowerCase();

		for (i = 0; i < total; ++i) string = string.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));

		return string.replaceAll(/[^a-z0-9 -]/g, '').replaceAll(/\s+/g, '-').replaceAll(/-+/g, '-').trim();
	};
	/**
	 *
	 * @param key
	 * @returns {string}
	 */
	static sprintf(key)
	{
		return sprintfFormat(sprintfParse(key), arguments)
	};
	/**
	 *
	 * @param fmt
	 * @param argv
	 * @returns {string}
	 */
	static vsprintf(fmt, argv)
	{
		return StringUtil.sprintf.apply(null, [fmt].concat(argv || []))
	}
}
const sprintFRegExp =
{
	notString: /[^s]/,
	notBool: /[^t]/,
	notType: /[^T]/,
	notPrimitive: /[^v]/,
	number: /[diefg]/,
	numericArg: /[bcdiefguxX]/,
	json: /[j]/,
	notJson: /[^j]/,
	text: /^[^\x25]+/,
	modulo: /^\x25{2}/,
	placeholder: /^\x25(?:([1-9]\d*)\$|\(([^)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-gijostTuvxX])/,
	key: /^([a-z_][a-z_\d]*)/i,
	keyAccess: /^\.([a-z_][a-z_\d]*)/i,
	indexAccess: /^\[(\d+)\]/,
	sign: /^[+-]/
}

const sprintfFormat = (parseTree, argv) =>
{
	let cursor = 1, treeLength = parseTree.length, arg, output = '', i, k, ph, pad, padCharacter, padLength, isPositive, sign;

	for (i = 0; i < treeLength; i++)
	{
		if (typeof parseTree[i] === 'string') output += parseTree[i];
		else if (typeof parseTree[i] === 'object')
		{
			ph = parseTree[i];
			if (ph.keys)
			{
				arg = argv[cursor];
				for (k = 0; k < ph.keys.length; k++)
				{
					if (arg == undefined) throw new Error(StringUtil.sprintf('[sprintf] Cannot access property "%s" of undefined value "%s"', ph.keys[k], ph.keys[k-1]));
					arg = arg[ph.keys[k]];
				}
			}
			else if (ph.paramNo) arg = argv[ph.paramNo];
			else arg = argv[cursor++];

			if (sprintFRegExp.notType.test(ph.type) && sprintFRegExp.notPrimitive.test(ph.type) && arg instanceof Function) arg = arg();

			if (sprintFRegExp.numericArg.test(ph.type) && (typeof arg !== 'number' && isNaN(arg))) throw new TypeError(StringUtil.sprintf('[sprintf] expecting number but found %T', arg));

			if (sprintFRegExp.number.test(ph.type)) isPositive = arg >= 0;

			switch (ph.type)
			{
				case 'b':
					arg = parseInt(arg, 10).toString(2);
					break
				case 'c':
					arg = String.fromCharCode(parseInt(arg, 10));
					break
				case 'd':
				case 'i':
					arg = parseInt(arg, 10);
					break
				case 'j':
					arg = JSON.stringify(arg, null, ph.width ? parseInt(ph.width) : 0);
					break
				case 'e':
					arg = ph.precision ? parseFloat(arg).toExponential(ph.precision) : parseFloat(arg).toExponential();
					break
				case 'f':
					arg = ph.precision ? parseFloat(arg).toFixed(ph.precision) : parseFloat(arg);
					break
				case 'g':
					arg = ph.precision ? String(Number(arg.toPrecision(ph.precision))) : parseFloat(arg);
					break
				case 'o':
					arg = (parseInt(arg, 10) >>> 0).toString(8);
					break
				case 's':
					arg = String(arg)
					arg = (ph.precision ? arg.substring(0, ph.precision) : arg);
					break
				case 't':
					arg = String(!!arg)
					arg = (ph.precision ? arg.substring(0, ph.precision) : arg);
					break
				case 'T':
					arg = Object.prototype.toString.call(arg).slice(8, -1).toLowerCase();
					arg = (ph.precision ? arg.substring(0, ph.precision) : arg);
					break
				case 'u':
					arg = parseInt(arg, 10) >>> 0;
					break
				case 'v':
					arg = arg.valueOf()
					arg = (ph.precision ? arg.substring(0, ph.precision) : arg);
					break
				case 'x':
					arg = (parseInt(arg, 10) >>> 0).toString(16);
					break
				case 'X':
					arg = (parseInt(arg, 10) >>> 0).toString(16).toUpperCase();
					break
			}
			if (sprintFRegExp.json.test(ph.type)) output += arg;
			else
			{
				if (sprintFRegExp.number.test(ph.type) && (!isPositive || ph.sign))
				{
					sign = isPositive ? '+' : '-'
					arg = arg.toString().replace(sprintFRegExp.sign, '')
				}
				else sign = '';

				padCharacter = ph.padChar ? ph.padChar === '0' ? '0' : ph.padChar.charAt(1) : ' '
				padLength = ph.width - (sign + arg).length
				pad = ph.width ? (padLength > 0 ? padCharacter.repeat(padLength) : '') : ''
				output += ph.align ? sign + arg + pad : (padCharacter === '0' ? sign + pad + arg : pad + sign + arg)
			}
		}
	}
	return output;
}

const sprintfCache = Object.create(null);

const sprintfParse = (fmt) =>
{
	if (sprintfCache[fmt]) return sprintfCache[fmt];

	let _fmt = fmt, match, parseTree = [], argNames = 0;

	while (_fmt)
	{
		if ((match = sprintFRegExp.text.exec(_fmt)) !== null) parseTree.push(match[0]);
		else if ((match = sprintFRegExp.modulo.exec(_fmt)) !== null) parseTree.push('%');
		else if ((match = sprintFRegExp.placeholder.exec(_fmt)) !== null)
		{
			if (match[2])
			{
				argNames |= 1;

				let fieldList = [], replacementField = match[2], fieldMatch = [];

				if ((fieldMatch = sprintFRegExp.key.exec(replacementField)) !== null)
				{
					fieldList.push(fieldMatch[1]);

					while ((replacementField = replacementField.substring(fieldMatch[0].length)) !== '')
					{
						if ((fieldMatch = sprintFRegExp.keyAccess.exec(replacementField)) !== null) fieldList.push(fieldMatch[1]);
						else if ((fieldMatch = sprintFRegExp.indexAccess.exec(replacementField)) !== null) fieldList.push(fieldMatch[1]);
						else throw new SyntaxError('[sprintf] failed to parse named argument key');
					}
				}
				else throw new SyntaxError('[sprintf] failed to parse named argument key');

				match[2] = fieldList;
			}
			else argNames |= 2;

			if (argNames === 3) throw new Error('[sprintf] mixing positional and named placeholders is not (yet) supported')

			parseTree.push({placeholder: match[0], paramNo: match[1], keys: match[2], sign: match[3], padChar: match[4], align: match[5], width: match[6], precision: match[7], type: match[8] });
		}
		else throw new SyntaxError('[sprintf] unexpected placeholder')
		_fmt = _fmt.substring(match[0].length)
	}
	return sprintfCache[fmt] = parseTree
}
/**
 * @class {EventDispatcher}
 */
export class EventDispatcher
{
	/**
	 * @constructor
	 * @param target
	 */
	constructor(target = window)
	{
		/**
		 *
		 * @type {Window}
		 * @private
		 */
		this._target = target;
		/**
		 *
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._$target = $(target);
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._interactive = true;
	}
	/**
	 *
	 * @returns {*}
	 */
	on()
	{
		if(!this._interactive) return this;
		this._$target.on.apply(this._$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	one()
	{
		if(!this._interactive) return this;
		this._$target.one.apply(this._$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	off()
	{
		if(!this._interactive) return this;
		this._$target.off.apply(this._$target, [].slice.call(arguments));

		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	trigger()
	{
		if(!this._interactive) return this;
		this._$target.trigger.apply(this._$target, [].slice.call(arguments));

		return this;
	}

	get target()
	{
		return this._target;
	}

	set target(value)
	{
		this._target = value;
	}

	get $target()
	{
		return this._$target;
	}

	set $target(value)
	{
		this._$target = value;
	}

	get interactive()
	{
		return this._interactive;
	}

	set interactive(value)
	{
		this._interactive = value;
	}
}

/**
 *
 * @type {EventDispatcher}
 * @private
 */
const
	/**
	 *
	 * @type {EventDispatcher}
	 * @private
	 */
	_eventDispatcher = new EventDispatcher();
/**
 * @class {StaticEventDispatcher}
 */
export class StaticEventDispatcher
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
	 * @returns {*}
	 */
	static on()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.on.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	static one()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.one.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {*}
	 */
	static off()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.off.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	static trigger()
	{
		if(!_eventDispatcher.interactive) return this;
		_eventDispatcher.$target.trigger.apply(_eventDispatcher.$target, [].slice.call(arguments));

		return this;
	}

	/**
	 *
	 * @returns {Window}
	 */
	static get target()
	{
		return _eventDispatcher.target;
	}

	/**
	 *
	 * @param value
	 */
	static set target(value)
	{
		_eventDispatcher.target = value;
	}

	/**
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	static get $target()
	{
		return _eventDispatcher.$target;
	}

	/**
	 *
	 * @param value
	 */
	static set $target(value)
	{
		_eventDispatcher.$target = value;
	}

	/**
	 *
	 * @returns {boolean}
	 */
	static get interactive()
	{
		return _eventDispatcher.interactive;
	}

	/**
	 *
	 * @param value
	 */
	static set interactive(value)
	{
		_eventDispatcher.interactive = value;
	}
}
/**
 * @class DynamicObject
 */
export class DynamicObject
{
	/**
	 *
	 * @param {string|HTMLElement} selector
	 * @param {Object} [options = {}]
	 */
	constructor(selector, options = {})
	{
		/**
		 *
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._selector = selector;
		/**
		 *
		 * @type {Object}
		 * @private
		 */
		this._options = options;
		/**
		 *
		 * @type {Signal}
		 * @private
		 */
		this._change = new signals.Signal();
		/**
		 *
		 * @type {Signal}
		 * @private
		 */
		this._click = new signals.Signal();
		/**
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._$element = $(this._selector);//.on('change', (e) => { this._change.dispatch(this._$element, e); }).on('click', (e) => { this._click.dispatch(this._$element, e); });
	}
	/**
	 * Return dom element(s)
	 * @returns {String}
	 */
	get selector()
	{
		return this._selector;
	}
	/**
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	get $element()
	{
		return this._$element;
	}
	/**
	 * Return options
	 * @returns {Object}
	 */
	get options()
	{
		return this._options;
	}

	/**
	 * Change signal
	 * @returns {Signal}
	 */
	get change()
	{
		return this._change;
	}

	/**
	 * Click signal
	 * @returns {Signal}
	 */
	get click()
	{
		return this._click;
	}
}

/**
 * @class {DOMComponent}
 */
export class DOMComponent extends EventDispatcher
{
	/**
	 *
	 * @param {String} selector
	 * @param {Object} [options = {}]
	 */
	constructor(selector = null, options = {})
	{
		super(selector);
		/**
		 *
		 * @type {String}
		 * @private
		 */
		this._selector = selector;
		/**
		 *
		 * @type {Object}
		 * @private
		 */
		this._options = options;
		/**
		 *
		 * @type {*|jQuery|HTMLElement}
		 * @private
		 */
		this._$selector = $(selector);
		/**
		 *
		 * @private
		 */
		this._value = this._$selector.val();
		/**
		 *
		 * @private
		 */
		this._id = this._$selector.attr('id');
		/**
		 *
		 * @private
		 */
		this._name = this._$selector.attr('name') || this._$selector.attr('data-name');
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._optional = this.$selector.attr('data-optional') === 'data-optional';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._required = this.$selector.attr('required') === 'required';

		this.changeCallBack = () => {};
		this.$selector.data('instance', this);

		this.addAttributes(options.attributes);
	}
	/**
	 *
	 * @param duration
	 * @returns {DOMComponent}
	 */
	show(duration = 500)
	{
		this.$selector.fadeIn(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {DOMComponent}
	 */
	hide(duration = 500)
	{
		this.$selector.fadeOut(duration);
		return this;
	}
	/**
	 *
	 * @param attributes
	 * @returns {DOMComponent}
	 */
	addAttributes(attributes = null)
	{
		if(!attributes) return this;
		let p;
		for(p in attributes) this.$selector.attr(p, attributes[p]);
		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	get instance()
	{
		return this.$selector.data('instance');
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	enable()
	{
		this._$selector.removeClass('disabled').removeAttr('disabled');
		return this;
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	disable()
	{
		this._$selector.addClass('disabled').attr('disabled');
		return this;
	}

	/**
	 *
	 * @param offset
	 * @returns {DOMComponent}
	 */
	scrollY(offset = 0)
	{
		this.$selector.scrollTop(this.y + offset);
		//window.scroll(0, this.y + this.innerHeight + offset);
		return this;
	}
	/**
	 *
	 * @returns {String}
	 */
	get selector()
	{
		return this._selector;
	}
	/**
	 *
	 * @returns {jQuery|HTMLElement}
	 */
	get $selector()
	{
		return this._$selector;
	}
	/**
	 *
	 * @returns {Object}
	 */
	get options()
	{
		return this._options;
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this._$selector.val();
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		this._value = value;
		this._$selector.val(value);
	}
	/**
	 *
	 * @returns {string}
	 */
	get state()
	{
		return this._state;
	}
	/**
	 *
	 * @param value
	 */
	set state(value)
	{
		this._state = value;
		this._$selector.removeClass('disabled').removeClass('valid').removeClass('invalid').addClass(value);

		if(value === 'enabled') this.enable();
		else if(value === 'disabled') this.disable();
	}
	/**
	 *
	 * @returns {string}
	 */
	get id()
	{
		return String(this._id || '').replace('#', '');
	}
	/**
	 *
	 * @param value
	 */
	set id(value)
	{
		this._id = value;
	}
	/**
	 *
	 * @returns {*}
	 */
	get name()
	{
		return this._name;
	}
	/**
	 *
	 * @param value
	 */
	set name(value)
	{
		this._name = value;
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get interactive()
	{
		return super.interactive;
	}

	/**
	 *
	 * @param value
	 */
	set interactive(value)
	{
		super.interactive = value;
		this.$selector[value ? 'removeClass' : 'addClass']('no-interaction');
	}

	/**
	 *
	 * @returns {*}
	 */
	get x()
	{
		return this.$selector.offset().left;
	}

	/**
	 *
	 * @returns {*}
	 */
	get y()
	{
		return this.$selector.offset().top;
	}

	/**
	 *
	 * @returns {*}
	 */
	get width()
	{
		return this.$selector.width();
	}

	/**
	 *
	 * @returns {*}
	 */
	get height()
	{
		return this.$selector.height();
	}

	/**
	 *
	 * @returns {*}
	 */
	get innerWidth()
	{
		return this.$selector.innerWidth();
	}

	/**
	 *
	 * @returns {*}
	 */
	get innerHeight()
	{
		return this.$selector.innerHeight();
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get optional()
	{
		return this._optional;
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get required()
	{
		return this._required;
	}

	/**
	 * @returns {string}
	 */
	get outerHTML()
	{
		return this.$selector[0].outerHTML;
	}
}
/**
 * @class {API}
 */


export class API
{
	/**
	 *
	 * @param baseURL
	 * @param headers
	 * @param strictResponse
	 */
	constructor(baseURL = '/api', headers = {}, strictResponse = false)
	{
		//const token = { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') };

		/**
		 * Base url for all requests
		 * @type {string}
		 */
		this.baseURL = baseURL;
		/**
		 * Axios instance
		 * @type {AxiosInstance}
		 * @private
		 */
		this._axios = axios.create({ baseURL });
		/**
		 * Axios default headers
		 * @type {{post: {'Content-Type': string}, 'X-Requested-With': string, 'Access-Control-Allow-Origin': string, 'Content-Type': string}}
		 */
		this._axios.defaults.headers =
		{
			/* post: { 'Content-Type': 'application/x-www-form-urlencoded' }, */
			'Access-Control-Allow-Origin': '*',
			'X-Requested-With': 'xmlhttprequest',
			'Content-Type': 'application/json; charset=utf-8',
			...headers
		};
		this._strictResponse = strictResponse;
	}

	/**
	 * Request
	 * @param method
	 * @param url
	 * @param data
	 * @returns {AxiosPromise}
	 */
	async request(method, url, data = {})
	{
		if(!this._strictResponse) return this._axios.request({ method, url, data });

		try
		{
			const response = await this._axios.request({ method, url, data });
			return new HTTPResponse(response);
		}
		catch (e)
		{
			return null;
		}
	}

	/**
	 * get request
	 * @param {string} url
	 * @returns {Promise<AxiosResponse<any>>}
	 */
	get(url)
	{
		return this.request('get', `${this.baseURL}${url}`);
	}

	/**
	 * post request
	 * @param {string} url
	 * @param {{}} data
	 * @returns {Promise<AxiosResponse<any>>}
	 */
	post(url, data)
	{
		return this.request('post', `${this.baseURL}${url}`, data);
	}

	/**
	 * put request
	 * @param {string} url
	 * @param {{}} data
	 * @returns {Promise<AxiosResponse<any>>}
	 */
	put(url, data)
	{
		return this.request('put', `${this.baseURL}${url}`, data);
	}

	/**
	 * delete request
	 * @param {string} url
	 * @returns {Promise<AxiosResponse<any>>}
	 */
	delete(url)
	{
		return this.request('delete', `${this.baseURL}${url}`);
	}

	/**
	 * options request
	 * @param {string} url
	 * @returns {Promise<AxiosResponse<any>>}
	 */
	options(url)
	{
		return this.request('options', `${this.baseURL}${url}`);
	}

	/**
	 * patch request
	 * @param url
	 * @param data
	 * @returns {AxiosPromise}
	 */
	patch(url, data)
	{
		return this.request('patch', `${this.baseURL}${url}`, data);
	}

	/**
	 * head request
	 * @param {string} url
	 * @returns {Promise<AxiosResponse<any>>}
	 */
	head(url)
	{
		return this.request('head', `${this.baseURL}${url}`);
	}

	/**
	 * Get headers
	 * @returns {*}
	 */
	get headers()
	{
		return this._axios.defaults.headers;
	}

	/**
	 * Get axios instance
	 * @returns {*}
	 */
	get axios()
	{
		return this._axios;
	}
}

/**
 * @class BackOfficeAPI
 */
export class BackOfficeAPI extends API
{
	/**
	 * @constructor
	 */
	constructor()
	{
		super('');
	}
	/**
	 * Retrieve all clients datas by given ids.
	 * @param clientIDS
	 */
	getPlatformsAndProductsByClient(clientIDS)
	{
		return this.patch(`/client-transactions/client-data`, { data:clientIDS });
	}
	/**
	 * Retrieve audit data
	 * @param {Number} id
	 * @param {String} dateStart
	 * @param {String} dateEnd
	 * @param {String} columnID
	 * @returns {Promise<AxiosResponse<*>>}
	 */
	getAuditData = (id, dateStart, dateEnd, columnID) =>
	{
		return this.get(`/audit?id=${id}&date-start=${dateStart}&date-end=${dateEnd}&column-id=${columnID}`);
	}
}
/**
 * @class {HTTPResponse}
 */
export class HTTPResponse
{
	/**
	 *
	 * @param {Object} response
	 */
	constructor(response)
	{
		this._response = response;
		this._config = response.config;
		this._headers = response.headers;
		this._request = response.request;
		this._status = this._request.status;
		this._statusText = this._request.statusText;
		this._header = response.data.header;
		this._body = response.data.body;
		this._validation = this._header.validation;
		this._validationSuccess = this._validation.success;
		this._success = this._header.success;
		this._error = this._header.error;
	}
	/**
	 *
	 * @returns {{success: (*|boolean), statusText, header, validationSuccess, body, error: (*|boolean), validation, status}}
	 */
	toObject()
	{
		return {
			'status': this._status,
			'statusText': this._statusText,
			'header': this._header,
			'body': this._body,
			'validation': this._validation,
			'validationSuccess': this._validationSuccess,
			'success': this._success,
			'error': this._error
		};
	}
	toString()
	{
		return `status: ${this._status}, statusText: ${this._statusText}, success: ${this._success}, error: ${this._error}, validation success: ${this._validationSuccess}, header: ${this._header}, body: ${this._body}`;
	}
	/**
	 *
	 * @returns {Object}
	 */
	get response()
	{
		return this._response;
	}
	/**
	 *
	 * @returns {*|{}}
	 */
	get headers()
	{
		return this._headers;
	}
	/**
	 *
	 * @returns {*|{}}
	 */
	get request()
	{
		return this._request;
	}
	/**
	 *
	 * @returns {number}
	 */
	get status()
	{
		return this._status;
	}
	/**
	 *
	 * @returns {string}
	 */
	get statusText()
	{
		return this._statusText;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get success()
	{
		return this._success;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get error()
	{
		return this._error;
	}
	/**
	 *
	 * @returns {[]}
	 */
	get validation()
	{
		return this._validation;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get validationSuccess()
	{
		return this._validationSuccess;
	}
	/**
	 *
	 * @returns {*|{}}
	 */
	get config()
	{
		return this._config;
	}
	/**
	 *
	 * @returns {*}
	 */
	get header()
	{
		return this._header;
	}
	/**
	 *
	 * @returns {*}
	 */
	get body()
	{
		return this._body;
	}
}
/**
 * @class CEP
 */

/**
 * Service url
 * @type {string}
 */
const SERVICE_CEP_URL = `https://viacep.com.br/ws`;

/**
 * @class {CEP}
 */
export class CEP
{
	constructor()
	{

	}

	/**
	 *
	 * @param cep
	 * @returns {Promise<boolean|any>}
	 */
	static async getAddress (cep = null)
	{
		const url = `${SERVICE_CEP_URL}/${cep.replace(/[^0-9]/g, '')}/json/`;

		if(!Validator.cep(cep) || url === `${SERVICE_CEP_URL}//json/`) return false;

		const result = await fetch(url);
		return result.json();
	}
}
/**
 * Service url
 * @type {string}
 */
const SERVICE_RECEITA_FEDERAL_URL = ``;
/**
 * @class {ReceitaFederal}
 */
export class ReceitaFederal
{
	constructor()
	{

	}
	static getCPF()
	{

	}
	static getCNPJ()
	{

	}
}






/**
 * @class Input
 */
export class Input extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, { autoMask:true, forbiddenKeys:[], autoBuild:true, hideValidationMessagesOnFocus: true, validateOnBlur:false, ...options });
		/**
		 * @type {string}
		 * @private
		 */
		this._id = id;
		/**
		 *
		 * @type {string}
		 * @private
		 */
		this._name = String((name || id) || '').replace('#', '');
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isValidationEnabled = true;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isServerValidationEnabled = true;
		/**
		 *
		 * @type {string}
		 * @private
		 */
		this._state = 'idle';
		/**
		 *
		 * @type {Element}
		 * @private
		 */
		this._$parent = this.$selector.closest(this.options.parentSelector || '.input-parent');
		/**
		 *
		 * @type {API}
		 * @private
		 */
		this._api = new API();
		/**
		 *
		 * @type {number}
		 * @private
		 */
		this._formSchema = 0;
		/**
		 *
		 * @private
		 */
		this._$spinner = this.$parent.find(this.options.spinnerSelector || '.input-spinner');
		/**
		 *
		 * @private
		 */
		this._$label = this.$parent.find('label');
		/**
		 *
		 * @type {*|number}
		 */
		this.schema = !isNaN(this.$selector.data('schema')) ? this.$selector.data('schema') : -1;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._hasMask = false;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._validateOnInput = false;
		/**
		 *
		 * @type {null}
		 * @private
		 */
		this._validateOnInputHandler = null;
		/**
		 *
		 * @type {null}
		 * @private
		 */
		this._bufferValue = $(id).attr('value');
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isCleared = false;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isRestored = false;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isBuilt = false;
		/**
		 *
		 * @type {string}
		 * @private
		 */
		this._dataType = 'text';
		/**
		 *
		 * @type {{}}
		 * @private
		 */
		this._dataTypeParams = {};
		/**
		 *
		 * @type {number}
		 * @private
		 */
		this._maxLength = 0;
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._maxLengthBadgeEnabled = false;

		const $group = this.$selector.parents('[data-form-group]');

		if($group.length) this.groupName = $($group[0]).attr('data-name');

		this.hideSpinner(0);

		this.addAttributes(this.options.attributes);

		if(this.options.hideValidationMessagesOnFocus) this.$selector.on('keydown mousedown', e => { this.hideAllValidationMessages(); });
		if(this.options.validateOnBlur) this.$selector.on('blur', e => { this.validate(); });

		if(this.options.autoBuild || !this.options.hasOwnProperty('autoBuild')) this.build();
	}
	/**
	 *
	 * @returns {Input}
	 */
	async build()
	{
		if(this._isBuilt) return this;
		this._isBuilt = true;

		this._parsePopOver();
		this._parseMaxLength();
		this._parseClientValidation();
		this._parseServerValidation();
		this._parseForbiddenKeys();
		this._parseDataType();

		await this.update();

		return this.enableMaxLengthBadge();
	}

	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseClientInfos()
	{
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseServerInfos()
	{
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseForbiddenKeys()
	{
		this._forbiddenKeys = this.options.forbiddenKeys;

		if(this._forbiddenKeys && this._forbiddenKeys.length)
		{
			this.on('keydown', e =>
			{
				if(this._forbiddenKeys.indexOf((e.keyCode || e.which)) !== -1)
				{
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
			});
		}

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	parseSchema()
	{
		if(this.schema !== -1) this[this._formSchema === this._schema ? 'show' : 'hide'](0);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parsePopOver()
	{
		const content = this.$selector.data('tip');

		if(content)
		{
			this._popOver = new PopOver
			(
				this.selector,
				{
					content,
					trigger:'manual',
					placement:'top',
					template:`<div class="popover input-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>`
				}
			);

			this.$selector.on('mouseover click focus', e => { this._popOver.show(); });
			this.$selector.on('blur mouseout', e => { this._popOver.hide(); });
		}

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseDataType()
	{
		const
			dataType = this.$selector.data('type').split('(');

		this._dataType = String(dataType[0] || dataType || '').trim();
		this._dataTypeParams = String(dataType[1] || '').trim().replace(')', '');

		if(this.options.autoMask && this._dataType && this._dataType !== 'text') this.mask(this._dataType);

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseMaxLength()
	{
		this._maxLength = Number(this.$selector.attr('maxlength') || 0);

		if(!isNaN(this._maxLength) && this._maxLength > 0)
		{
			this._$maxLengthBadge = this._$parent.find('.maxlength-badge').fadeOut(0);

			this.on('input', (e) =>
			{
				if(!this._maxLengthBadgeEnabled) return;
				this.showMaxLengthBadge();
				clearTimeout(this._badgeTimeOut);
				this._badgeTimeOut = setTimeout(() => { this.hideMaxLengthBadge(); }, 1000);
			});
		}

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	enableMaxLengthBadge()
	{
		this._maxLengthBadgeEnabled = true;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	disableMaxLengthBadge()
	{
		this._$maxLengthBadge.fadeOut(0);
		this._maxLengthBadgeEnabled = false;
		return this;
	}
	/**
	 *
	 * @param validation
	 * @returns {*[]}
	 * @private
	 */
	_parseValidationRules(validation)
	{
		return Validator.parseRulesByAttribute(validation);
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseClientValidation()
	{
		this._validation = [];

		if(this.$selector.data('validation')) this._validation = this._parseValidationRules(this.$selector.data('validation'));

		this._$validationContainer = this.$parent.find('.validation-container.client-validation').slideUp(0);

		return this;
	}
	/**
	 *
	 * @returns {Input}
	 * @private
	 */
	_parseServerValidation()
	{
		this._serverValidation = [];

		if(this.$selector.data('server-validation')) this._serverValidation = this._parseValidationRules(this.$selector.data('server-validation'));

		this._$serverValidationContainer = this.$parent.find('.validation-container.server-validation').slideUp(0);

		return this;
	}
	/**
	 *
	 * @returns {Promise<*>}
	 */
	async update()
	{
		return this.trigger({ type:'input' });
	}
	/**
	 *
	 * @param method
	 * @param url
	 * @param data
	 * @returns {Promise<*>}
	 */
	async request(method, url, data = null)
	{
		const response = await this._api[method](url, data);
		return response.data;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	showSpinner(duration = 500)
	{
		this._$spinner.fadeIn(duration);
		this.disable();
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	hideSpinner(duration = 500)
	{
		this._$spinner.fadeOut(duration);
		this.enable();
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	show(duration = 500)
	{
		this.$parent.fadeIn(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	hide(duration = 500)
	{
		this.$parent.fadeOut(duration);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	enable()
	{
		this.$parent.removeClass('disabled').removeAttr('disabled');
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	disable()
	{
		this.$parent.addClass('disabled').attr('disabled');
		this.$selector.blur();
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	focus()
	{
		this.scrollY().$selector.focus();
		return this;
	}
	/**
	 *
	 * @param name
	 * @param options
	 * @returns {*}
	 */
	mask(name, options = {})
	{
		this._hasMask = true;
		return Mask.byName(this.$selector, name, options);
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	showMaxLengthBadge(duration = 500)
	{
		if((this._maxLength - this.value.length) === 0) return this;
		this._$maxLengthBadge.text(`caracteres restantes: ${this._maxLength - this.value.length}`).fadeIn(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {Input}
	 */
	hideMaxLengthBadge(duration = 500)
	{
		this._$maxLengthBadge.fadeOut(duration);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	showAllValidationMessages()
	{
		return this.showValidationMessages().showServerValidationMessages();
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideAllValidationMessages()
	{
		return this.hideValidationMessages().hideServerValidationMessages();
	}
	/**
	 *
	 * @returns {Input}
	 */
	showValidationMessages()
	{
		this._$validationContainer.slideDown(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideValidationMessages()
	{
		this._$validationContainer.slideUp(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	showServerValidationMessages()
	{
		this._$serverValidationContainer.slideDown(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideServerValidationMessages()
	{
		this._$serverValidationContainer.slideUp(500);
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	showInfos()
	{
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	hideInfos()
	{
		return this;
	}
	/**
	 *
	 * @param name
	 * @returns {Input}
	 */
	showInfo(name)
	{
		return this;
	}
	/**
	 *
	 * @param name
	 * @returns {Input}
	 */
	hideInfo(name)
	{
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	validate()
	{
		this.hideValidationMessages();

		if(!this._isValidationEnabled || this.isOptional) return true;

		const validationResults = [];

		this._$validationContainer.find('.validation-message').fadeOut(0);

		for(const rule of this._validation)
		{
			try
			{
				const name = rule.rule, currentValidation = Validator[name](this.value, rule.params);
				this._$validationContainer.find(`[data-rule="${name}"]`)[currentValidation ? 'fadeOut' : 'fadeIn'](0);

				validationResults.push(currentValidation);
			}
			catch (e)
			{
				log(e);
				throw new Error(`Validation error, rule: ${rule.rule} not exists`);
			}
		}

		const isValid = validationResults.indexOf(false) === -1;

		this[isValid ? 'hideValidationMessages' : 'showValidationMessages'](500);

		return isValid;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	serverValidate(validation)
	{
		this.hideServerValidationMessages();

		if(!this._isServerValidationEnabled) return true;

		const validationResults = [];

		this._$serverValidationContainer.find('.validation-message').fadeOut(0);

		for(const rule of validation)
		{
			const
			name = rule.rule,
			currentValidation = rule.result;

			this._$serverValidationContainer.find(`[data-rule="${name}"]`)[currentValidation ? 'fadeOut' : 'fadeIn'](0);

			validationResults.push(currentValidation);
		}

		const isValid = validationResults.indexOf(false) === -1;

		this[isValid ? 'hideServerValidationMessages' : 'showServerValidationMessages'](500);

		return isValid;
	}
	/**
	 *
	 * @param restore
	 * @returns {Input}
	 */
	enableAllValidation(restore = true)
	{
		return this.enableValidation(restore).enableServerValidation(restore);
	}
	/**
	 *
	 * @param clear
	 * @returns {Input}
	 */
	disableAllValidation(clear = true)
	{
		return this.disableValidation(clear).disableServerValidation(clear);
	}
	/**
	 *
	 * @param restore
	 * @returns {Input}
	 */
	enableValidation(restore = true)
	{
		if(restore) this.restore();
		this._isValidationEnabled = true;
		return this;
	}
	/**
	 *
	 * @param clear
	 * @returns {Input}
	 */
	disableValidation(clear = true)
	{
		if(clear) this.clear();
		this._isValidationEnabled = false;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	enableServerValidation(restore = true)
	{
		if(restore) this.restore();
		this._isServerValidationEnabled = true;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	disableServerValidation(clear = true)
	{
		if(clear) this.clear();
		this._isServerValidationEnabled = false;
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	restore()
	{
		if(this._isRestored) return this;
		this._isRestored = true;
		this.value = this._bufferValue || '';
		return this;
	}
	/**
	 *
	 * @returns {Input}
	 */
	clear()
	{
		if(this._isCleared) return this;
		this._isCleared = true;
		this._bufferValue = this.value;
		this.value = '';
		return this;
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return super.value;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		super.value = value ? value.substring(0, this._maxLength || value.length) : value;
	}
	/**
	 *
	 * @returns {*}
	 */
	get $parent()
	{
		return this._$parent;
	}
	/**
	 *
	 * @returns {string}
	 */
	get dataType()
	{
		return this._dataType;
	}
	/**
	 *
	 * @returns {number}
	 */
	get formSchema()
	{
		return this._formSchema;
	}
	/**
	 *
	 * @param value
	 */
	set formSchema(value)
	{
		this._formSchema = value;
		this.parseSchema();
	}
	/**
	 *
	 * @returns {*|number}
	 */
	get schema()
	{
		return this._schema;
	}
	/**
	 *
	 * @param value
	 */
	set schema(value)
	{
		this._schema = value;
		this.parseSchema();
	}
	/**
	 *
	 * @param value
	 */
	set interactive(value)
	{
		super.interactive = value;
		this.$parent[value ? 'removeClass' : 'addClass']('no-interaction');
	}
	/**
	 *
	 * @returns {*}
	 */
	get groupName()
	{
		return this.$selector.attr('data-group');
	}
	/**
	 *
	 * @param value
	 */
	set groupName(value)
	{
		this._groupName = value;
		this.$selector.attr('data-group', value);
	}
	/**
	 *
	 * @returns {*}
	 */
	get $label()
	{
		return this._$label;
	}
	/**
	 *
	 * @param value
	 */
	set $label(value)
	{
		this._$label = value;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get hasMask()
	{
		return this._hasMask;
	}
	/**
	 *
	 * @returns {*}
	 */
	get unformattedValue()
	{
		if(!this._hasMask) return this.value;
		return this.$selector.cleanVal();
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isValidationEnabled()
	{
		return this._isValidationEnabled;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isServerValidationEnabled()
	{
		return this._isServerValidationEnabled;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get validateOnInput()
	{
		return this._validateOnInput;
	}
	/**
	 *
	 * @param value
	 */
	set validateOnInput(value)
	{
		if(this._validateOnInputHandler && value) return;
		else
		{
			this._validateOnInputHandler = (e) =>
			{
				this.validate();
			};

			this.on('keydown input blur focus', this._validateOnInputHandler);
		}

		if(!this._validateOnInputHandler && !value) return;
		else this.off('keydown input blur focus', this._validateOnInputHandler);

		this._validateOnInput = value;
	}
	get isOptional()
	{
		const
			isRequired = this.$selector.attr('required') === 'required',
			isEmpty = this.value === '' || !String(this.value || '').length,
			hasDefaultValue = this.value === this.$selector.attr('value') || this.value === this.$selector.attr('data-value');

		return !isRequired && (isEmpty || hasDefaultValue);
	}
	/**
	 *
	 * @returns {null}
	 */
	get bufferValue()
	{
		return this._bufferValue;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isCleared()
	{
		return this._isCleared;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isRestored()
	{
		return this._isRestored;
	}
	/**
	 *
	 * @returns {API}
	 */
	get api()
	{
		return this._api;
	}
	/**
	 *
	 * @returns {*}
	 */
	get $spinner()
	{
		return this._$spinner;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isBuilt()
	{
		return this._isBuilt;
	}
	/**
	 *
	 * @returns {{}}
	 */
	get dataTypeParams()
	{
		return this._dataTypeParams;
	}

	/**
	 *
	 * @returns {number}
	 */
	get maxLength()
	{
		return this._maxLength;
	}
}




/**
 * @class {InputNumber}
 */
export class InputNumber extends Input
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






















/**
 * @class {AjaxForm}
 */
export class AjaxForm extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param inputs
	 * @param options
	 */
	constructor(id, options = {})
	{
		super(id, { strictResponse:true, inputOptions:{}, ...options });
		/**
		 *
		 * @type {*[]}
		 * @private
		 */
		this._inputs = [];
		/**
		 *
		 * @type {{}}
		 * @private
		 */
		this._inputsObject = {};
		/**
		 *
		 * @type {*[]}
		 * @private
		 */
		this._groups = [];
		/**
		 *
		 * @private
		 */
		this._action = this.$selector.attr('action');
		/**
		 *
		 * @private
		 */
		this._method = this.$selector.attr('method');
		/**
		 *
		 * @type {API}
		 * @private
		 */
		this._api = new API('', {}, options.strictResponse);
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isSubmitting = false;
		/**
		 *
		 * @type {*|string}
		 * @private
		 */
		this._feedBackType = this.$selector.attr('data-feed-back-type') || '';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._clearAfterCreate = this.$selector.attr('data-clear-after-create') === 'data-clear-after-create';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._clearAfterUpdate = this.$selector.attr('data-clear-after-update') === 'data-clear-after-update';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isGET = this._method === 'get';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isPOST = this._method === 'post';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isPUT = this._method === 'put';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isDELETE = this._method === 'delete';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isPATCH = this._method === 'patch';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isHEAD = this._method === 'head';
		/**
		 *
		 * @type {boolean}
		 * @private
		 */
		this._isOPTIONS = this._method === 'options';
		/**
		 *
		 * @type {FormAlerts}
		 * @private
		 */
		this._messagesTop = null;
		this.$selector.addClass('clearfix');
		this._$htmlBody = $('html, body');

		this._createByType();
		this._parseInfos();

/*
		this.$selector.find('[data-form-group]').each((i, e) =>
		{
			const
				$element = $(e),
				name = $element.data('name'),
				id = e.id || name,
				inputs = [];

			let
				options = {};

			this._inputs.forEach((e1, i1) => { if(name === e1.groupName) inputs.push(e1); });

			this._groups.push(new InputGroup(`#${id || name}`, name, options, inputs));
		});
*/
		this._submit = new Button(this.$selector.find('button[type="submit"]'));

		this.$selector.on('submit', async (e) =>
		{
			e.preventDefault();
			await this.submit();
		});

		this.schema = this.$selector.data('schema') || 0;

		if(this._inputs.length) this._inputs[0].focus();
	}
	_parseInfos()
	{
		const id = '#' + $($(`[data-target="#${this.id}"]`)[0]).attr('id');

		if($(id).length) this._messagesTop = new FormAlerts(id);
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 * @private
	 */
	_createByType()
	{
		log('Form inputs:');
		log('--------------------------------------------------------');

		this.$selector.find('[data-form-input]').each((i, e) =>
		{
			const
			$element = $(e),
			id = e.id,
			name = e.name,
			idOrName = id || name;

			let
			InputClass = Input,
			options = this.options.inputOptions ? this.options.inputOptions[idOrName] || {} : {};

			switch($element.attr('data-type'))
			{
				case 'password':

					InputClass = InputPassword;

					break;

				case 'radio':

					InputClass = InputRadio;

					break;

				case 'checkbox':

					InputClass = InputCheckBox;

					break;

				case 'email':

					InputClass = InputEmail;

					break;

				case 'file':

					InputClass = InputFile;

					break;

				case 'url':

					InputClass = InputURL;

					break;

				case 'number':

					InputClass = InputNumber;

					break;

				case 'float':

					InputClass = InputFloat;

					break;

				case 'double':

					InputClass = InputDouble;

					break;

				case 'currency':

					InputClass = InputCurrency;

					break;

				case 'phone':

					InputClass = InputPhone;

					break;

				case 'select':

					InputClass = Select;

					break;

				case 'select2':

					InputClass = Select2;

					break;

				case 'cpf':

					InputClass = InputCPF;

					break;

				case 'cnpj':

					InputClass = InputCNPJ;

					break;

				case 'cep':

					if($element.is('select')) InputClass = Select;
					else InputClass = InputCEP;

					break;
			}

			if(InputClass)
			{
				const
					className = String(InputClass).split('{')[0].trim().replace('class', '').split('extends')[0].trim(),
					instance = new InputClass(`#${idOrName}`, name, options);

				log(`${idOrName}: [${className}]`, options);

				this._inputs.push(instance);
				this._inputsObject[idOrName] = instance;


			}
		});

		log('--------------------------------------------------------');

		return this;
	}
	/**
	 *
	 */
	async submit()
	{
		if(this._isSubmitting) return;
		this._isSubmitting = true;

		if(!this.validate())
		{
			this._isSubmitting = false;
			this.enableInputs().toast(SweetAlert2.MESSAGES.validationErrorText);
			return;
		}

		this.disableInputs().hideAllValidationMessages();
		this._submit.showSpinner();

		SweetAlert2.showLoading();

		log('form value');
		log(this.value);

		const
			isFeedBackModal = this._feedBackType === 'modal',
			serverErrorTitle = SweetAlert2.MESSAGES.serverErrorTitle,
			serverErrorText = SweetAlert2.MESSAGES.serverErrorText,
			serverErrorOptions = { showConfirmButton: false, showCloseButton:true, showCancelButton: false };

		let errorInfo = '';

		const
			response = await this._api[this._method](this._action, this.value),
			header = response.header || { status: { code: 500 } },
			success = response.success,
			validation = response.validation.result,
			hasValidation = validation && Object.keys(validation).length,
			body = response.body,
			exception = body ? body.exception : null,
			messages = body ? body.formMessages : null;

		log('success', success);
		log('validation:', hasValidation, validation);
		log('body', body);
		log('messages', messages);
		log('exception', exception);

		if(messages) this._messagesTop.addMessages(messages);

		try
		{
			if(body && exception && exception.errorInfo) errorInfo = `<pre class="error-description">${exception.errorInfo.join('\n')}</pre>`;

			if(success)
			{
				if(isFeedBackModal) this.alertSuccess(null, null, {}, () => { this._$htmlBody.animate({ scrollTop: 0 }, 500); }).trigger({ type: 'success', success: true, error: false, response });

				if((this._clearAfterCreate && this._method === 'post') || (this._clearAfterUpdate && this._method === 'put')) this.clear().trigger({ type: 'clear' });

			}
			else
			{
				if(hasValidation) if(!this.serverValidate(validation)) this.toast(SweetAlert2.MESSAGES.validationErrorText);

				if(!hasValidation && isFeedBackModal) this.alertError().trigger({ type: 'error', success: false, error: true, response });
			}

			if(header.status.code === 500 && isFeedBackModal) this.alertError(serverErrorTitle, `${serverErrorText}${errorInfo}`, serverErrorOptions).trigger({ type: 'error', success: false, error: true });
		}
		catch (e)
		{
			if(isFeedBackModal) this.alertError(serverErrorTitle, `${serverErrorText}${errorInfo}`, serverErrorOptions).trigger({ type: 'error', success: false, error: true });
		}

		this.enableInputs();
		this._submit.showText();

		this._isSubmitting = false;
		SweetAlert2.hideLoading();

		this.trigger({ type: 'response' });

		return this;
	}

	/**
	 *
	 * @param message
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	toast(message, options = { position: 'bottom-end', timer: 7770 }, completeHandler = (result) => { return result; })
	{
		SweetAlert2.toastValidationError(message, options, completeHandler);
		return this;
	}

	/**
	 *
	 * @param icon
	 * @param title
	 * @param text
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	alert(icon, title, text, options = {}, completeHandler = (result) => { return result; })
	{
		SweetAlert2.alert(icon, title, text, options, completeHandler);
		return this;
	}

	/**
	 *
	 * @param title
	 * @param message
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	alertSuccess(title = null, message = null, options = {}, completeHandler = (result) => { return result; })
	{
		SweetAlert2.alertSuccess(title || SweetAlert2.MESSAGES.successTitle, message || SweetAlert2.MESSAGES[this._isPOST ? 'createSuccessText' : 'updateSuccessText'], options, completeHandler);
		return this;
	}
	/**
	 *
	 * @param title
	 * @param message
	 * @param options
	 * @param completeHandler
	 * @returns {AjaxForm}
	 */
	alertError(title = null, message = null, options = {}, completeHandler = (result) => { return result; })
	{
		SweetAlert2.alertError(title || SweetAlert2.MESSAGES.errorTitle, message || SweetAlert2.MESSAGES[this._isPOST ? 'createErrorText' : 'updateErrorText'], options, completeHandler);
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	showAllValidationMessages()
	{
		this._inputs.forEach(input => input.showAllValidationMessages());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	hideAllValidationMessages()
	{
		this._inputs.forEach(input => input.hideAllValidationMessages());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	clear()
	{
		this._inputs.forEach(input => input.clear());
		this.enableInputs();
		return this;
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	enable()
	{
		this.enableInputs().enableSubmit();
		return super.enable();
	}
	/**
	 *
	 * @returns {DOMComponent}
	 */
	disable()
	{
		this.disableInputs().disableSubmit();
		return super.disable();
	}

	/**
	 *
	 * @returns {AjaxForm}
	 */
	enableInputs()
	{
		this._inputs.forEach(input => input.enable());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	disableInputs()
	{
		this._inputs.forEach(input => input.disable());
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	enableSubmit()
	{
		this._submit.enable();
		return this;
	}
	/**
	 *
	 * @returns {AjaxForm}
	 */
	disableSubmit()
	{
		this._submit.disable();
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	validate()
	{
		let isMoved = false;

		return this._inputs.map((input) =>
		{
			const validation = input.validate();

			if(!isMoved && !validation)
			{
				isMoved = true;
				input.focus();
			}

			return validation;

		}).indexOf(false) === -1;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	serverValidate(validation)
	{
		return this._inputs.map((input) => { return input.serverValidate(validation[input.id.replace('input-', '')]); }).indexOf(false) === -1;
	}
	/**
	 *
	 * @param id
	 * @returns {null|*}
	 */
	getInputByID(id)
	{
		let i;
		const total = this._inputs.length;

		for(i = 0; i < total; ++i) if(this._inputs[i].id === id) return this._inputs[i];

		return null;
	}
	/**
	 *
	 * @param id
	 * @returns {null|*}
	 */
	getGroupByID(id)
	{
		let i;
		const total = this._groups.length;

		for(i = 0; i < total; ++i) if(this._groups[i].id === id) return this._groups[i];

		return null;
	}
	/**
	 *
	 * @param name
	 * @returns {null|*}
	 */
	getInputByName(name)
	{
		let i;
		const total = this._inputs.length;

		for(i = 0; i < total; ++i) if(this._inputs[i].name === name) return this._inputs[i];

		return null;
	}
	/**
	 *
	 * @returns {*}
	 */
	get action()
	{
		return this._action;
	}
	/**
	 *
	 * @param value
	 */
	set action(value)
	{
		this._action = value;
		this.$selector.attr('action', value);
	}
	/**
	 *
	 * @returns {*}
	 */
	get method()
	{
		return this._method;
	}
	/**
	 *
	 * @param value
	 */
	set method(value)
	{
		this._method = value;
		this.$selector.attr('method', value);
	}
	/**
	 *
	 * @returns {*}
	 */
	get inputs()
	{
		return this._inputs;
	}
	/**
	 *
	 * @returns {*[]}
	 */
	get data()
	{
		return this._inputs.map((input) => { return input.value; });
	}
	/**
	 *
	 * @param value
	 */
	set data(value)
	{
		let i;
		const total = value.length;

		for(i = 0; i < total; ++i) this._inputs[i].value = value[i];
	}
	/**
	 *
	 * @returns {{}}
	 */
	get value()
	{
		const result = {};

		this._inputs.forEach((e, i) => { result[((e.id || e.name) || '').replace('input-', '').replace('#', '')] = e.value; });

		return result;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		let p;
		for(p in value)
		{
			//log(p, '=', value[p]);
			const input = this._inputsObject['input-' + p];
			//log('input', input)
			input.value = value[p];
			input.update();
		}
	}
	/**
	 *
	 * @returns {number}
	 */
	get schema()
	{
		return this._schema;
	}
	/**
	 * Show and hide inputs according the schema number
	 * @param value
	 */
	set schema(value)
	{
		//log('form schema', value)
		this._schema = value;
		this.$selector.attr('data-schema', value);
		this._inputs.forEach(input => input.formSchema = value);
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isSubmitting()
	{
		return this._isSubmitting;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isGET()
	{
		return this._isGET;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isPOST()
	{
		return this._isPOST;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isPUT()
	{
		return this._isPUT;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isDELETE()
	{
		return this._isDELETE;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isPATCH()
	{
		return this._isPATCH;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isHEAD()
	{
		return this._isHEAD;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get isOPTIONS()
	{
		return this._isOPTIONS;
	}
}

/**
 * @class {Alert}
 */
export class Alert extends DOMComponent
{
	constructor(id, options = {})
	{
		super(id, options);
	}

}

/**
 * @class {Button}
 */
export class Button extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param options
	 */
	constructor(id, options = [])
	{
		super(id, options);

		this._$spanText = this.$selector.find('.button-content');
		this._$spinner = this.$selector.find('.spinner').animate({ opacity:0 }, 0);
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	showSpinner(duration = 500)
	{
		if(this.$selector.innerHeight() > this._$spinner.innerHeight()) this._$spinner.css({ top:(this.$selector.innerHeight() - this._$spinner.innerHeight()) >> 1 });
		this._$spanText.animate({ opacity:0 }, duration);
		this._$spinner.animate({ opacity:1 }, duration);
		this.$selector.attr('data-state', 'spinner');
		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	hideSpinner(duration = 500)
	{
		this._$spinner.animate({ opacity:0 }, duration);
		this._$spanText.animate({ opacity:1 }, duration);
		this.$selector.removeAttr('data-spinner');
		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	showText(duration = 500)
	{
		this._$spanText.animate({ opacity:1 }, duration);
		this._$spinner.animate({ opacity:0 }, duration);
		this.$selector.attr('data-state', 'text');
		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {Button}
	 */
	hideText(duration = 500)
	{
		this._$spanText.animate({ opacity:0 }, duration);
		return this;
	}
}
/**
 * @class {CRUD}
 */
export class CRUD
{
	constructor(options)
	{

	}
}



/**
 *
 * @type {string}
 */
$.fn.dataTable.ext.errMode = 'none';
/**
 *
 * @type {string}
 */
const DEFAULT_VALUE = '-';
/**
 *
 * @param columns
 * @returns {*[]}
 */
const setupColumns = (columns = []) =>
{
	let i;
	const total = columns.length,
	config = [];

	for (i = 0; i < total; ++i)
	{
		const column = columns[i],
		typeData = {...{ prefix: '', suffix: ''}, ...(column.typeData || {}) },
		columnConfig = { data: column.name, render: (data) => { return data; } },
		prefix = typeData.prefix || '',
		suffix = typeData.suffix || '';

		if(!column.data) column.data = column.name;

		switch (column.type)
		{
			case 'name':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return `<span class="name">${prefix}${value}${suffix}</span>`;
				};

				break;

			case 'email':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return `<a href="mailto:${value}">${prefix}${value}${suffix}</a>`;
				};

				break;

			case 'document':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					if (value.length === 11) return prefix + StringUtil.formatCPF(value) + suffix;
					if (value.length === 14) return prefix + StringUtil.formatCNPJ(value) + suffix;

					return value;
				};

				break;

			case 'cpf':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.formatCPF(value) + suffix;
				};

				break;

			case 'cnpj':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.formatCNPJ(value) + suffix;
				};

				break;

			case 'date':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.dateToPTBRFormat(value) + suffix;
				};

				break;

			case 'list':

				columnConfig.render = (data) =>
				{
					return prefix + (data || DEFAULT_VALUE) + suffix;
				};

				break;

			case 'number':

				columnConfig.render = (data) =>
				{
					return prefix + (Number(data) || DEFAULT_VALUE) + suffix;
				};

				break;

			case 'float':

				columnConfig.render = (data) =>
				{
					if(!data) return DEFAULT_VALUE;
					return prefix + parseFloat(data).toFixed(typeData.decimals || 2) + suffix;
				};

				break;

			case 'currency':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.formatCurrency(value) + suffix;
				};

				break;


			case 'object':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return '<span class="status">' + prefix + (typeData[value] || value) + suffix + '</span>';
				};

				break;

			default:

				columnConfig.render = (data) =>
				{
					if(!data) return DEFAULT_VALUE;
					return prefix + data + suffix;
				};

				break;
		}

		config.push(columnConfig);
	}

	return config;
};
/**
 *
 * @returns {Promise<any>}
 */
const requestData = async () =>
{
	_exportTimeInterval = setInterval(() =>
	{
		++_exportTime;
		log(`time elapsed: ${_exportTime} seconds`);
	}, 1000);
	showLoader();
	const response = await fetch(`${URL}?` + $.param({...getURLParams(), ...{all: true, test: false, result_type: 'values'}}));
	return response.json();
};
/**
 *
 * @param type
 * @returns {Promise<void>}
 */
const exportAll = async (type) =>
{
	const data = await requestData();
	//console.log(data);
	hideLoader();
	clearInterval(_exportTimeInterval);

	if (type === 'excel') exportExcel(null, null, data);

	console.log(`Total time to export: ${_exportTime} seconds`);
};
/**
 *
 * @param name
 * @param fileName
 * @param data
 * @param meta
 */
const exportExcel = (name, fileName, data, meta) =>
{
	const wb = XLSX.utils.book_new();

	wb.Props = {
		Title: 'Report',
		Subject: 'Report',
		Author: 'XGrow',
		CreatedDate: (new Date()).getDate()
	};

	wb.SheetNames.push('Test Sheet');

	var ws_data = data.data;  //a row with 2 columns

	var ws = XLSX.utils.aoa_to_sheet(ws_data);

	wb.Sheets['Test Sheet'] = ws;

	var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

	saveAs(new Blob([s2ab(wbout)], {type: 'application/octet-stream'}), 'relatorio.xlsx');
};

/**
 *
 * @param s
 * @returns {ArrayBuffer}
 */
function s2ab(s)
{
	var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
	var view = new Uint8Array(buf);  //create uint8array as viewer
	for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
	return buf;
}

/**
 *
 * @type {{exportOptions: {modifier: {search: string, page: string}}}}
 */
const exportOptions = { exportOptions: { modifier: {page: 'all', search: 'none'} } };
/**
 *
 */
const initHandler = () =>
{
	//this.options.initHandler();
	//updateSort();
	//hideLoader();
};
/**
 *
 * @param e
 */
const updateSort = (e) =>
{
	if (e && e.currentTarget)
	{
		const index = $(e.currentTarget).data('index');
		const existentIndex = _order.map(x => x[0]).indexOf(index);

		if (existentIndex > -1)
		{
			++_order[existentIndex][2];

			if (_order[existentIndex][2] === 1) _order[existentIndex][1] = 'asc';
			else _order.splice(existentIndex, 1);
		}
		else
		{
			_order.unshift([index, 'desc', 0]);
		}
	}

	let counter = 0;

	$(`.badge-sort`).addClass('hidden');
	_order.forEach((e, i) =>
	{
		$(`.badge-sort[data-index=${_order[i][0]}]`).text(++counter).removeClass('hidden');
	});

	if (e && e.currentTarget) $dataTable.order(_order);
};
/**
 *
 * @type {*[]}
 * @private
 */
const _order =
[

];
/**
 *
 */
$(document).on('click', '.sort-column', (e) =>
{
	e.preventDefault();
	e.stopImmediatePropagation();
	e.stopPropagation();

	updateSort(e);
});
/**
 *
 * @type {number[][]}
 */
const lengthMenu = [[100, 250, 500, 1000, 2500, 5000, 10000], [100, 250, 500, 1000, 2500, 5000, 10000]];
/**
 * @class {DataTable}
 */
export class DataTable extends DOMComponent
{
	/**
	 * @inheritDoc
	 */
	constructor(id, options = {})
	{
		super(id, { ...{ getURLParams:() => { return {}; } }, ...options});

		this._$tableContainer = this.$selector.closest('.table-container');
		this._$loader = this._$tableContainer.find('.table-loader');

		const COLUMNS_DATA = options.columns;
		delete options.columns;

		this._$dataTable = $(id)
		.on('init.dt', (e, settings, data) =>
		{
			console.log(`DataTables ${id} inited`);
		})
		.on('preXhr.dt', (e, settings, data) =>
		{
			this.showLoader();
		})
		.on('xhr.dt', (e, settings, data) =>
		{
			this.hideLoader();
		})
		.on('order.dt', (e, settings, data) =>
		{

		})
		.on('error.dt', (e, settings, techNote, message) =>
		{
			console.log(`DataTables ${id} error`);
			console.warn(message);
		})
		.DataTable
		(
			{
				...{
					/*dom: 'l<br />Bfrtip',*/
					orderMulti: true,
					columnDefs:
					[
						{
							defaultContent:'-',
							targets: '_all',
						}
					],
					order: [],
					columns: setupColumns(COLUMNS_DATA),
					buttons: [],
					language: { url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json' },
					ajax:
					{
						url: this.options.url,
						data: (data) =>
						{
							const vars =
							{
								...{
									itemsPerPage: Number(this._$dataTable ? this._$dataTable.page.len() : lengthMenu[0]),
									page: Number(this._$dataTable ? this._$dataTable.page() : 0) + 1
								},
								...{ ...this.options.getURLParams(), ...data }
							}
							//console.log('urlParams', vars);
							return vars;
						}
					},
					searching: false,
					processing: false,
					serverSide: true,
					searchDelay: 1500,
					lengthMenu
				},
				...(options || {})
			}
		);

		//console.log('OPTIONS');
		//console.log(this.options);
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	showLoader()
	{
		this._$loader.fadeIn(500);
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	hideLoader()
	{
		this._$loader.fadeOut(500);
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	show(reload = true, completeCallBack = () => {})
	{
		if(reload) this.reloadRequest(() => { this.hideLoader(); completeCallBack(); });
		this.$selector.closest('.table-container').removeClass('hidden').removeAttr('hidden');
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	hide()
	{
		this.abortRequest();
		this.$selector.closest('.table-container').addClass('hidden').attr('hidden', 'hidden');
		return this;
	}
	/**
	 *
	 * @param completeCallBack
	 * @returns {DataTable}
	 */
	reloadRequest(completeCallBack = () => {})
	{
		this._$dataTable.ajax.reload(completeCallBack);
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	abortRequest()
	{
		try { this._$dataTable.settings()[0].jqXHR.abort(); } catch (e) {}
		return this;
	}
}


/**
 * @class {DatePicker}
 */
export class DatePicker extends DOMComponent
{
	/**
	 * @inheritDoc
	 */
	constructor(selector, options = {})
	{
		super(selector, options);
		this._$datePicker = DatePicker.create(selector, options);
	}
	get value()
	{
		return this._$datePicker.val();
	}
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	static create(selector= '.datepicker', options = {})
	{
		return  $(selector).datepicker
		(
			{
				...{
					todayBtn: true,
					clearBtn: true,
					language: 'pt-BR',
					multidate: false,
					calendarWeeks: true,
					autoclose: true,
					todayHighlight: true,
					zIndexOffset:777
				},
				...options
			}
		);
	}
}

/**
 *
 * @type {EventDispatcher}
 */
const
	/**
	 *
	 * @type {{closeButtonText: string, denyButtonText: string, confirmButtonText: string, cancelButtonText: string}}
	 */
	MESSAGE =
	{
		confirm: 'confirmar',
		deny: 'negar',
		cancel: 'cancelar',
		close: 'fechar'
	},
	/**
	 *
	 * @type {{SUCCESS: string, ERROR: string, INFO: string, WARNING: string, QUESTION: string}}
	 */
	ICON =
	{
		'SUCCESS': 'success',
		'ERROR': 'error',
		'WARNING': 'warning',
		'INFO': 'info',
		'QUESTION': 'question'
	},
	/**
	 *
	 * @type {{SUCCESS: string, ERROR: string, INFO: string, WARNING: string, QUESTION: string}}
	 */
	TITLE =
	{
		'SUCCESS': `<h1>sucesso!</h1>`,
		'ERROR': `<h1>erro!</h1>`,
		'WARNING': `<h1>aviso!</h1>`,
		'INFO': `<h1>informação!</h1>`,
		'QUESTION': `<h1>pergunta!</h1>`
	},
	/**
	 *
	 * @type {{closeButtonText: string, denyButtonText: string, confirmButtonText: string, cancelButtonText: string}}
	 */
	DEFAULTS =
	{
		showConfirmButton: true,
		showCloseButton: true,
		showCancelButton: true,
		showDenyButton: true,
		allowEscapeKey:true,
		allowEnterKey:true,
		backdrop: true,
		allowOutsideClick: true,
		confirmButtonText: MESSAGE.confirm,
		denyButtonText: MESSAGE.deny,
		cancelButtonText: MESSAGE.cancel
	},
	/**
	 *
	 * @type {{backdrop: boolean, showCloseButton: boolean, showCancelButton: boolean}}
	 */
	ALERT_DEFAULTS =
	{
		...DEFAULTS,
		showConfirmButton: true,
		showCloseButton: true,
		showCancelButton: false,
		showDenyButton: false,
	},
	/**
	 *
	 * @type {{backdrop: boolean, showCloseButton: boolean, showCancelButton: boolean}}
	 */
	PIN_ALERT_DEFAULTS =
	{
		...ALERT_DEFAULTS,
		showConfirmButton: false,
		showCloseButton: false,
		showCancelButton: false,
		showDenyButton: false,
		allowEscapeKey:false,
		allowEnterKey:false,
		backdrop: true,
		allowOutsideClick: false
	};
/**
 * @class {Dialog}
 */
export class Dialog extends StaticEventDispatcher
{
	constructor()
	{
		super();
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alert(options = {})
	{
		return Swal.fire({ ...ALERT_DEFAULTS, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertSuccess(options = {})
	{
		return Dialog.alert({ icon:ICON.SUCCESS, title:TITLE.SUCCESS, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertError(options = {})
	{
		return Dialog.alert({ icon:ICON.ERROR, title:TITLE.ERROR, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertWarning(options = {})
	{
		return Dialog.alert({ icon:ICON.WARNING, title:TITLE.WARNING, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertInfo(options = {})
	{
		return Dialog.alert({ icon:ICON.INFO, title:TITLE.INFO, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static alertQuestion(options = {})
	{
		return Dialog.alert({ icon:ICON.QUESTION, title:TITLE.QUESTION, ...options });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertSuccess(options = {})
	{
		return Dialog.alert({ icon:ICON.SUCCESS, title:TITLE.SUCCESS, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertError(options = {})
	{
		return Dialog.alert({ icon:ICON.ERROR, title:TITLE.ERROR, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertWarning(options = {})
	{
		return Dialog.alert({ icon:ICON.WARNING, title:TITLE.WARNING, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertInfo(options = {})
	{
		return Dialog.alert({ icon:ICON.INFO, title:TITLE.INFO, ...options, ...PIN_ALERT_DEFAULTS });
	}
	/**
	 *
	 * @param options
	 * @returns {*}
	 */
	static pinAlertQuestion(options = {})
	{
		return Dialog.alert({ icon:ICON.QUESTION, title:TITLE.QUESTION, ...options, ...PIN_ALERT_DEFAULTS });
	}
}
/**
 * @class Form
 */


/**
 * @class {Form}
 */
export class Form extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param options
	 */
	constructor(id, options = {})
	{
		super(id, options);
		this._autoSubmit = options.autoSubmit || false;
		this._state = 'enabled';

		this._$form = $(id).on('submit', (e) =>
		{
			e.preventDefault();
			e.stopPropagation();
			this.submit();
		});

		this._$inputs = {};

		this._$form.find(`[data-form-input]`).each((i, e) =>
		{
			const $input = $(e);
			this._$inputs[$input.attr('id').replace('input-', '')] = $input;
		});

		this._$submit = this._$form.find('[type="submit"]');

		this._$form.find('.input-validation-message').removeAttr('hidden').fadeOut(0);

		console.log(this._$form.attr('id'));
		console.log(Object.keys(this._$inputs));
	}

	showSubmitSpinner()
	{
		this._$form.find('.submit-spinner').removeClass('hidden');
		this._$form.find('.submit-content').addClass('hidden');
	}

	hideSubmitSpinner()
	{
		this._$form.find('.submit-spinner').addClass('hidden');
		this._$form.find('.submit-content').removeClass('hidden');
	}

	enable()
	{
		this.hideSubmitSpinner();
		this._$form.removeClass('disabled');
	}

	disable()
	{
		this._$form.addClass('disabled');
	}

	awaiting()
	{
		this.disable();
		this.showSubmitSpinner();
	}

	validate()
	{
		//console.log('this._$inputs', this._$inputs);
		let p, validation = [];
		for (p in this._$inputs)
		{
			const $input = $(this._$inputs[p]),
			required = $input[0].hasAttribute('required'),
			name = $input.attr('name'),
			value = $input.val();

			if(required && !value)
			{
				this._$form.find(`[data-name="${name}"]`).fadeIn(500);
				validation.push(false);
			}
			else
			{
				this._$form.find(`[data-name="${name}"]`).fadeOut(500);
				validation.push(true);
			}
		}

		console.log('validation', validation, validation.indexOf(false));

		return validation.indexOf(false) === -1;
	}

	submit()
	{
		if(this.options.validate && !this.validate()) return;

		if (!this._autoSubmit)
		{
			$(window).trigger({ type: 'form:submit', value: this.value });
			return;
		}
	}

	get value()
	{
		let p,
		value = {};
		for (p in this._$inputs) value[p] = this._$inputs[p].val();
		return value;
	}

	get inputs()
	{
		return this._$inputs;
	}

	get state()
	{
		return this._state;
	}

	set state(state)
	{
		this._state = state;
		if (state === 'enabled') this.enable();
		else if (state === 'disabled') this.disable();
		else if (state === 'awaiting') this.awaiting();
	}
}



/**
 *
 * @type {{success: string, warning: string, info: string}}
 */
const TEMPLATES =
{
	'success': `<li class="success" data-type="success" data-name="%s" data-params="%s" data-fixed="%s" data-alert><i class="fas fa-check"></i> %s</li>`,
	'warning': `<li class="warning" data-type="warning" data-name="%s" data-params="%s" data-fixed="%s" data-alert><i class="fas fa-times"></i> %s</li>`,
	'info': `<li class="info" data-type="info" data-name="%s" data-params="%s" data-fixed="%s" data-alert><i class="fas fa-info"></i> %s</li>`
};

/**
 * @class {FormAlerts}
 */
export class FormAlerts extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param options
	 */
	constructor(id = null, options = {})
	{
		super(id, options);

		this._$messages = {};
		this._$list = this.$selector.find('ul');

		this.hide(0);

		this._build();

		this.show();
	}
	/**
	 *
	 * @returns {FormAlerts}
	 * @private
	 */
	_build()
	{
		this.$selector.find('[data-alert]').each((i, e) =>
		{
			const $alert = $(e);
			this._$messages[$alert.attr('data-name')] = $alert;
		});

		return this;
	}

	/**
	 *
	 * @param duration
	 * @returns {FormAlerts}
	 */
	show(duration = 500)
	{
		this.$selector.slideDown(duration);
		return this;
	}
	/**
	 *
	 * @param duration
	 * @returns {FormAlerts}
	 */
	hide(duration = 500)
	{
		this.$selector.slideUp(duration);
		return this;
	}
	/**
	 *
	 * @param messages
	 * @returns {FormAlerts}
	 */
	addMessages(messages)
	{
		let p;
		for(p in messages) this.addMessage(p, messages[p]);
		log('this._$messages', this._$messages)
		return this;
	}

	/**
	 *
	 * @param name
	 * @param data
	 * @returns {FormAlerts}
	 */
	addMessage(name, data)
	{
		if(this._$messages[name]) this.removeMessage(name);

		const { type, message, fixed, params } = data;

		const html = StringUtil.vsprintf(TEMPLATES[type], [name, params, fixed ? 1 : 0, StringUtil.vsprintf(message, parseParams(params))]);

		this._$messages[name] = $(html).prependTo(this._$list);

		return this;
	}
	/**
	 *
	 * @param name
	 * @returns {FormAlerts}
	 */
	removeMessage(name)
	{
		this._$messages[name].remove();
		delete this._$messages[name];
		return this;
	}
}

const parseParams = (params = null) =>
{
	return !params ? [] : String(params || '').split('|').map((e, i) => { return String(e || '').trim(); });
}


/**
 * @class {IconButton}
 */
export class IconButton extends DOMComponent
{
	/**
	 *
	 */
	constructor(icon0, icon1)
	{
		super();

		this._isSwitched = false;

		this._$icon0 = $(icon0).css('cursor', 'pointer');
		this._$icon1 = $(icon1).css('cursor', 'pointer').animate({ opacity:0 }, 0);

		const clickHandler = (e) =>
		{
			const duration = 500;

			if(!this._isSwitched)
			{
				this._$icon0.animate({ opacity:0 }, duration);
				this._$icon1.animate({ opacity:1 }, duration);
			}
			else
			{
				this._$icon0.animate({ opacity:1 }, duration);
				this._$icon1.animate({ opacity:0 }, duration);
			}

			this._isSwitched = !this._isSwitched;

			this.changeCallBack();
		}

		this._$icon0.parent().css('min-width', this._$icon0.parent().width()).css('cursor', 'pointer').on('click', clickHandler);

	}
}



/**
 *
 * @type {{uf: string, logradouro: string, bairro: string, localidade: string}}
 */
const
	/**
	 *
	 * @type {{uf: string, logradouro: string, bairro: string, localidade: string}}
	 */
	FRAGMENTS_MAP =
	{
		'logradouro': 'address',
		'bairro': 'district',
		'localidade': 'city',
		'uf': 'state'
	};

/**
 * @class {InputCEP}
 */
export class InputCEP extends InputNumber
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, { ...options, ...{ autoMask:false } });

		this._fragments = {};
		let p;

		if(!this.$selector.attr('data-cep-input'))
		{
			this._isCepInput = true;

			$(`[data-cep-input="${this.id}"]`).each((i, e) =>
			{
				const $element = $(e);
				this._fragments[$element.attr('data-fragment')] = $element;
			});

			this.on('keyup input', async e =>
			{
				await this.requestData();
			});

			this.mask(this.dataType);
		}
		else
		{
			this._isCepInput = false;
			for(p in this._fragments) this._fragments[p].val('');
		}
	}
	async update()
	{
		await this.requestData();
		return super.update();
	}
	/**
	 *
	 * @returns {Promise<InputCEP|boolean>}
	 */
	async requestData()
	{
		let p;

		if(this.value === '.' || this.value === '-' || this.value === '.-')
		{
			this.value = '';
			return false;
		}

		const length = (this.value.indexOf('.') > -1 || this.value.indexOf('-') > -1) ? 10 : 8;

		if(this.value.length === length)
		{
			this.showSpinner();
			const cepResult = await CEP.getAddress(this.value);

			if(cepResult)
			{
				for(p in cepResult) if(this._fragments[FRAGMENTS_MAP[p]]) this._fragments[FRAGMENTS_MAP[p]].val(cepResult[p]);

				this.hideSpinner();

				if(Object.keys(cepResult).length && this._fragments['number']) this._fragments['number'].focus();
			}
			else
			{
				this._clearInputs();
				this.hideSpinner();
			}
		}
		else this._clearInputs();

		return this;
	}
	_clearInputs()
	{
		let p;
		for(p in this._fragments) this._fragments[p].val('');
		this.hideSpinner();
		return this;
	}
}



/**
 * @class {InputRadio}
 */
export class InputCheckBox extends Input
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, { ...{ autoMask:false }, ...options });
		this.value = Cast.boolean(this.$selector.attr('value')) || this.$selector.attr('checked') === 'checked';
		this.on('click', e => { this.toggle(); });
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	toggle()
	{
		if(this.checked) this.uncheck();
		else this.check();

		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	check()
	{
		this.$selector.attr('checked', 'checked').attr('value', '1');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	uncheck()
	{
		this.$selector.removeAttr('checked').attr('value', '0');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get checked()
	{
		return this.$selector.attr('checked') === 'checked' || this.$selector.attr('value') === '1';
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this.checked;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		this._value = Cast.boolean(value);
		if(this._value) this.check();
		else this.uncheck();
	}
}

/**
 * @class {InputNumber}
 */
export class InputCNPJ extends Input
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
			if(this.value === '.' || this.value === '-' || this.value === '.-' || this.value === '../-')
			{
				this.value = '';
				return false;
			}
		});
	}
}

/**
 * @class {InputNumber}
 */
export class InputCPF extends Input
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

/**
 * @class {InputCurrency}
 */
export class InputCurrency extends Input
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
		this.$selector.attr('data-validation', 'currency').attr('data-mask', 'currency');
	}
}



/**
 * @class {InputNumber}
 */
export class InputDouble extends InputNumber
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
		/**
		 *
		 * @private
		 */
		this._punctuation = this.$selector.attr('data-punctuation') || '.';
		/**
		 *
		 * @private
		 */
		this._decimals = Number(this.$selector.attr('data-decimals')) || 2;
		/**
		 * @private
		 */
		this.on('keydown input', e => { this.value = e.target.value; });

		if(this.value && (this.maxLength - (this._decimals +1) > 0))
		{
			this.value += this._punctuation + Array(this._decimals).fill('0').join('');
			//if(this.value.charAt(this.value.length-1) === this._punctuation) this.value = String(this.value).substr(0, this.value.length-1);
		}

		this.$selector.mask(NumberUtil.format(this.maxLength, this._decimals, this._punctuation), { reverse: true });
	}

}

/**
 * @class {InputCurrency}
 */
export class InputEmail extends Input
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

/**
 * @class {InputFile}
 */
export class InputFile extends Input
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


/**
 * @class {InputNumber}
 */
export class InputFloat extends InputNumber
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
		/**
		 *
		 * @private
		 */
		this._punctuation = this.$selector.attr('data-punctuation');
		/**
		 *
		 * @private
		 */
		this._decimals = this.$selector.attr('data-decimals');
	}
}
/**
 * @class {InputGroup}
 */

/**
 *
 * @type {{}}
 */
const GROUPS = {};
/**
 * @class {InputGroup}
 */
export class InputGroup extends DOMComponent
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 * @param inputs
	 */
	constructor(id, name, options = {}, inputs = [])
	{
		super(id, options);

		this._name = name;
		this._selector = name;
		this._$selector = $(`[data-name="${name}"]`);
		this._$inputs = inputs.length ? inputs : this._$selector.find(`[data-form-input]`);
		this._group = this.$selector.data('name');

		GROUPS[this._group] = this;

		const clickHandler = (e) =>
		{
			e.stopImmediatePropagation();
			e.stopPropagation();

			this.value = $(e.target).val() || $(e.currentTarget).val();
		};

		this._$inputs.forEach((e, i) =>
		{
			$(e).attr('name', `${this._name}[]`).on('change', clickHandler);//.attr('data-group', this._group)
		});

		this._options = options;

	}

	/**
	 *
	 * @param name
	 * @returns {*}
	 */
	static get(name)
	{
		return GROUPS[name];
	}
	/**
	 *
	 * @param input
	 * @returns {InputGroup}
	 */
	select(input)
	{
		const
			inputs = GROUPS[input.groupName].inputs,
			indexOf = inputs.indexOf(input);
			inputs.forEach((e, i) => { log('InputGroup.uncheck', i); e.uncheck(); });

		log('InputGroup.check', indexOf);

		if(indexOf > -1) inputs[indexOf].check();

		return this;
	}
	/**
	 *
	 * @returns {{}}
	 */
	get options()
	{
		return this._options;
	}
	/**
	 *
	 * @returns {jQuery|HTMLElement}
	 */
	get $selector()
	{
		return this._$selector;
	}
	/**
	 *
	 * @returns {*}
	 */
	get inputs()
	{
		return this._$inputs;
	}
	/**
	 *
	 * @returns {*}
	 */
	get group()
	{
		return this._group;
	}
	/**
	 *
	 * @param value
	 */
	set group(value)
	{
		this._group = value;
	}
}


/**
 * @class {InputPassword}
 */
export class InputPassword extends Input
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

/**
 * @class {InputPhone}
 */
export class InputPhone extends Input
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
		this.$selector.attr('data-validation', 'phone').attr('data-mask', 'phone');
	}
}


/**
 * @class {InputRadio}
 */
export class InputRadio extends Input
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, { ...{ autoMask:false }, ...options });

		this.$label.on('click', e =>
		{
			e.preventDefault();
			e.stopPropagation();
			$(`[name="${this.name}"]`).removeAttr('checked');
			this.$selector.attr('checked', 'checked').trigger({ type:'click' });
		});

		this.on('mousedown', e =>
		{
			$(`[name="${this.name}"]`).removeAttr('checked');
			this.$selector.attr('checked', 'checked');
		});

		if(this.checked) this.trigger({ type:'click' });
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	toggle()
	{
		if(this.checked) this.uncheck();
		else this.check();

		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	check()
	{
		this.$selector.attr('checked', 'checked');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {InputRadio}
	 */
	uncheck()
	{
		this.$selector.removeAttr('checked');
		this.parseSchema();
		return this;
	}
	/**
	 *
	 * @returns {boolean}
	 */
	get checked()
	{
		return this.$selector.attr('checked') === 'checked';
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this.checked;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		if(value) this.check();
		else this.uncheck();
	}
}

/**
 * @class {InputCurrency}
 */
export class InputURL extends Input
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
		//this.$selector.attr('data-validation', 'email|unique:email').attr('data-mask', 'email');
	}
	/**
	 *
	 * @returns {string}
	 */
	get value()
	{
		return String(super.value || '').replace('https://', '').replace('http://', '').replace('www.', '')
	}
	set value(value)
	{
		super.value = value;
	}
}
/**
 *
 * @type {*|jQuery|HTMLElement}
 */
const $loader = $('<div id="report-loader"><div class="loader-container"><div class="loader"></div></div></div>'),
$body = $('body');
/**
 * @class {Loader}
 */
export class Loader
{
	static create()
	{
		$body.removeClass('no-interaction').append($loader.fadeOut(0));
	}
	static show(duration = 500)
	{
		$body.addClass('no-interaction');
		$loader.fadeIn(duration);
	}
	static hide(duration = 500)
	{
		$body.removeClass('no-interaction');
		$loader.fadeOut(duration);
	}
}

/**
 * @class PopOver
 */
export class PopOver extends DOMComponent
{
	/**
	 *
	 * @param selector
	 * @param options
	 */
	constructor(selector = null, options = {})
	{
		super(selector, options);
		this._$popOver = $(selector).popover(options);
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	show()
	{
		this._$popOver.popover('show');
		return this;
	}
	hide()
	{
		this._$popOver.popover('hide');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	toggle()
	{
		this._$popOver.popover('toggle');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	dispose()
	{
		this._$popOver.popover('dispose');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	enable()
	{
		this._$popOver.popover('enable');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	disable()
	{
		this._$popOver.popover('disable');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	toggleEnabled()
	{
		this._$popOver.popover('toggleEnabled');
		return this;
	}
	/**
	 *
	 * @returns {PopOver}
	 */
	update()
	{
		this._$popOver.popover('update');
		return this;
	}
	/**
	 *
	 * @param selector
	 * @param options
	 * @returns {*|jQuery}
	 */
	static create(selector, options = {})
	{
		return $(selector).popover(options);
	}
}

/**
 * @class {Select}
 */
export class Select extends Input
{
	/**
	 *
	 * @param id
	 * @param name
	 * @param options
	 */
	constructor(id, name = null, options = {})
	{
		super(id, name, { ...{ autoMask:false }, ...options });
		this._multiple = this.$selector.attr('multiple') === 'multiple';
		if(this.$selector.attr('value')) this.value = this.$selector.attr('value');
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this.$selector.val();
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		this.$selector.val(value).change();
	}

	/**
	 *
	 * @returns {boolean}
	 */
	get multiple()
	{
		return this._multiple;
	}
}



/**
 * @class {Select2}
 */
export class Select2 extends Select
{
	/**
	 *
	 * @param {String} selector
	 * @param {Object} options
	 */
	constructor(selector, options = {})
	{
		super(selector, { minimumResultsForSearch: -1, autoBuild:false, ...options });

		this._$select2 = this.$selector.addClass('select2-fix').select2(this.options);

		this.build();
	}
	/**
	 *
	 * @returns {*}
	 */
	get value()
	{
		return this.ids;
	}
	/**
	 *
	 * @param value
	 */
	set value(value)
	{
		if(this.multiple) this.$selector.val(String(value || '').split('|')).trigger('change');
		else this.$selector.val(value).trigger('change');
	}
	/**
	 *
	 * @returns {Select2}
	 */
	clear()
	{
		if(this.multiple) this.$selector.val(null).trigger('change');
		else this.$selector.select2().empty().select2(this.options);
		return this;
	}
	/**
	 * Get selected data
	 * @returns {array}
	 */
	get data()
	{
		return this._$selector.select2('data');
	}

	/**
	 *
	 * @returns {*}
	 */
	get $select2()
	{
		return this._$select2;
	}

	/**
	 *
	 * @returns {[]}
	 */
	get ids()
	{
		return this.data.map(selection => selection.id || null);
	}
	/**
	 *
	 * @returns {[]}
	 */
	get labels()
	{
		return this.data.map(selection => selection.text || null);
	}
	/**
	 *
	 * @returns {[]}
	 */
	get selected()
	{
		return this.data.filter(selection => selection.selected === true);
	}
}
/**
 *
 * @type {{SUCCESS: string, ERROR: string, INFO: string, WARNING: string, QUESTION: string}}
 */
const ICONS =
{
	'SUCCESS': 'success',
	'ERROR': 'error',
	'WARNING': 'warning',
	'INFO': 'info',
	'QUESTION': 'question'
};
/**
 *
 * @type {{createSuccessText: string, updateErrorText: string, serverErrorText: string, serverErrorTitle: string, errorTitle: string, sendingRequestText: string, successTitle: string, createErrorText: string, validationErrorText: string, requestErrorText: string, updateSuccessText: string}}
 */
const MESSAGES =
{
	successTitle:'<h3>Sucesso</h3>',
	errorTitle:'<h3>Erro</h3>',
	serverErrorTitle:'<h3>Erro de servidor</h3>',
	createSuccessText:'<p>Registro criado com sucesso!</p>',
	updateSuccessText:'<p>Registro atualizado com sucesso!</p>',
	createErrorText:'<p>Falha ao criar o registro, tente novamente mais tarde!</p>',
	updateErrorText:'<p>Falha ao atualizar o registro, tente novamente mais tarde!</p>',
	validationErrorText:'<p>O formulário possui erro (s), favor corrigi-lo (s) antes de prosseguir.</p>',
	requestErrorText:'<p>Erro de servidor, tente novamente mais tarde!</p>',
	sendingRequestText:'<p>Enviando sua solicitação, por favor aguarde...</p>',
	serverErrorText:'<p>Favor informar à equipe de desenvolvedores.</p>',
	warningTitle: '<p>Aviso</p>'
};
/**
 *
 * @type {{iconColor: string, customClass: {popup: string}}}
 */
const COLOR_OPTIONS =
{
	iconColor: 'white',
	customClass: { popup: 'colored-toast' },
}
/**
 * @class {SweetAlert2}
 */
export class SweetAlert2
{
	constructor()
	{
		throw new Error('SweetAlert2 must not be instantiated');
	}
	/**
	 *
	 * @returns {*}
	 */
	static showLoading(text = SweetAlert2.MESSAGES.sendingRequestText, completeHandler = () => { })
	{
		return Swal.fire
		({
			html: text,
			didOpen: () => { Swal.showLoading() },
			willClose: () => { Swal.hideLoading(); },
			didClose: () => { completeHandler(); },
			icon: 'info',
			showConfirmButton: false,
			allowOutsideClick: false,
			allowEscapeKey: false
		});
	}
	/**
	 *
	 * @returns {*}
	 */
	static hideLoading()
	{
		return Swal.hideLoading();
	}

	/**
	 *
	 * @param icon
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alert(icon, title, html, options = {}, completeHandler = () => { })
	{
		return Swal.fire({ ...options, ...{ icon, title, html, didClose: () => { completeHandler(); } } });
	}

	/**
	 *
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alertSuccess(title = MESSAGES.successTitle, html = MESSAGES.createSuccessText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.alert('success', title, html, options, completeHandler);
	}
	/**
	 *
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alertWarning(title = MESSAGES.warningTitle, html = '', options = {}, completeHandler = () => { })
	{
		return SweetAlert2.alert('error', title, html, options, completeHandler, completeHandler);
	}
	/**
	 *
	 * @param title
	 * @param html
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static alertError(title = MESSAGES.errorTitle, html = MESSAGES.createErrorText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.alert('error', title, html, options, completeHandler, completeHandler);
	}

	/**
	 *
	 * @param icon
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toast(icon, title, options = {}, completeHandler = () => { })
	{
		return Swal.mixin({
			toast: true,
			position: 'bottom-end',
			timer: 5000,
			timerProgressBar: true,
			animation: true,
			showConfirmButton: false,
			showCloseButton:true,
			showCancelButton: false,
			allowEscapeKey: true,
			allowOutsideClick: true,
			didClose: () => { completeHandler(); },
			didOpen: (toast) =>
			{
				toast.addEventListener('mouseenter', Swal.stopTimer)
				toast.addEventListener('mouseleave', Swal.resumeTimer)
			}
		}).fire
		({
			...options,
			...{ icon, title }
		});
	}

	/**
	 *
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toastSuccess(title = MESSAGES.createSuccessText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.toast('success', title, { ...COLOR_OPTIONS, ...options }, completeHandler);
	}

	/**
	 *
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toastError(title = MESSAGES.createErrorText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.toast('error', title, { ...COLOR_OPTIONS, ...options }, completeHandler);
	}

	/**
	 *
	 * @param title
	 * @param options
	 * @param completeHandler
	 * @returns {*}
	 */
	static toastValidationError(title = MESSAGES.validationErrorText, options = {}, completeHandler = () => { })
	{
		return SweetAlert2.toast('error', title, { ...COLOR_OPTIONS, ...options }, completeHandler);
	}
	/**
	 *
	 * @returns {{errorText: string, errorTitle: string, successTitle: string, successText: string, validationErrorText: string, requestErrorText: string}}
	 * @constructor
	 */
	static get MESSAGES()
	{
		return MESSAGES;
	}
}

/**
 * @class Tabs
 */
export class Tabs extends DynamicObject
{
	/**
	 * Tabs constructor
	 * @param {string|HTMLElement} selector
	 * @param {Object} [options = {}]
	 */
	constructor(selector, options = {  })
	{
		super(selector, options);

		this._$tab = this._$element;
		this._$tabContent = $(`${this._selector}-content`);
		this._currentTab = 0;
		this._lastHash = window.location.hash;

		this._$tabLinks = this._$tab.find('.nav-item');
		this._totalTabs = this._$tabLinks.length;
		this._tabsCounter = 0;

		this._$tab.on('click', (e) => { window.location.hash = $(e.target).attr('href'); });
		if (!window.location.hash) window.location.hash = $(this._$tab.find('.nav-link')[0]).attr('href');

		$(window).on('hashchange', (e) => { this.update(); });

		console.log('this._totalTabs', this._totalTabs);
	}
	update()
	{
		const hash = window.location.hash;

		if (hash === '#undefined')
		{
			window.location.hash = this._lastHash;
			return;
		}

		this._$tab.find('.active').removeClass('active show');
		this._$tabContent.find('.active').removeClass('active show');

		$(`[href="${hash}"]`).addClass('active show');

		this._currentTab = $(hash).addClass('active show').index();

		this._$tab.find('.selected').removeClass('selected');
		this._$tab.find('.active').closest('li').addClass('selected');

		this._lastHash = hash.replace('#', '');

		this._tabsCounter = this._currentTab;

		this._change.dispatch(this._currentTab, this._lastHash);
	}
	previous()
	{
		if(--this._tabsCounter < 0) this._tabsCounter = this._totalTabs -1;
		console.log('previous tab', this._tabsCounter);
		$(this._$tabLinks[this._tabsCounter]).find('a').trigger({ type:'click' });
	}
	next()
	{
		if(++this._tabsCounter >= this._totalTabs) this._tabsCounter = 0;
		console.log('next tab', this._tabsCounter);
		$(this._$tabLinks[this._tabsCounter]).find('a').trigger({ type:'click' });
	}
}
