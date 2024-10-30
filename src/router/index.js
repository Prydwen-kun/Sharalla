// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Signup from '../views/auth/authView.vue';
import Dashboard from '../views/dashboard/dashboardView.vue';
import NotFound from '../views/error/404View.vue'

const routes = [
  { path: '/', name: 'Signup', component: Signup },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard },
  //catch-all route for 404 need to stay as last route
  { path: '/:pathMatch(.*)*', name: 'NotFound', component:NotFound },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;
