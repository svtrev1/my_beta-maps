
import axios from 'axios';

// Базовый URL для API и URL для получения CSRF-cookie
const API_URL = import.meta.env.VITE_API_URL || 'http://betamaps.admsurgut.ru/api';
const SANCTUM_BASE = API_URL.replace(/\/api$/, '');

// Экземпляр axios для API-запросов
const http = axios.create({
  baseURL: API_URL,
  withCredentials: true, // обязательно для передачи cookies
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
});

// Параметры для управления единственным запросом за CSRF-cookie
let csrfPromise = null;

// Перед каждым модифицирующим запросом обновляем CSRF-cookie
http.interceptors.request.use(async config => {
  const method = config.method ? config.method.toLowerCase() : 'get';
  // Обновляем CSRF-токен только перед non-GET и non-logout запросами
  if (method !== 'get' && !config.url.includes('/sanctum/csrf-cookie')) {
    if (!csrfPromise) {
      csrfPromise = axios.get(`${SANCTUM_BASE}/sanctum/csrf-cookie`, { withCredentials: true })
        .finally(() => {
          csrfPromise = null;
        });
    }
    await csrfPromise;
  }
  return config;
}, error => Promise.reject(error));

export default http;
