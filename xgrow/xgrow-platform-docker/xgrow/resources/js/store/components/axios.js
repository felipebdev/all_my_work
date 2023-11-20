import { defineStore } from 'pinia'
import axios from "axios";


export const useAxiosStore = defineStore('axios', {
    state: () => ({
        axiosHeader: null,
        axiosUrl: null
    }),
    actions: {
        /** The URL may be changed when use another url to do auth */
        setAxiosHeader: async function (url = '/learning-area/producer-connect') {
            const res = await axios.get(url);
            this.axiosHeader = { headers: { Authorization: 'Bearer ' + res.data.response.atx } };
            this.axiosUrl = res.data.response.url;
        },
    }
})
