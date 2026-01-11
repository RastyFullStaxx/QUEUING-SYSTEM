import { createRouter, createWebHistory } from 'vue-router'
import Display from './pages/Display.vue'

const routes = [
  { path: '/', redirect: '/display' },
  { path: '/display', component: Display },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
