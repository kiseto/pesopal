<template>
  <div class="max-w-7xl mx-auto p-8">
    <!-- Header & Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 mb-12">
      <div>
        <h1 class="text-4xl font-bold text-gray-900">Reports & Analytics</h1>
        <div class="text-lg text-gray-500">Analyze your spending patterns and financial trends</div>
      </div>
      <div class="flex gap-4 items-center">
        <select v-model="selectedRange" class="border rounded px-3 py-2 text-lg">
          <option value="30">Last 30 days</option>
          <option value="90">Last 90 days</option>
          <option value="365">Last year</option>
        </select>
        <button class="bg-green-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-green-700 transition">Export Report</button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Total Income</div>
        <div class="text-3xl font-bold text-green-600">₱{{ summary.income.toLocaleString() }}</div>
        <div class="text-base text-green-700 mt-2">+12% from last month</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Total Expenses</div>
        <div class="text-3xl font-bold text-red-600">₱{{ summary.expenses.toLocaleString() }}</div>
        <div class="text-base text-red-700 mt-2">+8% from last month</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Net Savings</div>
        <div class="text-3xl font-bold text-blue-600">₱{{ summary.savings.toLocaleString() }}</div>
        <div class="text-base text-blue-700 mt-2">+25% from last month</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Avg. Daily Spend</div>
        <div class="text-2xl font-bold text-gray-800">₱{{ summary.avgDaily.toLocaleString() }}</div>
        <div class="text-base text-gray-500 mt-2">Based on {{ selectedRange }} days</div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <div class="bg-white rounded-2xl shadow p-8 h-80 flex flex-col">
        <div class="text-2xl font-semibold text-gray-800 mb-4">Spending by Category</div>
        <div class="flex-1 flex items-center justify-center text-gray-400 text-lg">(Chart Placeholder)</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 h-80 flex flex-col">
        <div class="text-2xl font-semibold text-gray-800 mb-4">Monthly Spending Trends</div>
        <div class="flex-1 flex items-center justify-center text-gray-400 text-lg">(Chart Placeholder)</div>
      </div>
    </div>

    <!-- Breakdown & Export Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <!-- Category Breakdown -->
      <div class="bg-white rounded-2xl shadow p-8">
        <div class="text-2xl font-semibold text-gray-800 mb-6">Category Breakdown</div>
        <div v-for="cat in breakdown" :key="cat.title" class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <span :class="cat.color" class="h-5 w-5 rounded-full inline-block"></span>
            <span class="text-lg">{{ cat.title }}</span>
          </div>
          <div class="text-right">
            <span class="font-bold text-xl">₱{{ cat.amount.toLocaleString() }}</span>
            <span class="text-base text-gray-500 ml-2">{{ cat.percent }}%</span>
          </div>
        </div>
      </div>
      <!-- Export Reports -->
      <div class="bg-white rounded-2xl shadow p-8">
        <div class="text-2xl font-semibold text-gray-800 mb-6">Export Reports</div>
        <div class="flex flex-col gap-4 mb-6">
          <button class="bg-green-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-green-700 flex items-center gap-3"><span>Download PDF Report</span></button>
          <button class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 flex items-center gap-3"><span>Export to CSV</span></button>
          <button class="bg-gray-700 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-gray-800 flex items-center gap-3"><span>Export to Excel</span></button>
          <button class="bg-white border text-gray-700 px-6 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 flex items-center gap-3"><span>Email Report</span></button>
        </div>
        <div class="mt-6">
          <div class="text-2xl font-semibold text-gray-800 mb-4">Schedule Reports</div>
          <div class="text-lg text-gray-500 mb-4">Get automated reports delivered to your email</div>
          <div class="flex gap-3 items-center mb-2">
            <select v-model="schedule" class="border rounded px-3 py-2 text-lg">
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
              <option value="Quarterly">Quarterly</option>
            </select>
            <button class="bg-green-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-green-700">Set Up Schedule</button>
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
</script>

<style scoped>
</style>
