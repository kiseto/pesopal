<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8">
        <div class="flex flex-col">
          <h1 class="text-4xl font-bold text-gray-900">Welcome back, <span class="text-self-blue">{{ userName }}</span></h1>
          <p class="text-lg text-gray-500">See your recent activity and money flow at a glance</p>
        </div>
      </div>
      <!-- 1st Card Row -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
            <BanknotesIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="text-blue-600 text-3xl font-bold mb-2">₱{{ dashboardData.summary?.total_balance ? dashboardData.summary.total_balance.toLocaleString() : '0' }}</div>
          <div class="text-gray-600 text-md">Total Balance</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col ">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
            <ArrowTrendingUpIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="text-green-600 text-3xl font-bold mb-2">₱{{ dashboardData.summary?.total_income ? dashboardData.summary.total_income.toLocaleString() : '0' }}</div>
          <div class="text-md text-gray-600">Income</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col ">
          <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
            <ArrowTrendingDownIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="text-red-600 text-3xl font-bold mb-2">₱{{ dashboardData.summary?.total_expenses ? dashboardData.summary.total_expenses.toLocaleString() : '0' }}</div>
          <div class="text-md text-gray-600">Expenses</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col ">
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
            <BanknotesIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="text-purple-600 text-3xl font-bold mb-2">₱{{ dashboardData.summary?.total_savings ? dashboardData.summary.total_savings.toLocaleString() : '0' }}</div>
          <div class="text-md text-gray-600">Savings</div>
        </div>
      </div>
      <!-- 2nd Card Row -->
      <div class="grid grid-cols-1 md:grid-cols-7 gap-8 mb-12">
        <!-- 70% width for graph -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 min-h-[30rem] p-6 md:col-span-5">
          <h1 class="font-bold text-xl mb-4">Weekly Expenses (Last 7 Days)</h1>
          <div class="h-96 w-full" v-if="!isLoading">
            <ApexChart 
              type="line" 
              height="100%" 
              width="100%" 
              :options="lineChartOptions" 
              :series="lineChartSeries" 
            />
          </div>
          <div v-else class="h-96 w-full flex items-center justify-center">
            <div class="text-gray-500">Loading chart...</div>
          </div>
        </div>
        <!-- 30% width for savings goals -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 h-80 md:col-span-2 flex flex-col">
          <div class="flex items-center justify-between gap-6 mb-6 sticky top-0 bg-white z-10">
            <span class="text-xl font-bold text-neutral-800">Savings Goals</span>
            <router-link to="/dashboard/budgeting" class="text-green-400 cursor-pointer">
              <PlusIcon class="h-6 w-6" />
            </router-link>
          </div>
          <ul class="space-y-4">
            <li v-for="goal in dashboardData.savings_goals" :key="goal.title" class="flex justify-between items-center">
              <span class="text-gray-700 font-semibold">{{ goal.title }}</span>
              <span class="text-gray-500">{{ goal.progress_text }}</span>
            </li>
            <li v-if="!dashboardData.savings_goals?.length" class="text-gray-500 text-center py-4">
              No savings goals yet
            </li>          
          </ul>
        </div>
       
      </div>
      <!-- 3rd Card Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col h-auto">
          <div class="flex items-center gap-3 mb-6 sticky top-0 bg-white z-10">
            <span class="text-xl font-bold text-neutral-800">Recent Transactions</span>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="transaction in dashboardData.recent_transactions" :key="transaction.description + transaction.date" class="py-4 flex items-center">
              <span class="text-gray-700 flex-1">{{ transaction.description }}</span>
              <span :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'" class="w-32 text-center">{{ transaction.formatted_amount }}</span>
              <span class="text-gray-400 text-sm flex-1 text-right">{{ transaction.date }}</span>
            </li>
            <li v-if="!dashboardData.recent_transactions?.length" class="py-4 text-gray-500 text-center">
              No recent transactions
            </li>
          </ul>
        </div>
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col h-80">
          <div class="flex items-center gap-3 mb-6 sticky top-0 bg-white z-10">
            
            <span class="text-xl font-bold text-neutral-800">Quick Actions</span>
          </div>
          <div class="flex flex-col gap-4">
            <router-link to="/dashboard/transactions" class="cursor-pointer bg-blue-600 text-white py-3 rounded-lg font-semibold transition hover:bg-blue-700 text-center">Add Transaction</router-link>
            <router-link to="/dashboard/budgeting" class="bg-green-600 cursor-pointer text-white py-3 rounded-lg font-semibold transition hover:bg-green-700 text-center">Create Goal</router-link>
            <router-link to="/dashboard/reports" class="bg-purple-600 cursor-pointer text-white py-3 rounded-lg font-semibold transition hover:bg-purple-700 text-center">View Reports</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { 
  BanknotesIcon, 
  ArrowTrendingUpIcon, 
  ArrowTrendingDownIcon,
  PlusIcon 
} from '@heroicons/vue/24/outline'
// ApexChart is globally registered in main.js
import ApiService from '../services/ApiService.js'

