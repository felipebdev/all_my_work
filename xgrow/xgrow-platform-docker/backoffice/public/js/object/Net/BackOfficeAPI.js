import API from './API.js';
/**
 * @class BackOfficeAPI
 */
export default class BackOfficeAPI extends API
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
