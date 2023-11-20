import API from './API.js';

export default class UserProfileAPI extends API
{
	constructor()
	{
		super('');
	}

	read()
	{
		return this.get(`/profile/read`);
	}

	update(data)
	{
		return this.put(`/profile/update`, data);
	}

	uploadDocument()
	{
		return this.post(`/profile/verify/document`, []);
	}
}
