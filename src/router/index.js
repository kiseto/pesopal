import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import DashboardHome from '../views/DashboardHome.vue'
import Transactions from '../views/Transactions.vue'
import Invoices from '../views/Invoices.vue'
import Budgeting from '../views/Budgeting.vue'
import Reports from '../views/Reports.vue'
import TestAPI from '../views/TestAPI.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/register',
      name: 'Register',
      component: Register
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/test-api',
      name: 'TestAPI',
      component: TestAPI
    },
    {
      path: '/dashboard',
      component: Dashboard,
      children: [
        {
          path: '',
          name: 'DashboardHome',
          component: DashboardHome
        },
        {
          path: 'transactions',
          name: 'Transactions',
          component: Transactions
        },
        {
          path: 'invoices',
          name: 'Invoices',
          component: Invoices
        },
        {
          path: 'budgeting',
          name: 'Budgeting',
          component: Budgeting
        },
        {
          path: 'reports',
          name: 'Reports',
          component: Reports
        },
        
        
        // Add other dashboard subpages here
        // { path: 'settings', component: Settings },
        // ...
      ]
    }
  ],
})

export default router
