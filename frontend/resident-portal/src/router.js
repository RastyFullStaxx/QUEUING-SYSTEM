import { createRouter, createWebHistory } from 'vue-router'
import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import Dashboard from './pages/Dashboard.vue'
import Profile from './pages/Profile.vue'

const routes = [
  { path: '/', component: Login, alias: '/welcome', meta: { guestOnly: true } },
  { path: '/login', component: Login, meta: { guestOnly: true } },
  { path: '/register', component: Register, meta: { guestOnly: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/profile', component: Profile, meta: { requiresAuth: true } },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const clearResidentSession = () => {
  localStorage.removeItem('resident_token')
  localStorage.removeItem('resident_profile')
}

const getResidentSession = () => {
  const token = localStorage.getItem('resident_token')
  const cached = localStorage.getItem('resident_profile')
  if (!token || !cached) {
    if (token || cached) {
      clearResidentSession()
    }
    return { approved: false }
  }
  try {
    const profile = JSON.parse(cached)
    return { approved: profile?.status === 'approved' }
  } catch (err) {
    clearResidentSession()
    return { approved: false }
  }
}

router.beforeEach((to) => {
  const { approved } = getResidentSession()
  if (to.meta.requiresAuth && !approved) {
    clearResidentSession()
    return '/'
  }
  if (to.meta.guestOnly && approved) {
    return '/dashboard'
  }
  return true
})

export default router
