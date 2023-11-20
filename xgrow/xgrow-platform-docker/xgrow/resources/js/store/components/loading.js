import {defineStore} from 'pinia'


export const useLoadingStore = defineStore('loading', {
    state: () => ({
        isLoading: false,
        status: 'loading',
    }),
    actions: {
        setLoading(val = false) {
            this.isLoading = val;
        }
    }
})
