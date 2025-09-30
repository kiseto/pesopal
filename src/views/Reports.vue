<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header & Controls -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Reports & Analytics</h1>
          <div class="text-lg text-gray-500">Analyze your spending patterns and financial trends</div>
        </div>
        <div class="flex gap-4 items-center">
          <select v-model="selectedRange" class="cursor-pointer text-gray-900  border border-gray-300 rounded px-6 py-2">
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
            <option value="365">Last year</option>
          </select>
          <button class="cursor-pointer bg-green-600 text-white px-4 py-2 rounded font-semibold hover:bg-green-700 transition">Export Report</button>
        </div>
      </div>
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Total Income</div>
          <div class="text-3xl font-bold text-green-600">₱{{ summary.income.toLocaleString() }}</div>
          <div class="text-base text-green-700 mt-2">+12% from last month</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Total Expenses</div>
          <div class="text-3xl font-bold text-red-600">₱{{ summary.expenses.toLocaleString() }}</div>
          <div class="text-base text-red-700 mt-2">+8% from last month</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Net Savings</div>
          <div class="text-3xl font-bold text-blue-600">₱{{ summary.savings.toLocaleString() }}</div>
          <div class="text-base text-blue-700 mt-2">+25% from last month</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Avg. Daily Spend</div>
          <div class="text-2xl font-bold text-gray-800">₱{{ summary.avgDaily.toLocaleString() }}</div>
          <div class="text-base text-gray-500 mt-2">Based on {{ selectedRange }} days</div>
        </div>
      </div>
      
      <!-- Charts Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 h-120 flex flex-col justify-center">
          <h1 class="text-gray-900 font-bold text-xl pt-4">Spending by Category</h1>
          <ApexChart type="donut" width="100%" height="100%" :options="donutChartOptions" :series="donutChartSeries" />
        </div>

        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 h-120 flex flex-col justify-center">
          <h1 class="text-gray-900 font-bold text-xl pt-4">Monthly Spending Trends</h1>
          <ApexChart type="line" width="100%" height="100%" :options="lineChartOptions" :series="lineChartSeries" />
        </div>
      </div>
      
      <!-- Breakdown & Export Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Category Breakdown -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="text-xl font-semibold text-gray-800 mb-6">Category Breakdown</div>
          <div v-for="cat in breakdown" :key="cat.title" class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <span :class="cat.color" class="h-5 w-5 rounded-full inline-block"></span>
              <span class="text-md">{{ cat.title }}</span>
            </div>
            <div class="text-right">
              <span class="text-gray-900 font-bold text-lg">₱{{ cat.amount.toLocaleString() }}</span>
              <span class="text-base text-gray-500 ml-2">{{ cat.percent }}%</span>
            </div>
          </div>
        </div>
        <!-- Export Reports -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="text-xl font-semibold text-gray-800 mb-6">Export Reports</div>
          <div class="flex flex-col gap-4 mb-6">
            <button class="cursor-pointer bg-red-600 text-white px-6 py-3 rounded font-semibold hover:bg-red-700 flex items-center gap-3"><span>Download PDF Report</span></button>
            <button class="cursor-pointer bg-blue-600 text-white px-6 py-3 rounded  font-semibold hover:bg-blue-700 flex items-center gap-3"><span>Export to CSV</span></button>
            <button class="cursor-pointer bg-green-600 text-white px-6 py-3 rounded  font-semibold hover:bg-green-700 flex items-center gap-3"><span>Export to Excel</span></button>
            
          </div>
          
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const selectedRange = ref(30)
const schedule = ref('Weekly')
const summary = ref({
  income: 75000,
  expenses: 56250,
  savings: 18750,
  avgDaily: 1875
})
const breakdown = ref([
  { title: 'Food & Dining', amount: 18750, percent: 33.3, color: 'bg-green-400' },
  { title: 'Transportation', amount: 11250, percent: 20, color: 'bg-blue-400' },
  { title: 'Entertainment', amount: 9375, percent: 16.7, color: 'bg-purple-400' },
  { title: 'Shopping', amount: 16875, percent: 30, color: 'bg-yellow-400' },
])

//Apex Charts
// Donut Chart for Spending by Category
const donutChartOptions = ref({
  labels: breakdown.value.map(cat => cat.title),
  colors: ['#34d399', '#60a5fa', '#a78bfa', '#fbbf24'],
  legend: {
    position: 'bottom'
  }
})

const donutChartSeries = ref(breakdown.value.map(cat => cat.amount))

// Line Chart for Monthly Spending Trends
const lineChartOptions = ref({
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  chart: {
    width: '100%'
  },
  stroke: {
    curve: 'smooth',
    width: 3
  }
})

const lineChartSeries = ref([
  {
    name: 'Monthly Spending',
    data: [45000, 52000, 48000, 61000, 55000, 58000, 62000, 59000, 56000, 63000, 51000, 49000]
  }
])



</script>

<style scoped>
</style>