// Data
const dashboardData = ref({
  summary: {},
  recent_transactions: [],
  savings_goals: [],
  chart_data: { labels: [], expenses: [] }
})
const userName = ref('User')
const isLoading = ref(true)

// Chart configuration
const lineChartOptions = ref({
  chart: {
    height: 400,
    type: 'line',
    id: 'spending-line', 
    toolbar: { show: false },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800
    }
  },
  xaxis: {
    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    labels: { 
      style: { 
        fontSize: '14px',
        fontWeight: 500
      } 
    },
    title: {
      text: 'Day of Week'
    }
  },
  yaxis: { 
    labels: { 
      style: { fontSize: '14px' },
      formatter: function (value) {
        return '₱' + value.toLocaleString()
      }
    },
    title: {
      text: 'Amount (₱)'
    }
  },
  stroke: { 
    curve: 'smooth', 
    width: 3 
  },
  colors: ['#dc2626'], // Red color for expenses
  dataLabels: { enabled: false },
  grid: { 
    show: true,
    borderColor: '#e0e6ed',
    strokeDashArray: 5
  },
  tooltip: {
    y: {
      formatter: function (value) {
        return '₱' + value.toLocaleString()
      }
    }
  },
  markers: {
    size: 5,
    colors: ['#dc2626'],
    strokeColors: '#fff',
    strokeWidth: 2,
    hover: {
      size: 7
    }
  }
})

const lineChartSeries = ref([
  { name: 'Expenses', data: [] }
])

// Load dashboard data
const loadDashboardData = async () => {
  try {
    isLoading.value = true
    
    // Load user profile
    const profileResult = await ApiService.getUserProfile()
    if (profileResult.success) {
      userName.value = profileResult.data.user.first_name
    }
    
    // Load dashboard summary
    const dashboardResult = await ApiService.getDashboardSummary()
    console.log('Raw dashboard result:', dashboardResult)
    
    if (dashboardResult.success) {
      dashboardData.value = dashboardResult.data
      
      // Log chart data for debugging
      console.log('Chart data received:', dashboardData.value.chart_data)
      console.log('Weekly expense breakdown:', dashboardData.value.debug?.weekly_data)
      
      // Update chart data with real expense data from API
      if (dashboardData.value.chart_data && dashboardData.value.chart_data.labels.length > 0) {
        console.log('Using real database chart data:', {
          labels: dashboardData.value.chart_data.labels,
          expenses: dashboardData.value.chart_data.expenses
        })
        
        // Update chart options with real labels
        lineChartOptions.value = {
          ...lineChartOptions.value,
          xaxis: {
            ...lineChartOptions.value.xaxis,
            categories: dashboardData.value.chart_data.labels
          }
        }
        
        // Update chart series with real expense data
        lineChartSeries.value = [{
          name: 'Daily Expenses',
          data: dashboardData.value.chart_data.expenses
        }]
        
        // Force chart refresh
        await nextTick()
        console.log('Chart updated with real data')
      } else {
        console.log('No chart data from API, using fallback')
        // Fallback to default days if no data
        lineChartOptions.value = {
          ...lineChartOptions.value,
          xaxis: {
            ...lineChartOptions.value.xaxis,
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
          }
        }
        lineChartSeries.value = [{
          name: 'Daily Expenses',
          data: [0, 0, 0, 0, 0, 0, 0]
        }]
      }
    } else {
      console.error('Dashboard load failed:', dashboardResult)
      console.error('Success status:', dashboardResult.success)
      console.error('Message:', dashboardResult.message || 'No message')
      console.error('Data:', dashboardResult.data || 'No data')
      // Use fallback data
      dashboardData.value = {
        summary: { total_balance: 0, total_income: 0, total_expenses: 0, total_savings: 0 },
        recent_transactions: [],
        savings_goals: [],
        chart_data: { labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], expenses: [0, 0, 0, 0, 0, 0, 0] }
      }
      
      // Set fallback chart data
      lineChartSeries.value = [{
        name: 'Daily Expenses',
        data: [0, 0, 0, 0, 0, 0, 0]
      }]
    }
    
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  } finally {
    isLoading.value = false
  }
}

// Load data on component mount
onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
</style>
