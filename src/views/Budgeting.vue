<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8">
          <div class="flex flex-col">
            <h1 class="text-4xl font-bold text-gray-900">Budget Planner</h1>
            <p class="text-lg text-gray-500">Organize your income and expenses at a glance</p>
          </div>
      </div>
    
    
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-1 border-gray-200 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Monthly Budget</div>
          <div class="text-3xl font-bold text-gray-900">₱{{ summary.monthlyBudget.toLocaleString() }}</div>
          <div class="text-md text-gray-500">₱{{ summary.spentThisMonth.toLocaleString() }} spent this month</div>
          <div class="w-full h-3 bg-gray-200 rounded mt-4">
            <div class="h-3 bg-blue-600 rounded" :style="{ width: summary.progress + '%' }"></div>
          </div>
        </div>
        <div class="bg-white rounded-2xl border-1 border-gray-200 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Savings Goal</div>
          <div class="text-3xl font-bold text-gray-900">₱{{ summary.savingsGoal.toLocaleString() }}</div>
          <div class="text-md text-gray-500">₱{{ summary.savedSoFar.toLocaleString() }} saved so far</div>
          <div class="w-full h-3 bg-gray-200 rounded mt-4">
            <div class="h-3 bg-blue-500 rounded" :style="{ width: summary.savingsProgress + '%' }"></div>
          </div>
        </div>
        <div class="bg-white rounded-2xl border-1 border-gray-200 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Budget Status</div>
          <div :class="summary.remaining >= 0 ? 'text-green-600' : 'text-red-600'" class="text-3xl font-bold">{{ summary.status }}</div>
          <div class="text-md text-gray-500">₱{{ summary.remaining.toLocaleString() }} remaining</div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Budget Categories -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="flex items-center justify-between mb-4">
            <div class="text-xl font-bold text-neutral-800">Budget Categories</div>
            <button @click="openCategoryModal" class="cursor-pointer bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Category</button>
          </div>
          <div v-for="cat in categories" :key="cat.id" class="mb-4">
            <div class="flex items-center gap-2 mb-1">
              <span :class="cat.icon" class="h-6 w-6"></span>
              <span class="text-gray-700 font-semibold">{{ cat.title }}</span>
              <span v-if="cat.spent / cat.allocated > 0.9" class="ml-2 text-xs text-red-600 font-bold">Alert: Approaching limit!</span>
            </div>
            <div class="  text-gray-500">₱{{ cat.spent.toLocaleString() }} / ₱{{ cat.allocated.toLocaleString() }}</div>
            <div class="w-full h-2 bg-gray-200 rounded mt-1">
              <div class="h-2 rounded" :class="cat.color" :style="{ width: (cat.spent / cat.allocated * 100) + '%' }"></div>
            </div>
          </div>
        </div>
        <!-- Savings Goals -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="flex items-center justify-between mb-4">
            <div class="text-xl font-bold text-neutral-800">Savings Goals</div>
            <button @click="openGoalModal" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New Goal</button>
          </div>
          <div v-for="goal in savingsGoals" :key="goal.id" class="mb-4">
            <div class="flex items-center gap-2 mb-1">
              <span :class="goal.icon" class="h-6 w-6"></span>
              <span class="font-semibold">{{ goal.title }}</span>
              <span class="ml-2 text-xs text-gray-500">Target: {{ goal.targetDate }}</span>
            </div>
            <div class="text-gray-500">₱{{ goal.saved.toLocaleString() }} of ₱{{ goal.target.toLocaleString() }}</div>
            <div class="w-full h-2 bg-gray-200 rounded mt-1">
              <div class="h-2 rounded" :style="{ background: goal.color, width: (goal.saved / goal.target * 100) + '%' }"></div>
            </div>
            <div class="text-sm text-gray-500 mt-1">{{ Math.round(goal.saved / goal.target * 100) }}% complete</div>
          </div>
        </div>
      </div>
      
      
      <!-- Monthly Budget Progress Chart (ApexCharts Bar Graph) -->
      <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 mb-12">
        <div class="text-xl font-bold text-neutral-800 mb-4">Monthly Budget Progress</div>
        <div class="w-full h-[30rem]">
          <ApexChart type="bar" width="100%" height="100%" :options="barChartOptions" :series="barChartSeries" />
        </div>
      </div>
      
      
      <!-- Add Category Modal -->
      <div v-if="showCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeCategoryModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Budget Category</h2>
          <form @submit.prevent="submitCategory">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Title</label>
              <input type="text" v-model="categoryForm.title" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Allocated Amount</label>
              <input type="number" v-model.number="categoryForm.allocated" required min="0" class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Time Frame</label>
              <input type="text" v-model="categoryForm.timeFrame" required class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="e.g. Monthly" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Notes</label>
              <textarea v-model="categoryForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="2"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeCategoryModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Save</button>
            </div>
          </form>
        </div>
      </div>
      <!-- Add Savings Goal Modal -->
      <div v-if="showGoalModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeGoalModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Savings Goal</h2>
          <form @submit.prevent="submitGoal">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Title</label>
              <input type="text" v-model="goalForm.title" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Target Amount</label>
              <input type="number" v-model.number="goalForm.target" required min="0" class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Target Date</label>
              <input type="date" v-model="goalForm.targetDate" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Notes</label>
              <textarea v-model="goalForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="2"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeGoalModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ApexChart from 'vue3-apexcharts'

