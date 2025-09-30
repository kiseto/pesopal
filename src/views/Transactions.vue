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
        <vue-good-table
          :columns="columns"
          :rows="rows"
          :pagination-options="{ enabled: true, perPage: 5 }"
          :search-options="{ enabled: true }"
        />
      </div> 
      
      <!-- Add Expense Modal -->
      <div v-if="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeExpenseModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Expense</h2>
          <form @submit.prevent="submitExpense">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Description</label>
              <input type="text" v-model="expenseForm.description" required class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="e.g. Grocery shopping" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Category</label>
              <select v-model="expenseForm.category" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select category</option>
                <option value="Food">Food & Dining</option>
                <option value="Bills">Bills & Utilities</option>
                <option value="Transport">Transportation</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Health">Health & Medical</option>
                <option value="Shopping">Shopping</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Amount (₱)</label>
              <input type="number" v-model.number="expenseForm.amount" required min="0" step="0.01" class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Date</label>
              <input type="date" v-model="expenseForm.date" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeExpenseModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Save Expense</button>
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

      
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'

  const columns = ref([
    { label: 'Date', field: 'date' },
    { label: 'Category', field: 'category' },
    { label: 'Description', field: 'desc' },
    { label: 'Amount (₱)', field: 'amount', type: 'number', sortable: true },
  ])

  const rows = ref([
    { date: '2025-09-20', category: 'Food', desc: 'Groceries', amount: 2200 },
    { date: '2025-09-21', category: 'Bills', desc: 'Electricity', amount: 1500 },
    { date: '2025-09-22', category: 'Transport', desc: 'Grab ride', amount: 250 },
    { date: '2025-09-23', category: 'Entertainment', desc: 'Netflix', amount: 370 },
    { date: '2025-09-24', category: 'Savings', desc: 'Deposit', amount: 5000 },
    { date: '2025-09-25', category: 'Health', desc: 'Pharmacy', amount: 890 },
  ])

  // Expense Modal
  const showExpenseModal = ref(false)
  const expenseForm = ref({ description: '', category: '', amount: '', date: '' })
  function openExpenseModal() { showExpenseModal.value = true }
  function closeExpenseModal() { 
    showExpenseModal.value = false
    expenseForm.value = { description: '', category: '', amount: '', date: '' }
  }
  function submitExpense() {
    rows.value.push({
      date: expenseForm.value.date,
      category: expenseForm.value.category,
      desc: expenseForm.value.description,
      amount: -Math.abs(expenseForm.value.amount) // Make expenses negative
    })
    closeExpenseModal()
  }

  // Income Modal
  const showIncomeModal = ref(false)
  const incomeForm = ref({ description: '', category: '', amount: '', date: '' })
  function openIncomeModal() { showIncomeModal.value = true }
  function closeIncomeModal() { 
    showIncomeModal.value = false
    incomeForm.value = { description: '', category: '', amount: '', date: '' }
  }
  function submitIncome() {
    rows.value.push({
      date: incomeForm.value.date,
      category: incomeForm.value.category,
      desc: incomeForm.value.description,
      amount: Math.abs(incomeForm.value.amount) // Make income positive
    })
    closeIncomeModal()
  }
</script>


<style scoped>
/* Custom modal animation, optional */
</style>
