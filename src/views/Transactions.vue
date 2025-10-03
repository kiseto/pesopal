<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header & Add Buttons -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex flex-col">
          <h1 class="text-4xl font-bold text-gray-900">Transactions</h1>
          <p class="text-lg text-gray-500">Track, add, and manage all your income and expenses</p>
        </div>

        <!--Buttons-->
        <div class="flex gap-2">
          <button @click="openExpenseModal" class="bg-red-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-red-700 transition">
            Add Expense
          </button>

          <button @click="openIncomeModal" class="bg-green-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-green-700 transition">
            Add Income
          </button>
        </div>
      </div>
      
      <div>
        <div v-if="isLoading" class="bg-white rounded-2xl border-neutral-200 border-1 p-8 text-center">
          <div class="text-gray-500">Loading transactions...</div>
        </div>
        <vue-good-table
          v-else
          :columns="columns"
          :rows="rows"
          :pagination-options="{ enabled: true, perPage: 5 }"
          :search-options="{ enabled: true }"
        >
          <template #table-row="props">
            <span v-if="props.column.field == 'actions'" class="flex flex-row">
              <button @click="viewTransaction(props.row)" class="w-full cursor-pointer bg-blue-500 text-white px-2 py-2 rounded text-xs mr-1 hover:bg-blue-600">View</button>
              <button @click="editTransaction(props.row)" class="w-full cursor-pointer bg-yellow-500 text-white px-2 py-1 rounded text-xs mr-1 hover:bg-yellow-600">Edit</button>
              <button @click="deleteTransaction(props.row)" class="w-full cursor-pointer bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Delete</button>
            </span>
            <span v-else-if="props.column.field == 'amount'" :class="props.row.amount >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ props.formattedRow[props.column.field] }}
            </span>
            <span v-else>
              {{ props.formattedRow[props.column.field] }}
            </span>
          </template>
        </vue-good-table>
      </div> 
      
      <!-- Add Expense Modal -->
      <div v-if="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeExpenseModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Expense</h2>
          <form @submit.prevent="submitExpense">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Description</label>
              <input type="text" v-model="expenseForm.description" required class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="e.g. Grocery shopping" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Category</label>
              <select v-model="expenseForm.category" @change="onCategoryChange" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select category</option>
                <option value="Food">Food & Dining</option>
                <option value="Bills">Bills & Utilities</option>
                <option value="Transport">Transportation</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Health">Health & Medical</option>
                <option value="Shopping">Shopping</option>
                <option value="Budget/Savings">Budget/Savings</option>
                <option value="Other">Other</option>
              </select>
            </div>
            
            <!-- Budget/Savings Type Selection -->
            <div v-if="expenseForm.category === 'Budget/Savings'" class="mb-4">
              <label class="block text-gray-700 mb-2">Type</label>
              <select v-model="expenseForm.budgetType" @change="onBudgetTypeChange" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select type</option>
                <option value="budget">Budget Category</option>
                <option value="savings">Savings Goal</option>
              </select>
            </div>
            
            <!-- Budget Category Selection -->
            <div v-if="expenseForm.category === 'Budget/Savings' && expenseForm.budgetType === 'budget'" class="mb-4">
              <label class="block text-gray-700 mb-2">Budget Category</label>
              <select v-model="expenseForm.budgetCategoryId" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select budget category</option>
                <option v-for="category in budgetCategories" :key="category.id" :value="category.id">
                  {{ category.title }} - ₱{{ (parseFloat(category.allocated_amount || category.allocated || 0) - parseFloat(category.spent_amount || category.spent || 0)).toLocaleString() }} remaining
                </option>
              </select>
              <!-- Budget Status Display -->
              <div v-if="expenseForm.budgetCategoryId" class="mt-2 p-3 bg-gray-50 rounded border">
                <div v-for="category in budgetCategories" :key="category.id">
                  <div v-if="category.id == expenseForm.budgetCategoryId">
                    <div class="text-sm font-medium text-gray-700">{{ category.title }} Status:</div>
                    <div class="text-sm text-gray-600">
                      Allocated: ₱{{ parseFloat(category.allocated_amount || category.allocated || 0).toLocaleString() }}
                    </div>
                    <div class="text-sm text-gray-600">
                      Spent: ₱{{ parseFloat(category.spent_amount || category.spent || 0).toLocaleString() }}
                    </div>
                    <div class="text-sm font-medium" :class="parseFloat(category.allocated_amount || category.allocated || 0) - parseFloat(category.spent_amount || category.spent || 0) > 0 ? 'text-green-600' : 'text-red-600'">
                      Remaining: ₱{{ (parseFloat(category.allocated_amount || category.allocated || 0) - parseFloat(category.spent_amount || category.spent || 0)).toLocaleString() }}
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded mt-2">
                      <div class="h-2 rounded" :class="parseFloat(category.spent_amount || category.spent || 0) / parseFloat(category.allocated_amount || category.allocated || 1) > 0.9 ? 'bg-red-500' : parseFloat(category.spent_amount || category.spent || 0) / parseFloat(category.allocated_amount || category.allocated || 1) > 0.7 ? 'bg-yellow-500' : 'bg-green-500'" :style="{ width: Math.min(100, (parseFloat(category.spent_amount || category.spent || 0) / parseFloat(category.allocated_amount || category.allocated || 1) * 100)) + '%' }"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Savings Goal Selection -->
            <div v-if="expenseForm.category === 'Budget/Savings' && expenseForm.budgetType === 'savings'" class="mb-4">
              <label class="block text-gray-700 mb-2">Savings Goal</label>
              <select v-model="expenseForm.savingsGoalId" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select savings goal</option>
                <option v-for="goal in savingsGoals" :key="goal.id" :value="goal.id">
                  {{ goal.title }} - ₱{{ (parseFloat(goal.target_amount || goal.target || 0) - parseFloat(goal.saved_amount || goal.saved || 0)).toLocaleString() }} to goal
                </option>
              </select>
              <!-- Savings Status Display -->
              <div v-if="expenseForm.savingsGoalId" class="mt-2 p-3 bg-gray-50 rounded border">
                <div v-for="goal in savingsGoals" :key="goal.id">
                  <div v-if="goal.id == expenseForm.savingsGoalId">
                    <div class="text-sm font-medium text-gray-700">{{ goal.title }} Progress:</div>
                    <div class="text-sm text-gray-600">
                      Target: ₱{{ parseFloat(goal.target_amount || goal.target || 0).toLocaleString() }}
                    </div>
                    <div class="text-sm text-gray-600">
                      Saved: ₱{{ parseFloat(goal.saved_amount || goal.saved || 0).toLocaleString() }}
                    </div>
                    <div class="text-sm font-medium" :class="parseFloat(goal.target_amount || goal.target || 0) - parseFloat(goal.saved_amount || goal.saved || 0) > 0 ? 'text-blue-600' : 'text-green-600'">
                      Remaining: ₱{{ (parseFloat(goal.target_amount || goal.target || 0) - parseFloat(goal.saved_amount || goal.saved || 0)).toLocaleString() }}
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded mt-2">
                      <div class="h-2 bg-blue-500 rounded" :style="{ width: Math.min(100, (parseFloat(goal.saved_amount || goal.saved || 0) / parseFloat(goal.target_amount || goal.target || 1) * 100)) + '%' }"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ Math.round(parseFloat(goal.saved_amount || goal.saved || 0) / parseFloat(goal.target_amount || goal.target || 1) * 100) }}% complete</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Amount (₱)</label>
              <input type="number" v-model.number="expenseForm.amount" required min="0" step="0.01" class="border border-gray-300 rounded px-3 py-2 w-full" @input="validateTransactionAmount" />
              
              <!-- Real-time validation preview for Budget/Savings -->
              <div v-if="expenseForm.category === 'Budget/Savings' && expenseForm.amount > 0" class="mt-2">
                <!-- Budget Category Preview -->
                <div v-if="expenseForm.budgetType === 'budget' && expenseForm.budgetCategoryId" class="p-2 rounded text-sm">
                  <div v-for="category in budgetCategories" :key="category.id">
                    <div v-if="category.id == expenseForm.budgetCategoryId">
                      <div :class="getValidationPreviewClass('budget', category, expenseForm.amount)">
                        <strong>{{ getValidationPreviewMessage('budget', category, expenseForm.amount) }}</strong>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Savings Goal Preview -->
                <div v-if="expenseForm.budgetType === 'savings' && expenseForm.savingsGoalId" class="p-2 rounded text-sm">
                  <div v-for="goal in savingsGoals" :key="goal.id">
                    <div v-if="goal.id == expenseForm.savingsGoalId">
                      <div :class="getValidationPreviewClass('savings', goal, expenseForm.amount)">
                        <strong>{{ getValidationPreviewMessage('savings', goal, expenseForm.amount) }}</strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Date</label>
              <input type="date" v-model="expenseForm.date" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeExpenseModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button 
                type="submit" 
                :disabled="isExpenseInvalid" 
                :class="isExpenseInvalid ? 'cursor-not-allowed px-4 py-2 rounded bg-gray-400 text-gray-600' : 'cursor-pointer px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700'"
              >
                {{ isExpenseInvalid ? 'Invalid Amount' : 'Save Expense' }}
              </button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Add Income Modal -->
      <div v-if="showIncomeModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeIncomeModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Income</h2>
          <form @submit.prevent="submitIncome">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Description</label>
              <input type="text" v-model="incomeForm.description" required class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="e.g. Salary, Freelance work" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Source</label>
              <select v-model="incomeForm.category" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select source</option>
                <option value="Salary">Salary</option>
                <option value="Freelance">Freelance</option>
                <option value="Business">Business</option>
                <option value="Investment">Investment</option>
                <option value="Gift">Gift</option>
                <option value="Bonus">Bonus</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Amount (₱)</label>
              <input type="number" v-model.number="incomeForm.amount" required min="0" step="0.01" class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Date</label>
              <input type="date" v-model="incomeForm.date" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeIncomeModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Save Income</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Manage Transaction Modal -->
      <div v-if="showManageTransactionModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeManageTransactionModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          
          <!-- View Mode -->
          <div v-if="transactionModalMode === 'view'">
            <h2 class="text-xl font-bold mb-4">Transaction Details</h2>
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <p class="text-lg">{{ selectedTransaction.desc }}</p>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <p class="text-lg">{{ selectedTransaction.category }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                  <p class="text-lg font-semibold" :class="selectedTransaction.amount >= 0 ? 'text-green-600' : 'text-red-600'">
                    ₱{{ Math.abs(selectedTransaction.amount).toLocaleString() }}
                  </p>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                  <p class="text-lg capitalize">{{ selectedTransaction.amount >= 0 ? 'Income' : 'Expense' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                  <p class="text-lg">{{ selectedTransaction.date }}</p>
                </div>
              </div>
              <div class="flex gap-2 pt-4">
                <button @click="editTransaction(selectedTransaction)" class="cursor-pointer bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</button>
                <button @click="deleteTransaction(selectedTransaction)" class="bg-red-500 cursor-pointer text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
              </div>
            </div>
          </div>
          
          <!-- Edit Mode -->
          <div v-else-if="transactionModalMode === 'edit'">
            <h2 class="text-xl font-bold mb-4">Edit Transaction</h2>
            <form @submit.prevent="saveTransaction" class="space-y-4">
              <div>
                <label class="block text-gray-700 mb-2">Description</label>
                <input type="text" v-model="editTransactionForm.description" required class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Category</label>
                <select v-model="editTransactionForm.category" required class="border border-gray-300 rounded px-3 py-2 w-full">
                  <option value="Food">Food & Dining</option>
                  <option value="Bills">Bills & Utilities</option>
                  <option value="Transport">Transportation</option>
                  <option value="Entertainment">Entertainment</option>
                  <option value="Health">Health & Medical</option>
                  <option value="Shopping">Shopping</option>
                  <option value="Salary">Salary</option>
                  <option value="Freelance">Freelance</option>
                  <option value="Business">Business</option>
                  <option value="Investment">Investment</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Amount (₱)</label>
                <input type="number" v-model.number="editTransactionForm.amount" required step="0.01" class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Date</label>
                <input type="date" v-model="editTransactionForm.date" required class="border border-gray-300 rounded px-3 py-2 w-full" />
              </div>
              <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="cancelTransactionEdit" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, computed } from 'vue'
  import ApiService from '../services/ApiService.js'

  const columns = ref([
    { label: 'Date', field: 'date' },
    { label: 'Category', field: 'category' },
    { label: 'Description', field: 'desc' },
    { label: 'Amount (₱)', field: 'amount', type: 'number', sortable: true },
    { label: 'Actions', field: 'actions' }
  ])

  const rows = ref([])
  const isLoading = ref(true)
  
  // Budget Categories and Savings Goals for allocation
  const budgetCategories = ref([])
  const savingsGoals = ref([])

  // Load budget data for allocation options
  const loadBudgetData = async () => {
    try {
      // Load budget categories
      const categoriesResult = await ApiService.getBudgetCategories()
      if (categoriesResult.success) {
        budgetCategories.value = categoriesResult.data
        console.log('Loaded budget categories for allocation:', budgetCategories.value)
      }
      
      // Load savings goals
      const goalsResult = await ApiService.getSavingsGoals()
      if (goalsResult.success) {
        savingsGoals.value = goalsResult.data
        console.log('Loaded savings goals for allocation:', savingsGoals.value)
      }
    } catch (error) {
      console.error('Error loading budget data:', error)
    }
  }

  // Load transactions from API
  const loadTransactions = async () => {
    try {
      isLoading.value = true
      const result = await ApiService.getTransactions()
      
      console.log('Transactions API result:', result)
      
      if (result.success) {
        rows.value = result.data
        console.log('Loaded transactions:', result.data)
        
        // If no transactions found, create sample data
        if (result.data.length === 0) {
          console.log('No transactions found, creating sample data...')
          const sampleResult = await ApiService.createSampleTransactions()
          console.log('Sample creation result:', sampleResult)
          
          if (sampleResult.success) {
            // Reload transactions after creating samples
            const reloadResult = await ApiService.getTransactions()
            if (reloadResult.success) {
              rows.value = reloadResult.data
              console.log('Reloaded transactions after sample creation:', reloadResult.data)
            }
          }
        }
      } else {
        console.error('Failed to load transactions:', result.message)
        // Show fallback message to user
        alert('Failed to load transactions: ' + result.message)
      }
    } catch (error) {
      console.error('Error loading transactions:', error)
      alert('Error loading transactions: ' + error.message)
    } finally {
      isLoading.value = false
    }
  }

  // Expense Modal
  const showExpenseModal = ref(false)
  const expenseForm = ref({ 
    description: '', 
    category: '', 
    amount: '', 
    date: '',
    budgetType: '', // 'budget' or 'savings'
    budgetCategoryId: '',
    savingsGoalId: ''
  })
  
  // Handle category change
  function onCategoryChange() {
    // Reset budget/savings related fields when category changes
    if (expenseForm.value.category !== 'Budget/Savings') {
      expenseForm.value.budgetType = ''
      expenseForm.value.budgetCategoryId = ''
      expenseForm.value.savingsGoalId = ''
    }
  }
  
  // Handle budget type change
  function onBudgetTypeChange() {
    // Reset selection fields when budget type changes
    expenseForm.value.budgetCategoryId = ''
    expenseForm.value.savingsGoalId = ''
  }
  
  function openExpenseModal() { 
    showExpenseModal.value = true 
    // Set today's date as default
    expenseForm.value.date = new Date().toISOString().split('T')[0]
  }
  function closeExpenseModal() { 
    showExpenseModal.value = false
    expenseForm.value = { 
      description: '', 
      category: '', 
      amount: '', 
      date: '',
      budgetType: '',
      budgetCategoryId: '',
      savingsGoalId: ''
    }
  }
  async function submitExpense() {
    try {
      // Validation for Budget/Savings allocations
      if (expenseForm.value.category === 'Budget/Savings') {
        const amount = parseFloat(expenseForm.value.amount)
        
        if (expenseForm.value.budgetType === 'budget' && expenseForm.value.budgetCategoryId) {
          // Find the selected budget category
          const selectedCategory = budgetCategories.value.find(cat => cat.id == expenseForm.value.budgetCategoryId)
          if (selectedCategory) {
            const currentSpent = parseFloat(selectedCategory.spent_amount || selectedCategory.spent || 0)
            const allocated = parseFloat(selectedCategory.allocated_amount || selectedCategory.allocated || 0)
            const newTotal = currentSpent + amount
            
            if (newTotal > allocated) {
              const remaining = allocated - currentSpent
              alert(`⚠️ Budget Exceeded!\n\nCategory: ${selectedCategory.title}\nAllocated: ₱${allocated.toLocaleString()}\nCurrently Spent: ₱${currentSpent.toLocaleString()}\nRemaining: ₱${remaining.toLocaleString()}\nTrying to spend: ₱${amount.toLocaleString()}\n\nThis would exceed your budget by ₱${(newTotal - allocated).toLocaleString()}!`)
              return
            }
            
            // Warn if approaching limit (90% threshold)
            if (newTotal / allocated > 0.9) {
              const proceed = confirm(`⚠️ Budget Warning!\n\nYou're about to use ${Math.round((newTotal / allocated) * 100)}% of your "${selectedCategory.title}" budget.\n\nAllocated: ₱${allocated.toLocaleString()}\nAfter this expense: ₱${newTotal.toLocaleString()}\nRemaining: ₱${(allocated - newTotal).toLocaleString()}\n\nDo you want to continue?`)
              if (!proceed) return
            }
          }
        } else if (expenseForm.value.budgetType === 'savings' && expenseForm.value.savingsGoalId) {
          // Find the selected savings goal
          const selectedGoal = savingsGoals.value.find(goal => goal.id == expenseForm.value.savingsGoalId)
          if (selectedGoal) {
            const currentSaved = parseFloat(selectedGoal.saved_amount || selectedGoal.saved || 0)
            const target = parseFloat(selectedGoal.target_amount || selectedGoal.target || 0)
            const newTotal = currentSaved + amount
            
            if (newTotal > target) {
              const remaining = target - currentSaved
              alert(`🎯 Savings Goal Exceeded!\n\nGoal: ${selectedGoal.title}\nTarget: ₱${target.toLocaleString()}\nCurrently Saved: ₱${currentSaved.toLocaleString()}\nRemaining: ₱${remaining.toLocaleString()}\nTrying to save: ₱${amount.toLocaleString()}\n\nThis would exceed your goal by ₱${(newTotal - target).toLocaleString()}!\n\nConsider creating a new savings goal or adjusting your target amount.`)
              return
            }
            
            // Notify when goal is completed
            if (newTotal >= target) {
              alert(`🎉 Congratulations!\n\nYou've reached your savings goal "${selectedGoal.title}"!\n\nTarget: ₱${target.toLocaleString()}\nTotal Saved: ₱${newTotal.toLocaleString()}\n\nGreat job on achieving your financial goal!`)
            }
          }
        }
      }
      
      // Prepare transaction data
      const transactionData = {
        description: expenseForm.value.description,
        category: expenseForm.value.category,
        amount: expenseForm.value.amount,
        type: 'expense',
        date: expenseForm.value.date
      }
      
      // Add budget/savings allocation data if applicable
      if (expenseForm.value.category === 'Budget/Savings') {
        if (expenseForm.value.budgetType === 'budget' && expenseForm.value.budgetCategoryId) {
          transactionData.budget_category_id = expenseForm.value.budgetCategoryId
        } else if (expenseForm.value.budgetType === 'savings' && expenseForm.value.savingsGoalId) {
          transactionData.savings_goal_id = expenseForm.value.savingsGoalId
        }
      }
      
      const result = await ApiService.addTransaction(transactionData)
      
      if (result.success) {
        // If this was a budget/savings allocation, update the respective amounts
        if (expenseForm.value.category === 'Budget/Savings') {
          console.log('🔄 Processing Budget/Savings expense:', {
            budgetType: expenseForm.value.budgetType,
            budgetCategoryId: expenseForm.value.budgetCategoryId,
            savingsGoalId: expenseForm.value.savingsGoalId,
            amount: expenseForm.value.amount
          })
          
          if (expenseForm.value.budgetType === 'budget' && expenseForm.value.budgetCategoryId) {
            console.log('💰 Updating budget category spent amount...')
            // Update budget category spent amount
            const categoryResult = await ApiService.updateBudgetCategorySpent(
              expenseForm.value.budgetCategoryId,
              expenseForm.value.amount
            )
            console.log('📊 Budget category update result:', categoryResult)
            if (!categoryResult.success) {
              console.warn('❌ Failed to update budget category spent amount:', categoryResult.message)
            } else {
              console.log('✅ Budget category spent amount updated successfully!')
            }
          } else if (expenseForm.value.budgetType === 'savings' && expenseForm.value.savingsGoalId) {
            console.log('🎯 Updating savings goal saved amount...')
            // Update savings goal saved amount
            const goalResult = await ApiService.updateSavingsGoalSaved(
              expenseForm.value.savingsGoalId,
              expenseForm.value.amount
            )
            console.log('📈 Savings goal update result:', goalResult)
            if (!goalResult.success) {
              console.warn('❌ Failed to update savings goal saved amount:', goalResult.message)
            } else {
              console.log('✅ Savings goal saved amount updated successfully!')
            }
          }
        }
        
        // Add to local rows for immediate UI update
        rows.value.unshift({
          id: result.data.id,
          date: result.data.date,
          category: result.data.category,
          desc: result.data.description,
          amount: result.data.amount
        })
        closeExpenseModal()
        // Reload transactions to ensure accuracy
        loadTransactions()
        // Reload budget data to show updated amounts
        loadBudgetData()
      } else {
        console.error('Failed to add expense:', result.message)
        alert('Failed to add expense: ' + result.message)
      }
    } catch (error) {
      console.error('Error adding expense:', error)
      alert('Error adding expense')
    }
  }

  // Income Modal
  const showIncomeModal = ref(false)
  const incomeForm = ref({ description: '', category: '', amount: '', date: '' })
  function openIncomeModal() { 
    showIncomeModal.value = true 
    // Set today's date as default
    incomeForm.value.date = new Date().toISOString().split('T')[0]
  }
  function closeIncomeModal() { 
    showIncomeModal.value = false
    incomeForm.value = { description: '', category: '', amount: '', date: '' }
  }
  async function submitIncome() {
    try {
      const result = await ApiService.addTransaction({
        description: incomeForm.value.description,
        category: incomeForm.value.category,
        amount: incomeForm.value.amount,
        type: 'income',
        date: incomeForm.value.date
      })
      
      if (result.success) {
        // Add to local rows for immediate UI update
        rows.value.unshift({
          id: result.data.id,
          date: result.data.date,
          category: result.data.category,
          desc: result.data.description,
          amount: result.data.amount
        })
        closeIncomeModal()
        // Reload transactions to ensure accuracy
        loadTransactions()
      } else {
        console.error('Failed to add income:', result.message)
        alert('Failed to add income: ' + result.message)
      }
    } catch (error) {
      console.error('Error adding income:', error)
      alert('Error adding income')
    }
  }

  // Transaction Management
  const showManageTransactionModal = ref(false)
  const transactionModalMode = ref('view') // 'view' or 'edit'
  const selectedTransaction = ref(null)
  const editTransactionForm = ref({ description: '', category: '', amount: 0, date: '' })

  function closeManageTransactionModal() {
    showManageTransactionModal.value = false
    transactionModalMode.value = 'view'
    selectedTransaction.value = null
  }

  function viewTransaction(transaction) {
    selectedTransaction.value = transaction
    transactionModalMode.value = 'view'
    showManageTransactionModal.value = true
  }

  function editTransaction(transaction) {
    selectedTransaction.value = transaction
    editTransactionForm.value = {
      description: transaction.desc,
      category: transaction.category,
      amount: Math.abs(transaction.amount), // Always positive for editing
      date: transaction.date
    }
    transactionModalMode.value = 'edit'
    showManageTransactionModal.value = true
  }

  async function saveTransaction() {
    try {
      const result = await ApiService.updateTransaction(selectedTransaction.value.id, {
        description: editTransactionForm.value.description,
        category: editTransactionForm.value.category,
        amount: editTransactionForm.value.amount,
        date: editTransactionForm.value.date
      })
      
      if (result.success) {
        alert(`Transaction "${editTransactionForm.value.description}" updated successfully!`)
        closeManageTransactionModal()
        await loadTransactions()
        // Reload budget data in case amounts changed
        await loadBudgetData()
      } else {
        console.error('Failed to update transaction:', result.message)
        alert('Failed to update transaction: ' + result.message)
      }
    } catch (error) {
      console.error('Error updating transaction:', error)
      alert('Error updating transaction: ' + error.message)
    }
  }

  function cancelTransactionEdit() {
    transactionModalMode.value = 'view'
  }

  async function deleteTransaction(transaction) {
    if (confirm(`Are you sure you want to delete the transaction "${transaction.desc}"?`)) {
      try {
        const result = await ApiService.deleteTransaction(transaction.id)
        
        if (result.success) {
          alert(`Transaction "${transaction.desc}" deleted successfully!`)
          closeManageTransactionModal()
          await loadTransactions()
          // Reload budget data in case amounts changed
          await loadBudgetData()
        } else {
          console.error('Failed to delete transaction:', result.message)
          alert('Failed to delete transaction: ' + result.message)
        }
      } catch (error) {
        console.error('Error deleting transaction:', error)
        alert('Error deleting transaction: ' + error.message)
      }
    }
  }
  
  // Computed property to check if current expense would be invalid
  const isExpenseInvalid = computed(() => {
    if (expenseForm.value.category !== 'Budget/Savings' || !expenseForm.value.amount || expenseForm.value.amount <= 0) {
      return false
    }
    
    const amount = parseFloat(expenseForm.value.amount)
    
    if (expenseForm.value.budgetType === 'budget' && expenseForm.value.budgetCategoryId) {
      const selectedCategory = budgetCategories.value.find(cat => cat.id == expenseForm.value.budgetCategoryId)
      if (selectedCategory) {
        const currentSpent = parseFloat(selectedCategory.spent_amount || selectedCategory.spent || 0)
        const allocated = parseFloat(selectedCategory.allocated_amount || selectedCategory.allocated || 0)
        const newTotal = currentSpent + amount
        return newTotal > allocated
      }
    } else if (expenseForm.value.budgetType === 'savings' && expenseForm.value.savingsGoalId) {
      const selectedGoal = savingsGoals.value.find(goal => goal.id == expenseForm.value.savingsGoalId)
      if (selectedGoal) {
        const currentSaved = parseFloat(selectedGoal.saved_amount || selectedGoal.saved || 0)
        const target = parseFloat(selectedGoal.target_amount || selectedGoal.target || 0)
        const newTotal = currentSaved + amount
        return newTotal > target
      }
    }
    
    return false
  })

  // Real-time validation preview for transaction amounts
  function validateTransactionAmount() {
    // This function is called when user types in amount field
    // The actual validation preview is handled in the computed functions below
  }
  
  function getValidationPreviewClass(type, item, amount) {
    if (!amount || amount <= 0) return ''
    
    const amountNum = parseFloat(amount)
    
    if (type === 'budget') {
      const currentSpent = parseFloat(item.spent_amount || item.spent || 0)
      const allocated = parseFloat(item.allocated_amount || item.allocated || 0)
      const newTotal = currentSpent + amountNum
      
      if (newTotal > allocated) {
        return 'bg-red-100 border border-red-300 text-red-700'
      } else if (newTotal / allocated > 0.9) {
        return 'bg-yellow-100 border border-yellow-300 text-yellow-700'
      } else {
        return 'bg-green-100 border border-green-300 text-green-700'
      }
    } else if (type === 'savings') {
      const currentSaved = parseFloat(item.saved_amount || item.saved || 0)
      const target = parseFloat(item.target_amount || item.target || 0)
      const newTotal = currentSaved + amountNum
      
      if (newTotal > target) {
        return 'bg-red-100 border border-red-300 text-red-700'
      } else if (newTotal >= target) {
        return 'bg-green-100 border border-green-300 text-green-700'
      } else {
        return 'bg-blue-100 border border-blue-300 text-blue-700'
      }
    }
    
    return ''
  }
  
  function getValidationPreviewMessage(type, item, amount) {
    if (!amount || amount <= 0) return ''
    
    const amountNum = parseFloat(amount)
    
    if (type === 'budget') {
      const currentSpent = parseFloat(item.spent_amount || item.spent || 0)
      const allocated = parseFloat(item.allocated_amount || item.allocated || 0)
      const newTotal = currentSpent + amountNum
      
      if (newTotal > allocated) {
        const excess = newTotal - allocated
        return `❌ EXCEEDS BUDGET by ₱${excess.toLocaleString()}! (${newTotal.toLocaleString()} > ${allocated.toLocaleString()})`
      } else if (newTotal / allocated > 0.9) {
        const percentage = Math.round((newTotal / allocated) * 100)
        const remaining = allocated - newTotal
        return `⚠️ ${percentage}% of budget used - ₱${remaining.toLocaleString()} remaining`
      } else {
        const percentage = Math.round((newTotal / allocated) * 100)
        const remaining = allocated - newTotal
        return `✅ ${percentage}% of budget - ₱${remaining.toLocaleString()} remaining`
      }
    } else if (type === 'savings') {
      const currentSaved = parseFloat(item.saved_amount || item.saved || 0)
      const target = parseFloat(item.target_amount || item.target || 0)
      const newTotal = currentSaved + amountNum
      
      if (newTotal > target) {
        const excess = newTotal - target
        return `❌ EXCEEDS TARGET by ₱${excess.toLocaleString()}! Consider creating a new goal.`
      } else if (newTotal >= target) {
        return `🎉 GOAL ACHIEVED! You'll have saved ₱${newTotal.toLocaleString()} (target: ₱${target.toLocaleString()})`
      } else {
        const percentage = Math.round((newTotal / target) * 100)
        const remaining = target - newTotal
        return `💰 ${percentage}% of goal - ₱${remaining.toLocaleString()} to go`
      }
    }
    
    return ''
  }

  // Debug function - removed problematic debug calls
  const debugTransactions = async () => {
    // Only check current user session
    const profileResult = await ApiService.getUserProfile()
    console.log('Current user profile:', profileResult)
  }  // Load transactions on component mount
  onMounted(() => {
    debugTransactions()
    loadTransactions()
    loadBudgetData()
  })
</script>


<style scoped>
/* Custom modal animation, optional */
</style>
