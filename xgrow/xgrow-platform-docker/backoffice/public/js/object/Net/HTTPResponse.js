/**
 * @class {HTTPResponse}
 */
export default class HTTPResponse
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
