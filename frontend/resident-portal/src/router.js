import { createRouter, createWebHistory } from 'vue-router'
import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import Dashboard from './pages/Dashboard.vue'
import Profile from './pages/Profile.vue'

const routes = [
  { path: '/', component: Login, alias: '/welcome' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/dashboard', component: Dashboard },
  { path: '/profile', component: Profile },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  if (to.path !== '/dashboard') return true
  const cached = localStorage.getItem('resident_profile')
  if (!cached) return '/'
  try {
    const profile = JSON.parse(cached)
    if (profile?.status === 'approved') return true
  } catch (err) {
    return '/'
  }
  return '/'
})

export default router
