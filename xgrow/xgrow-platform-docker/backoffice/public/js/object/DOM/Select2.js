import DOMComponent from './DOMComponent.js';
import Select from './Select.js';

/**
 * @class {Select2}
 */
export default class Select2 extends Select
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
