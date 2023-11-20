export default class CEP
{
	constructor()
	{

	}
	static async getAddress (cep = null)
	{
		if (!cep || cep.length !== 10) return false;

		const result = await fetch(`https://viacep.com.br/ws/${cep.replace(/[^0-9]/g, '')}/json/`);
		return result.json();
	}
}
