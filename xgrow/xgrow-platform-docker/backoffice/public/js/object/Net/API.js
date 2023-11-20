/**
 * @class {API}
 */
import HTTPResponse from './HTTPResponse.js';

export default class API
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
