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
export default class StringUtil
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
