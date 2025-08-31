import { createRouter, createWebHistory } from 'vue-router'

// pages (create stubs for now)
import Login from '@/views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import Register from '@/views/Register.vue'
import Budget from '@/views/Budget.vue'
import AllotmentUpdate from '@/views/AllotmentUpdates.vue'
import SpendingChart from '@/views/SpendingChart.vue'
import GoalsAdd from '@/views/GoalsAdd.vue'
import CalcAndConverter from '@/views/CalcAndConverter.vue'
import SavingsGateway from '@/views/SavingsGateway.vue'
import AdminSavings from '@/views/AdminSavings.vue'
import EditProfile from '@/views/EditProfile.vue'
import AdminDashboard from '@/views/AdminDashboard.vue'


const routes = [
  { path: '/login', name: 'login', component: Login, meta: { public: true } },
  { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/admin/dashboard', name: 'admin.dashboard', component: AdminDashboard, meta: { requiresAuth: true } },
  { path: '/register', name: 'register', component: Register, meta: { public: true } },
  { path: '/budget', name: 'budget', component: Budget, meta: { requiresAuth: true } },
  { path: '/allotments/update', name: 'allotments.update', component: AllotmentUpdate, meta: { requiresAuth: true } },
  { path: '/goals/add', name: 'goals.add', component: GoalsAdd, meta: { requiresAuth: true } },
  { path: '/goals', name: 'goals.index', component: () => import('@/views/GoalsIndex.vue'), meta: { requiresAuth: true } },
  { path: '/spending', name: 'spending', component: SpendingChart, meta: { requiresAuth: true } },
  { path: '/tools', name: 'CalcAndConverter', component: CalcAndConverter },
  { path: '/savings', name: 'savings', component: SavingsGateway, meta: { requiresAuth: true } },
  { path: '/admin/savings', name: 'admin.savings', component: AdminSavings, meta: { requiresAuth: true } },
  { path: '/profile/edit', name: 'profile.edit', component: EditProfile, meta: { requiresAuth: true } },
  { path: '/', redirect: '/dashboard' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from, next) => {
  const token = localStorage.getItem('token')
  const role  = localStorage.getItem('role') || 'user'

  // if route is protected and no token -> go login
  if (to.meta?.requiresAuth && !token && to.name !== 'login') {
    return next({ name: 'login' })
  }

  // UI-only block for admin dashboard
  if (to.name === 'admin.dashboard' && role !== 'admin') {
    return next({ name: 'dashboard' })
  }

  next()
})

// simple guard: require token in localStorage
router.beforeEach((to) => {
  if (to.meta.public) return true
  if (localStorage.getItem('token')) return true
  return { name: 'login' }
})

export default router
