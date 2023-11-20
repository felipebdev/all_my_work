import axios from 'axios';
import { defineStore } from 'pinia'
import { useLoadingStore } from './loading';


export const useUploadImageS3Store = defineStore('uploadImageS3', {
    state: () => ({
        loadingStore: useLoadingStore(),
        path: null
    }),
    actions: {
        async uploadToS3(obj, pathS3 = "/learning-area/upload-image") {
            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0]);
                this.loadingStore.setLoading(true);
                const res = await axios.post(pathS3, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                this.loadingStore.setLoading();
                return res.data.response.file;
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
            }
        }

    }
})