const summary = ref({
  monthlyBudget: 45000,
  spentThisMonth: 32450,
  progress: 72,
  savingsGoal: 120000,
  savedSoFar: 78500,
  savingsProgress: 65,
  remaining: 12550,
  status: 'On Track'
})

const categories = ref([
  { id: 1, title: 'Food & Dining', allocated: 12000, spent: 9500, icon: 'bg-red-100 text-red-600 rounded-full p-1', color: 'bg-red-500', timeFrame: 'Monthly', notes: '' },
  { id: 2, title: 'Transportation', allocated: 8000, spent: 4200, icon: 'bg-blue-100 text-blue-600 rounded-full p-1', color: 'bg-blue-500', timeFrame: 'Monthly', notes: '' },
  { id: 3, title: 'Entertainment', allocated: 9000, spent: 7200, icon: 'bg-purple-100 text-purple-600 rounded-full p-1', color: 'bg-purple-500', timeFrame: 'Monthly', notes: '' },
  { id: 4, title: 'Shopping', allocated: 9000, spent: 3200, icon: 'bg-green-100 text-green-600 rounded-full p-1', color: 'bg-green-500', timeFrame: 'Monthly', notes: '' },
])

const savingsGoals = ref([
  { id: 1, title: 'Emergency Fund', target: 120000, saved: 78500, targetDate: '2024-12-31', icon: 'bg-blue-100 text-blue-600 rounded-full p-1', color: '#2563eb', notes: '' },
  { id: 2, title: 'Vacation Fund', target: 50000, saved: 32000, targetDate: '2024-06-30', icon: 'bg-green-100 text-green-600 rounded-full p-1', color: '#22c55e', notes: '' },
  { id: 3, title: 'New Laptop', target: 80000, saved: 45000, targetDate: '2024-03-31', icon: 'bg-purple-100 text-purple-600 rounded-full p-1', color: '#a855f7', notes: '' },
])

const months = ref([
  { name: 'Jan', budget: 45000, spent: 32000 },
  { name: 'Feb', budget: 45000, spent: 31000 },
  { name: 'Mar', budget: 45000, spent: 33000 },
  { name: 'Apr', budget: 45000, spent: 32500 },
  { name: 'May', budget: 45000, spent: 29500 },
  { name: 'Jun', budget: 45000, spent: 27000 },
])

const showCategoryModal = ref(false)
const categoryForm = ref({ title: '', allocated: '', timeFrame: '', notes: '' })
function openCategoryModal() { showCategoryModal.value = true }
function closeCategoryModal() { showCategoryModal.value = false }
function submitCategory() {
  categories.value.push({
    id: Date.now(),
    title: categoryForm.value.title,
    allocated: categoryForm.value.allocated,
    spent: 0,
    icon: 'bg-gray-100 text-gray-600 rounded-full p-1',
    color: 'bg-gray-500',
    timeFrame: categoryForm.value.timeFrame,
    notes: categoryForm.value.notes
  })
  showCategoryModal.value = false
}

const showGoalModal = ref(false)
const goalForm = ref({ title: '', target: '', targetDate: '', notes: '' })
function openGoalModal() { showGoalModal.value = true }
function closeGoalModal() { showGoalModal.value = false }
function submitGoal() {
  savingsGoals.value.push({
    id: Date.now(),
    title: goalForm.value.title,
    target: goalForm.value.target,
    saved: 0,
    targetDate: goalForm.value.targetDate,
    icon: 'bg-gray-100 text-gray-600 rounded-full p-1',
    color: '#64748b',
    notes: goalForm.value.notes
  })
  showGoalModal.value = false
}

// ApexCharts Bar Chart for Monthly Budget Progress
const barChartOptions = ref({
  xaxis: {
    categories: months.value.map(m => m.name)
  },
    chart: {
      width: '100%'
  },
  
})
const barChartSeries = ref([
  { name: 'Budget', data: months.value.map(m => m.budget) },
  { name: 'Spent', data: months.value.map(m => m.spent) }
])
</script>

<style scoped>
</style>
