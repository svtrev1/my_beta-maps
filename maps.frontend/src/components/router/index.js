import { createRouter, createWebHistory } from 'vue-router';
import Map from '../Map.vue';
import Login from '../Login.vue';
import AdminPanel from '../AdminPanel.vue'; // Модальное окно для администратора (например)

const routes = [
  { path: '/', component: Map },
  { path: '/login', component: Login },
  { path: '/admin-panel', component: AdminPanel, meta: { requiresAuth: true } } // Путь для админского окна
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Для проверки авторизации перед доступом к админским страницам
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth'); // или другой метод проверки авторизации
  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next('/login'); // Если не авторизован, перенаправляем на страницу логина
  } else {
    next();
  }
});

export default router;
