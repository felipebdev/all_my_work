import DOMComponent from './DOMComponent.js';

/**
 * @class {DatePicker}
 */
export default class DatePicker extends DOMComponent
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
