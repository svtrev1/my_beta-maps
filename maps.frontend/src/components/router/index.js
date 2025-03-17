import { createRouter, createWebHistory } from 'vue-router';
import Map from '../Map.vue';
import Login from '../Login.vue';

const routes = [
  { path: '/', component: Map },
  { path: '/login', component: Login },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
