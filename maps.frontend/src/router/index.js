import { createRouter, createWebHistory } from 'vue-router';
import Map from '../components/Map.vue';
import Login from '../components/Login.vue';

const routes = [
  { path: '/', component: Map },
  { path: '/login', component: Login },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
