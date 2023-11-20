import DOMComponent from './DOMComponent.js';
import StringUtil from '../Util/StringUtil.js';

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
export default class FormAlerts extends DOMComponent
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
