/**
 * @class CEP
 */
import Validator from '../../Data/Validator.js';
/**
 * Service url
 * @type {string}
 */
const SERVICE_CEP_URL = `https://viacep.com.br/ws`;

/**
 * @class {CEP}
 */
export default class CEP
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
