import { getCookie } from '../functions/helper';

const axios = require('axios');

export const axiosGraphqlClient = axios.create({
    timeout: 15000,
    headers: {
        "content-type": "application/json",
        "Authorization": `Bearer ${getCookie('content.token')}`
    }
});


/**
 * Example for queries
 *
 * const graphqlQuery = {
 *  "query": `query fetchAuthor { author { id name } }`,
 *  "variables": {}
 * };
 */
