import { createRouter, createWebHistory } from 'vue-router'
import Login from './pages/Login.vue'
import Dashboard from './pages/Dashboard.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login, meta: { guestOnly: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const clearAdminSession = () => {
  localStorage.removeItem('admin_token')
  localStorage.removeItem('admin_profile')
}

const getAdminSession = () => {
  const token = localStorage.getItem('admin_token')
  const cached = localStorage.getItem('admin_profile')
  if (!token || !cached) {
    if (token || cached) {
      clearAdminSession()
    }
    return { approved: false }
  }
  try {
    const profile = JSON.parse(cached)
    return { approved: profile?.status === 'approved' }
  } catch (err) {
    clearAdminSession()
    return { approved: false }
  }
}

router.beforeEach((to) => {
  const { approved } = getAdminSession()
  if (to.meta.requiresAuth && !approved) {
    clearAdminSession()
    return '/login'
  }
  if (to.meta.guestOnly && approved) {
    return '/dashboard'
  }
  return true
})

export default router
