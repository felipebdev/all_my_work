import axios from 'axios';

export const axiosInstance = axios.create({
  baseURL: 'https://la-config-develop.xgrow.com/v1/api',
  timeout: 5000
});

export const authProducerAPI = await axiosInstance.post('/producer/auth', {
  platformId: '43d27ccc-d74e-4479-92dc-6d37b2b2aeb2',
  producerId: '2'
});

export const axiosConfig = {
  headers: {
    Authorization: `Bearer ${authProducerAPI.data.token}`
  }
};
