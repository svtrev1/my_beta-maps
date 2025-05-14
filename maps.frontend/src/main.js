import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/index.js'
import './index.css'
import './assets/panel.css'
import axios from 'axios'

axios.defaults.withCredentials;
axios.defaults.baseURL = 'http://betamaps.admsurgut.ru/api';

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.mount('#app')