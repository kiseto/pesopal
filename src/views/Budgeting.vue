<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex flex-col">
            <h1 class="text-4xl font-bold text-gray-900">Budget Planner</h1>
            <p class="text-lg text-gray-500">Organize your income and expenses at a glance</p>
          </div>
          
          <!-- Buttons -->
          <div class="flex gap-2">
            
            <button @click="openManageBudgetModal" class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700 transition">
              Manage Budget
            </button>
            <button @click="openManageGoalsModal" class="bg-purple-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-purple-700 transition">
              Manage Savings Goals
            </button>
          </div>
        </div>
      </div>
    
    
      <!-- Summary Cards -->
      <div v-if="isLoading" class="flex items-center justify-center h-64">
        <div class="text-gray-500 text-lg">Loading budget data...</div>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
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
      <div v-if="!isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Budget Categories -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="flex items-center justify-between mb-4">
            <div class="text-xl font-bold text-neutral-800">Budget Categories</div>
            <button @click="openCategoryModal" class="cursor-pointer bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Category</button>
          </div>
          <div v-if="categories.length === 0" class="text-center py-8 text-gray-500">
            No budget categories yet. Add your first category to get started!
          </div>
          <div v-for="cat in categories" :key="cat.id" class="mb-4">
            <div class="flex items-center gap-2 mb-1">
              <span :class="cat.icon_class || cat.icon" class="h-6 w-6"></span>
              <span class="text-gray-700 font-semibold">{{ cat.title }}</span>
              <span v-if="(parseFloat(cat.spent_amount || cat.spent || 0) / parseFloat(cat.allocated_amount || cat.allocated || 1)) > 0.9" class="ml-2 text-xs text-red-600 font-bold">Alert: Approaching limit!</span>
            </div>
            <div class="text-gray-500">₱{{ parseFloat(cat.spent_amount || cat.spent || 0).toLocaleString() }} / ₱{{ parseFloat(cat.allocated_amount || cat.allocated || 0).toLocaleString() }}</div>
            <div class="w-full h-2 bg-gray-200 rounded mt-1">
              <div class="h-2 rounded" :class="cat.color_class || cat.color" :style="{ width: Math.min(100, (parseFloat(cat.spent_amount || cat.spent || 0) / parseFloat(cat.allocated_amount || cat.allocated || 1) * 100)) + '%' }"></div>
            </div>
          </div>
        </div>
        <!-- Savings Goals -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="flex items-center justify-between mb-4">
            <div class="text-xl font-bold text-neutral-800">Savings Goals</div>
            <button @click="openGoalModal" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New Goal</button>
          </div>
          <div v-if="savingsGoals.length === 0" class="text-center py-8 text-gray-500">
            No savings goals yet. Create your first goal to start saving!
          </div>
          <div v-for="goal in savingsGoals" :key="goal.id" class="mb-4">
            <div class="flex items-center gap-2 mb-1">
              <span :class="goal.icon_class || goal.icon" class="h-6 w-6"></span>
              <span class="font-semibold">{{ goal.title }}</span>
              <span class="ml-2 text-xs text-gray-500">Target: {{ goal.target_date || goal.targetDate }}</span>
            </div>
            <div class="text-gray-500">₱{{ parseFloat(goal.saved_amount || goal.saved || 0).toLocaleString() }} of ₱{{ parseFloat(goal.target_amount || goal.target || 0).toLocaleString() }}</div>
            <div class="w-full h-2 bg-gray-200 rounded mt-1">
              <div class="h-2 rounded" :style="{ background: goal.color, width: Math.min(100, (parseFloat(goal.saved_amount || goal.saved || 0) / parseFloat(goal.target_amount || goal.target || 1) * 100)) + '%' }"></div>
            </div>
            <div class="text-sm text-gray-500 mt-1">{{ Math.round(parseFloat(goal.saved_amount || goal.saved || 0) / parseFloat(goal.target_amount || goal.target || 1) * 100) }}% complete</div>
          </div>
        </div>
      </div>
      
      <!-- Monthly Budget Progress Chart (ApexCharts Bar Graph) -->
      <div v-if="!isLoading" class="bg-white rounded-2xl border-neutral-200 border-1 p-8 mb-12">
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
              <select v-model="categoryForm.timeFrame" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="Monthly">Monthly</option>
                <option value="Weekly">Weekly</option>
                <option value="Yearly">Yearly</option>
              </select>
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
      
      <!-- Manage Budget Categories Modal -->
      <div v-if="showManageBudgetModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-6xl p-8 relative max-h-[90vh] overflow-y-auto">
          <button @click="closeManageBudgetModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          
          <!-- List Mode -->
          <div v-if="budgetModalMode === 'list'">
            <h2 class="text-xl font-bold mb-4">Manage Budget Categories</h2>
            <div v-if="categories.length === 0" class="text-center py-8 text-gray-500">
              No budget categories found. Add your first category to get started!
            </div>
            <vue-good-table
              v-else
              :columns="budgetTableColumns"
              :rows="budgetTableRows"
              :pagination-options="{ enabled: true, perPage: 10 }"
              :search-options="{ enabled: true }"
            >
              <template #table-row="props">
                <span v-if="props.column.field == 'actions'">
                  <button @click="viewBudgetCategory(props.row)" class="cursor-pointer text-blue-600 hover:text-blue-800 mr-2">View</button>
                  <button @click="editBudgetCategory(props.row)" class="cursor-pointer text-green-600 hover:text-green-800 mr-2">Edit</button>
                  <button @click="deleteBudgetCategory(props.row)" class="cursor-pointer text-red-600 hover:text-red-800">Delete</button>
                </span>
                <span v-else>
                  {{ props.formattedRow[props.column.field] }}
                </span>
              </template>
            </vue-good-table>
          </div>
          
          <!-- View Mode -->
          <div v-else-if="budgetModalMode === 'view'">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold">Budget Category Details</h2>
              <button @click="budgetModalMode = 'list'" class="cursor-pointer text-blue-600 hover:text-blue-800">← Back to List</button>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <p class="text-lg font-semibold">{{ selectedBudgetCategory.title }}</p>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Allocated Amount</label>
                  <p class="text-lg">₱{{ selectedBudgetCategory.allocated_amount.toLocaleString() }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Spent Amount</label>
                  <p class="text-lg">₱{{ selectedBudgetCategory.spent_amount.toLocaleString() }}</p>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Time Frame</label>
                  <p class="text-lg">{{ selectedBudgetCategory.time_frame }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Progress</label>
                  <p class="text-lg font-semibold">{{ selectedBudgetCategory.progress }}</p>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <p class="text-gray-600">{{ selectedBudgetCategory.notes }}</p>
              </div>
              <div class="flex gap-2 pt-4">
                <button @click="editBudgetCategory(selectedBudgetCategory)" class="cursor-pointer bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Edit Category</button>
                <button @click="deleteBudgetCategory(selectedBudgetCategory)" class="cursor-pointer bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete Category</button>
              </div>
            </div>
          </div>
          
          <!-- Edit Mode -->
          <div v-else-if="budgetModalMode === 'edit'">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold">Edit Budget Category</h2>
              <button @click="cancelBudgetEdit" class="cursor-pointer text-blue-600 hover:text-blue-800">← Back to List</button>
            </div>
            <form @submit.prevent="saveBudgetCategory" class="space-y-4">
              <div>
                <label class="block text-gray-700 mb-2">Title</label>
                <input type="text" v-model="editBudgetForm.title" required class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Allocated Amount</label>
                <input type="number" v-model.number="editBudgetForm.allocated_amount" required min="0" class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Time Frame</label>
                <select v-model="editBudgetForm.time_frame" required class="border border-gray-300 rounded px-3 py-2 w-full">
                  <option value="Monthly">Monthly</option>
                  <option value="Weekly">Weekly</option>
                  <option value="Yearly">Yearly</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Notes</label>
                <textarea v-model="editBudgetForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="3"></textarea>
              </div>
              <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="cancelBudgetEdit" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Manage Savings Goals Modal -->
      <div v-if="showManageGoalsModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-5xl p-8 relative max-h-[90vh] overflow-y-auto">
          <button @click="closeManageGoalsModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          
          <!-- List Mode -->
          <div v-if="goalsModalMode === 'list'">
            <h2 class="text-xl font-bold mb-4">Manage Savings Goals</h2>
            <div v-if="savingsGoals.length === 0" class="text-center py-8 text-gray-500">
              No savings goals found. Create your first goal to start saving!
            </div>
            <vue-good-table
              v-else
              :columns="goalsTableColumns"
              :rows="goalsTableRows"
              :pagination-options="{ enabled: true, perPage: 10 }"
              :search-options="{ enabled: true }"
            >
              <template #table-row="props">
                <span v-if="props.column.field == 'actions'">
                  <button @click="viewSavingsGoal(props.row)" class="cursor-pointer text-blue-600 hover:text-blue-800 mr-2">View</button>
                  <button @click="editSavingsGoal(props.row)" class="cursor-pointer text-green-600 hover:text-green-800 mr-2">Edit</button>
                  <button @click="deleteSavingsGoal(props.row)" class="cursor-pointer text-red-600 hover:text-red-800">Delete</button>
                </span>
                <span v-else>
                  {{ props.formattedRow[props.column.field] }}
                </span>
              </template>
            </vue-good-table>
          </div>
          
          <!-- View Mode -->
          <div v-else-if="goalsModalMode === 'view'">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold">Savings Goal Details</h2>
              <button @click="goalsModalMode = 'list'" class="cursor-pointer text-blue-600 hover:text-blue-800">← Back to List</button>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <p class="text-lg font-semibold">{{ selectedSavingsGoal.title }}</p>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Target Amount</label>
                  <p class="text-lg">₱{{ selectedSavingsGoal.target_amount.toLocaleString() }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Saved Amount</label>
                  <p class="text-lg">₱{{ selectedSavingsGoal.saved_amount.toLocaleString() }}</p>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Target Date</label>
                  <p class="text-lg">{{ selectedSavingsGoal.target_date }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Progress</label>
                  <p class="text-lg font-semibold">{{ selectedSavingsGoal.progress }}</p>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <p class="text-gray-600">{{ selectedSavingsGoal.notes }}</p>
              </div>
              <div class="flex gap-2 pt-4">
                <button @click="editSavingsGoal(selectedSavingsGoal)" class="cursor-pointer bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Edit Goal</button>
                <button @click="deleteSavingsGoal(selectedSavingsGoal)" class="cursor-pointer bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete Goal</button>
              </div>
            </div>
          </div>
          
          <!-- Edit Mode -->
          <div v-else-if="goalsModalMode === 'edit'">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold">Edit Savings Goal</h2>
              <button @click="cancelGoalEdit" class="cursor-pointer text-blue-600 hover:text-blue-800">← Back to List</button>
            </div>
            <form @submit.prevent="saveSavingsGoal" class="space-y-4">
              <div>
                <label class="block text-gray-700 mb-2">Title</label>
                <input type="text" v-model="editGoalForm.title" required class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Target Amount</label>
                <input type="number" v-model.number="editGoalForm.target_amount" required min="0" class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Target Date</label>
                <input type="date" v-model="editGoalForm.target_date" required class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Notes</label>
                <textarea v-model="editGoalForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="3"></textarea>
              </div>
              <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="cancelGoalEdit" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated, onUnmounted } from 'vue'
import ApexChart from 'vue3-apexcharts'
import { VueGoodTable } from 'vue-good-table-next'
import ApiService from '../services/ApiService.js'

const summary = ref({
  monthlyBudget: 0,
  spentThisMonth: 0,
  progress: 0,
  savingsGoal: 0,
  savedSoFar: 0,
  savingsProgress: 0,
  remaining: 0,
  status: 'Loading...'
})

const categories = ref([])
const savingsGoals = ref([])
const months = ref([]) // Will be populated from API
const isLoading = ref(true)

const showCategoryModal = ref(false)
const categoryForm = ref({ title: '', allocated: '', timeFrame: 'Monthly', notes: '' })
function openCategoryModal() { showCategoryModal.value = true }
function closeCategoryModal() { showCategoryModal.value = false }
async function submitCategory() {
  try {
    const result = await ApiService.addBudgetCategory({
      title: categoryForm.value.title,
      allocated_amount: parseFloat(categoryForm.value.allocated),
      time_frame: categoryForm.value.timeFrame,
      notes: categoryForm.value.notes
    })
    
    if (result.success) {
      categoryForm.value = { title: '', allocated: '', timeFrame: 'Monthly', notes: '' }
      showCategoryModal.value = false
      await loadBudgetData()
    } else {
      console.error('Failed to add category:', result.message)
      alert('Failed to add category: ' + result.message)
    }
  } catch (error) {
    console.error('Error adding category:', error)
    alert('Error adding category: ' + error.message)
  }
}

const showGoalModal = ref(false)
const goalForm = ref({ title: '', target: '', targetDate: '', notes: '' })
function openGoalModal() { showGoalModal.value = true }
function closeGoalModal() { showGoalModal.value = false }
async function submitGoal() {
  try {
    const result = await ApiService.addSavingsGoal({
      title: goalForm.value.title,
      target_amount: parseFloat(goalForm.value.target),
      target_date: goalForm.value.targetDate,
      notes: goalForm.value.notes
    })
    
    if (result.success) {
      goalForm.value = { title: '', target: '', targetDate: '', notes: '' }
      showGoalModal.value = false
      await loadBudgetData()
    } else {
      console.error('Failed to add goal:', result.message)
      alert('Failed to add goal: ' + result.message)
    }
  } catch (error) {
    console.error('Error adding goal:', error)
    alert('Error adding goal: ' + error.message)
  }
}

// Manage Budget Categories Modal
const showManageBudgetModal = ref(false)
const budgetModalMode = ref('list') // 'list', 'view', 'edit'
const selectedBudgetCategory = ref(null)
const editBudgetForm = ref({ title: '', allocated_amount: 0, time_frame: 'Monthly', notes: '' })

function openManageBudgetModal() { 
  showManageBudgetModal.value = true 
  budgetModalMode.value = 'list'
  selectedBudgetCategory.value = null
}
function closeManageBudgetModal() { 
  showManageBudgetModal.value = false 
  budgetModalMode.value = 'list'
  selectedBudgetCategory.value = null
}

// Manage Savings Goals Modal
const showManageGoalsModal = ref(false)
const goalsModalMode = ref('list') // 'list', 'view', 'edit'
const selectedSavingsGoal = ref(null)
const editGoalForm = ref({ title: '', target_amount: 0, target_date: '', notes: '' })

function openManageGoalsModal() { 
  console.log('Opening Manage Goals Modal...')
  showManageGoalsModal.value = true 
  goalsModalMode.value = 'list'
  selectedSavingsGoal.value = null
  console.log('showManageGoalsModal is now:', showManageGoalsModal.value)
}
function closeManageGoalsModal() { 
  console.log('Closing Manage Goals Modal...')
  showManageGoalsModal.value = false 
  goalsModalMode.value = 'list'
  selectedSavingsGoal.value = null
}

// Budget Categories Table Configuration
const budgetTableColumns = ref([
  { label: 'Title', field: 'title' },
  { label: 'Allocated Amount (₱)', field: 'allocated_amount', type: 'number' },
  { label: 'Spent Amount (₱)', field: 'spent_amount', type: 'number' },
  { label: 'Time Frame', field: 'time_frame' },
  { label: 'Progress', field: 'progress' },
  { label: 'Notes', field: 'notes' },
  { label: 'Actions', field: 'actions' }
])

const budgetTableRows = computed(() => {
  return categories.value.map(cat => ({
    id: cat.id,
    title: cat.title,
    allocated_amount: parseFloat(cat.allocated_amount || cat.allocated || 0),
    spent_amount: parseFloat(cat.spent_amount || cat.spent || 0),
    time_frame: cat.time_frame || 'Monthly',
    progress: `${Math.round((parseFloat(cat.spent_amount || cat.spent || 0) / parseFloat(cat.allocated_amount || cat.allocated || 1)) * 100)}%`,
    notes: cat.notes || 'No notes',
    actions: ''
  }))
})

// Savings Goals Table Configuration
const goalsTableColumns = ref([
  { label: 'Title', field: 'title' },
  { label: 'Target Amount (₱)', field: 'target_amount', type: 'number' },
  { label: 'Saved Amount (₱)', field: 'saved_amount', type: 'number' },
  { label: 'Target Date', field: 'target_date' },
  { label: 'Progress', field: 'progress' },
  { label: 'Notes', field: 'notes' },
  { label: 'Actions', field: 'actions' }
])

const goalsTableRows = computed(() => {
  return savingsGoals.value.map(goal => {
    // Format the target date properly
    let formattedDate = 'No date set'
    if (goal.target_date || goal.targetDate) {
      try {
        const dateValue = goal.target_date || goal.targetDate
        const date = new Date(dateValue)
        if (!isNaN(date.getTime())) {
          formattedDate = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
          })
        }
      } catch (error) {
        console.warn('Invalid date format:', goal.target_date || goal.targetDate)
        formattedDate = 'Invalid date'
      }
    }
    
    return {
      id: goal.id,
      title: goal.title,
      target_amount: parseFloat(goal.target_amount || goal.target || 0),
      saved_amount: parseFloat(goal.saved_amount || goal.saved || 0),
      target_date: formattedDate,
      progress: `${Math.round((parseFloat(goal.saved_amount || goal.saved || 0) / parseFloat(goal.target_amount || goal.target || 1)) * 100)}%`,
      notes: goal.notes || 'No notes',
      actions: ''
    }
  })
})

// Budget Category Actions
function viewBudgetCategory(category) {
  selectedBudgetCategory.value = category
  budgetModalMode.value = 'view'
}

function editBudgetCategory(category) {
  selectedBudgetCategory.value = category
  editBudgetForm.value = {
    title: category.title,
    allocated_amount: category.allocated_amount,
    time_frame: category.time_frame,
    notes: category.notes
  }
  budgetModalMode.value = 'edit'
}

async function saveBudgetCategory() {
  try {
    const result = await ApiService.updateBudgetCategory(selectedBudgetCategory.value.id, {
      title: editBudgetForm.value.title,
      allocated_amount: editBudgetForm.value.allocated_amount,
      time_frame: editBudgetForm.value.time_frame,
      notes: editBudgetForm.value.notes
    })
    
    if (result.success) {
      alert(`Budget Category "${editBudgetForm.value.title}" updated successfully!`)
      budgetModalMode.value = 'list'
      selectedBudgetCategory.value = null
      await loadBudgetData()
    } else {
      console.error('Failed to update budget category:', result.message)
      alert('Failed to update budget category: ' + result.message)
    }
  } catch (error) {
    console.error('Error updating budget category:', error)
    alert('Error updating budget category: ' + error.message)
  }
}

function cancelBudgetEdit() {
  budgetModalMode.value = 'list'
  selectedBudgetCategory.value = null
}

async function deleteBudgetCategory(category) {
  if (confirm(`Are you sure you want to delete the budget category "${category.title}"?`)) {
    try {
      const result = await ApiService.deleteBudgetCategory(category.id)
      
      if (result.success) {
        alert(`Budget Category "${category.title}" deleted successfully!`)
        // If we're in view mode and deleted the current item, go back to list
        if (budgetModalMode.value === 'view' && selectedBudgetCategory.value?.id === category.id) {
          budgetModalMode.value = 'list'
          selectedBudgetCategory.value = null
        }
        await loadBudgetData()
      } else {
        console.error('Failed to delete budget category:', result.message)
        alert('Failed to delete budget category: ' + result.message)
      }
    } catch (error) {
      console.error('Error deleting budget category:', error)
      alert('Error deleting budget category: ' + error.message)
    }
  }
}

// Savings Goal Actions
function viewSavingsGoal(goal) {
  selectedSavingsGoal.value = goal
  goalsModalMode.value = 'view'
}

function editSavingsGoal(goal) {
  selectedSavingsGoal.value = goal
  editGoalForm.value = {
    title: goal.title,
    target_amount: goal.target_amount,
    target_date: goal.target_date,
    notes: goal.notes
  }
  goalsModalMode.value = 'edit'
}

async function saveSavingsGoal() {
  try {
    const result = await ApiService.updateSavingsGoal(selectedSavingsGoal.value.id, {
      title: editGoalForm.value.title,
      target_amount: editGoalForm.value.target_amount,
      target_date: editGoalForm.value.target_date,
      notes: editGoalForm.value.notes
    })
    
    if (result.success) {
      alert(`Savings Goal "${editGoalForm.value.title}" updated successfully!`)
      goalsModalMode.value = 'list'
      selectedSavingsGoal.value = null
      await loadBudgetData()
    } else {
      console.error('Failed to update savings goal:', result.message)
      alert('Failed to update savings goal: ' + result.message)
    }
  } catch (error) {
    console.error('Error updating savings goal:', error)
    alert('Error updating savings goal: ' + error.message)
  }
}

function cancelGoalEdit() {
  goalsModalMode.value = 'list'
  selectedSavingsGoal.value = null
}

async function deleteSavingsGoal(goal) {
  if (confirm(`Are you sure you want to delete the savings goal "${goal.title}"?`)) {
    try {
      const result = await ApiService.deleteSavingsGoal(goal.id)
      
      if (result.success) {
        alert(`Savings Goal "${goal.title}" deleted successfully!`)
        // If we're in view mode and deleted the current item, go back to list
        if (goalsModalMode.value === 'view' && selectedSavingsGoal.value?.id === goal.id) {
          goalsModalMode.value = 'list'
          selectedSavingsGoal.value = null
        }
        await loadBudgetData()
      } else {
        console.error('Failed to delete savings goal:', result.message)
        alert('Failed to delete savings goal: ' + result.message)
      }
    } catch (error) {
      console.error('Error deleting savings goal:', error)
      alert('Error deleting savings goal: ' + error.message)
    }
  }
}

// Load budget data from API
async function loadBudgetData() {
  try {
    isLoading.value = true
    console.log('🔄 Loading budget data...')
    
    // Load budget summary (includes monthly data)
    const summaryResult = await ApiService.getBudgetSummary()
    console.log('📈 Budget summary result:', summaryResult)
    
    // Load budget categories
    const categoriesResult = await ApiService.getBudgetCategories()
    console.log('📊 Budget categories result:', categoriesResult)
    if (categoriesResult.success) {
      categories.value = categoriesResult.data
      console.log('✅ Loaded categories:', categories.value)
      // Debug each category
      categories.value.forEach((cat, index) => {
        console.log(`Category ${index}:`, cat)
        console.log(`- allocated_amount: ${cat.allocated_amount}`)
        console.log(`- allocated: ${cat.allocated}`)
        console.log(`- spent_amount: ${cat.spent_amount}`)
        console.log(`- spent: ${cat.spent}`)
      })
    } else {
      console.error('❌ Failed to load categories:', categoriesResult.message)
    }
    
    // Load savings goals
    const goalsResult = await ApiService.getSavingsGoals()
    console.log('💰 Savings goals result:', goalsResult)
    if (goalsResult.success) {
      savingsGoals.value = goalsResult.data
      console.log('✅ Loaded savings goals:', savingsGoals.value)
    } else {
      console.error('❌ Failed to load savings goals:', goalsResult.message)
    }
    
    // Set monthly data from summary or create default data
    if (summaryResult.success && summaryResult.data.monthly) {
      months.value = summaryResult.data.monthly
      console.log('✅ Loaded monthly data from API:', months.value)
    } else {
      // Create current month + 5 future months data (Option 1: Forward-looking)
      const currentDate = new Date()
      const totalAllocated = categories.value.reduce((sum, cat) => sum + parseFloat(cat.allocated_amount || cat.allocated || 0), 0)
      
      // Calculate actual total spent from categories (this is real data)
      const totalSpent = categories.value.reduce((sum, cat) => sum + parseFloat(cat.spent_amount || cat.spent || 0), 0)
      
      const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      months.value = []
      
      for (let i = 0; i < 6; i++) {
        const monthDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + i, 1)
        const monthName = monthNames[monthDate.getMonth()]
        const year = monthDate.getFullYear()
        const displayName = currentDate.getFullYear() === year ? monthName : `${monthName} ${year}`
        
        // Only current month shows actual spending, future months show 0 spent
        const spentAmount = i === 0 ? totalSpent : 0
        
        months.value.push({
          name: displayName,
          budget: totalAllocated,  // Budget allocation for all months
          spent: spentAmount       // Only current month has actual spending
        })
      }
      
      console.log('📊 Created realistic monthly data with actual spending only:', months.value)
    }
    
    // Calculate summary data
    const totalAllocated = categories.value.reduce((sum, cat) => {
      const allocated = parseFloat(cat.allocated_amount || cat.allocated || 0)
      console.log(`Category ${cat.title}: allocated = ${allocated}`)
      return sum + allocated
    }, 0)
    const totalSpent = categories.value.reduce((sum, cat) => {
      const spent = parseFloat(cat.spent_amount || cat.spent || 0)
      console.log(`Category ${cat.title}: spent = ${spent}`)
      return sum + spent
    }, 0)
    const totalSavingsTarget = savingsGoals.value.reduce((sum, goal) => {
      const target = parseFloat(goal.target_amount || goal.target || 0)
      return sum + target
    }, 0)
    const totalSaved = savingsGoals.value.reduce((sum, goal) => {
      const saved = parseFloat(goal.saved_amount || goal.saved || 0)
      return sum + saved
    }, 0)
    
    console.log('📈 Summary calculations:')
    console.log('- Categories count:', categories.value.length)
    console.log('- Total Allocated:', totalAllocated)
    console.log('- Total Spent:', totalSpent)
    console.log('- Savings Goals count:', savingsGoals.value.length)
    console.log('- Total Savings Target:', totalSavingsTarget)
    console.log('- Total Saved:', totalSaved)
    
    summary.value = {
      monthlyBudget: totalAllocated,
      spentThisMonth: totalSpent,
      progress: totalAllocated > 0 ? Math.round((totalSpent / totalAllocated) * 100) : 0,
      savingsGoal: totalSavingsTarget,
      savedSoFar: totalSaved,
      savingsProgress: totalSavingsTarget > 0 ? Math.round((totalSaved / totalSavingsTarget) * 100) : 0,
      remaining: totalAllocated - totalSpent,
      status: totalAllocated - totalSpent >= 0 ? 'On Track' : 'Over Budget'
    }
    
    console.log('📊 Final summary:', summary.value)
    
  } catch (error) {
    console.error('Error loading budget data:', error)
  } finally {
    isLoading.value = false
  }
}

// Load data on component mount
onMounted(() => {
  loadBudgetData()
})

// Reload data when component becomes active (when navigating to this page)
onActivated(() => {
  console.log('🔄 Budgeting page activated - refreshing data...')
  loadBudgetData()
})

// Add window focus listener to refresh data when user returns to the app
let focusHandler = null

onMounted(() => {
  focusHandler = () => {
    console.log('🔄 Window focused - refreshing budget data...')
    loadBudgetData()
  }
  window.addEventListener('focus', focusHandler)
})

onUnmounted(() => {
  if (focusHandler) {
    window.removeEventListener('focus', focusHandler)
  }
})

// ApexCharts Bar Chart for Monthly Budget Progress - computed from API data
const barChartOptions = computed(() => ({
  xaxis: {
    categories: months.value.map(m => m.name)
  },
  chart: {
    width: '100%'
  },
  colors: ['#2563eb', '#dc2626'],
  legend: {
    position: 'top'
  }
}))

const barChartSeries = computed(() => [
  { name: 'Budget', data: months.value.map(m => m.budget) },
  { name: 'Spent', data: months.value.map(m => m.spent) }
])
</script>

<style scoped>
</style>
