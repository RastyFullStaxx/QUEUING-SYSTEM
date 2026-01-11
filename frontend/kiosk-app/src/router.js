import { createRouter, createWebHistory } from 'vue-router'
import Welcome from './pages/Welcome.vue'
import Scan from './pages/Scan.vue'
import Services from './pages/Services.vue'
import Confirm from './pages/Confirm.vue'
import Ticket from './pages/Ticket.vue'

const routes = [
  { path: '/', redirect: '/welcome' },
  { path: '/welcome', component: Welcome },
  { path: '/scan', component: Scan },
  { path: '/services', component: Services, meta: { requiresApproval: true } },
  { path: '/confirm', component: Confirm, meta: { requiresApproval: true } },
  { path: '/ticket', component: Ticket, meta: { requiresApproval: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  if (to.meta.requiresApproval) {
    const approved = localStorage.getItem('kiosk_approved') === 'true'
    if (!approved) {
      return '/scan'
    }
  }
  return true
})

export default router
