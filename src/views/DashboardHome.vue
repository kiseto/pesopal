<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8">
        <div class="flex flex-col">
          <h1 class="text-4xl font-bold text-gray-900">Welcome back, User!</h1>
          <p class="text-lg text-gray-500">See your recent activity and money flow at a glance</p>
        </div>
      </div>
      <!-- 1st Card Row -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">Icon</span></div>
          <div class="text-3xl font-bold mb-2">$12,500</div>
          <div class="text-gray-600 text-md">Total Balance</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col ">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">Icon</span></div>
          <div class="text-3xl font-bold mb-2">$2,300</div>
          <div class="text-md text-gray-600">Income</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col ">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">Icon</span></div>
          <div class="text-3xl font-bold mb-2">$1,800</div>
          <div class="text-md text-gray-600">Expenses</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col ">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">Icon</span></div>
          <div class="text-3xl font-bold mb-2">$3,400</div>
          <div class="text-md text-gray-600">Savings</div>
        </div>
      </div>
      <!-- 2nd Card Row -->
      <div class="grid grid-cols-1 md:grid-cols-7 gap-8 mb-12">
        <!-- 70% width for graph -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 min-h-[30rem] p-3 md:col-span-5">
          <h1 class="font-bold text-xl p-3">Expenses</h1>
          <ApexChart type="line" width="100%" height="100%" :options="lineChartOptions" :series="lineChartSeries" />
        </div>
        <!-- 30% width for savings goals -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 h-80 md:col-span-2 flex flex-col">
          <div class="flex items-center justify-between gap-6 mb-6 sticky top-0 bg-white z-10">
            <span class="text-xl font-bold text-neutral-800">Savings Goals</span>
            <button class="text-green-400 cursor-pointer text-2xl">+</button>
          </div>
          <ul class="space-y-4">
            <li class="flex justify-between items-center">
              <span class="text-gray-700 font-semibold">Vacation Fund</span>
              <span class="text-gray-500">$1,200 / $2,000</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="text-gray-700 font-semibold">Emergency Savings</span>
              <span class="text-gray-500">$800 / $1,500</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="text-gray-700 font-semibold">New Laptop</span>
              <span class="text-gray-500">$400 / $1,200</span>
            </li>
          </ul>
        </div>
      </div>
      <!-- 3rd Card Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col h-80">
          <div class="flex items-center gap-3 mb-6 sticky top-0 bg-white z-10">
            <span class="text-xl font-bold text-neutral-800">Recent Transactions</span>
          </div>
          <ul class="divide-y divide-gray-200">
            <li class="py-4 flex justify-between items-center">
              <span class="text-gray-700">Grocery Store</span>
              <span class="text-red-600">-$120.00</span>
              <span class="text-gray-400 text-sm">Sep 28</span>
            </li>
            <li class="py-4 flex justify-between items-center">
              <span class="text-gray-700">Salary</span>
              <span class="text-green-600">+$2,300.00</span>
              <span class="text-gray-400 text-sm">Sep 27</span>
            </li>
            <li class="py-4 flex justify-between items-center">
              <span class="text-gray-700">Electric Bill</span>
              <span class="text-red-600">-$75.00</span>
              <span class="text-gray-400 text-sm">Sep 25</span>
            </li>
            <li class="py-4 flex justify-between items-center">
              <span class="text-gray-700">Transfer from Savings</span>
              <span class="text-green-600">+$500.00</span>
              <span class="text-gray-400 text-sm">Sep 24</span>
            </li>
          </ul>
        </div>
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col h-80">
          <div class="flex items-center gap-3 mb-6 sticky top-0 bg-white z-10">
            
            <span class="text-xl font-bold text-neutral-800">Quick Actions</span>
          </div>
          <div class="flex flex-col gap-4">
            <button class="cursor-pointer bg-gray-600 text-white py-3 rounded-lg font-semibold transition">Add Transaction</button>
            <button class="bg-gray-600 cursor-pointer text-white py-3 rounded-lg font-semibold transition">Create Goal</button>
            <button class="bg-gray-600 cursor-pointer text-white py-3 rounded-lg font-semibold transition">View Reports</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import { ref } from 'vue'

const lineChartOptions = ref({
  xaxis: {
    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  },
    chart: {
    width: '100%'
  },

  chart: { id: 'spending-line', toolbar: { show: false } },
})


// const lineChartOptions = ref({
//   chart: { id: 'spending-line', toolbar: { show: false } },
//   xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] },
//   stroke: { curve: 'smooth', width: 3 },
//   colors: ['#2563eb'],
//   dataLabels: { enabled: false },
//   grid: { show: true },
//   yaxis: { labels: { style: { fontSize: '14px' } } },
//   xaxis: { labels: { style: { fontSize: '14px' } } }
// })
const lineChartSeries = ref([
  { name: 'Spent', data: [320, 410, 380, 290, 500, 450, 370] }
])
</script>

<style scoped>
</style>
