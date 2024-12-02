// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import Signup from '../views/auth/authView.vue'
import Dashboard from '../views/dashboard/dashboardView.vue'
import NotFound from '../views/error/404View.vue'
import Login from '@/views/auth/loginView.vue'
import LoginProcessView from '@/views/auth/loginProcessView.vue'
import ProfileView from '@/views/users/profileView.vue'
import Friend from '@/views/users/friendView.vue'

const routes = [
  { path: '/', name: 'Signup', component: Signup },
  { path: '/login', name: 'Login', component: Login },
  {
    path: '/loginProcessing',
    name: 'LoginProcessView',
    component: LoginProcessView,
  },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard },
  { path: '/profile', name: 'Profile', component: ProfileView },
  { path: '/friend', name: 'Friend', component: Friend },
  //catch-all route for 404 need to stay as last route
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router
