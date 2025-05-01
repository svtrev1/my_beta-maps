import axios from 'axios';

const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://betamaps.admsurgut.ru/api',
  withCredentials: true,            // обязательно для Sanctum
  headers: { 'Accept': 'application/json' }
});

export default http;
