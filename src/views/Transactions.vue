<template>
  <div class="max-w-6xl mx-auto p-4">
    <!-- Header & Add Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Transactions</h1>
      <div class="flex gap-2">
        <button @click="openModal('income')" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add Income</button>
        <button @click="openModal('expense')" class="cursor-pointer bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Add Expense</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-col md:flex-row gap-4 md:items-center">
      <div class="flex flex-col md:flex-row gap-4 w-full">
        <input type="date" v-model="filters.startDate" class="border rounded px-2 py-1" />
        <span class="hidden md:inline">to</span>
        <input type="date" v-model="filters.endDate" class="border rounded px-2 py-1" />
        <select v-model="filters.category" class="border rounded px-2 py-1">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
        <select v-model="filters.type" class="border rounded px-2 py-1">
          <option value="">All Types</option>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>
        <input type="text" v-model="filters.search" placeholder="Search description..." class="border rounded px-2 py-1 w-full md:w-48" />
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow mb-6">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Date</th>
            <th class="px-4 py-2 text-left">Description</th>
            <th class="px-4 py-2 text-left">Category</th>
            <th class="px-4 py-2 text-right">Amount</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tx in filteredTransactions" :key="tx.id" class="border-b">
            <td class="px-4 py-2">{{ tx.date }}</td>
            <td class="px-4 py-2">{{ tx.description }}</td>
            <td class="px-4 py-2">{{ tx.category }}</td>
            <td class="px-4 py-2 text-right" :class="tx.type === 'income' ? 'text-green-600' : 'text-red-600'">
              {{ tx.type === 'income' ? '+' : '-' }}{{ tx.amount.toLocaleString() }}
            </td>
            <td class="px-4 py-2 text-center">
              <button @click="editTransaction(tx)" class="text-blue-600 hover:underline mr-2">Edit</button>
              <button @click="deleteTransaction(tx.id)" class="text-red-600 hover:underline">Delete</button>
            </td>
          </tr>
          <tr v-if="filteredTransactions.length === 0">
            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No transactions found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Summary Section -->
    <div class="bg-white rounded-lg shadow p-4 flex flex-col md:flex-row gap-4 justify-between items-center">
      <div class="flex flex-col md:flex-row gap-8">
        <div>
          <div class="text-gray-500">Total Income</div>
          <div class="text-green-600 font-bold text-xl">{{ summary.income.toLocaleString() }}</div>
        </div>
        <div>
          <div class="text-gray-500">Total Expenses</div>
          <div class="text-red-600 font-bold text-xl">{{ summary.expense.toLocaleString() }}</div>
        </div>
        <div>
          <div class="text-gray-500">Net Balance</div>
          <div :class="summary.balance >= 0 ? 'text-green-700' : 'text-red-700'" class="font-bold text-xl">{{ summary.balance.toLocaleString() }}</div>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px] bg-opacity-30">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button @click="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
        <h2 class="text-xl font-bold mb-4">{{ modalType === 'income' ? 'Add Income' : 'Add Expense' }}</h2>
        <form @submit.prevent="submitTransaction">
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Amount</label>
            <input type="number" v-model.number="form.amount" required min="0" class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Category</label>
            <select v-model="form.category" required class="border rounded px-2 py-1 w-full">
              <option value="" disabled>Select category</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Date</label>
            <input type="date" v-model="form.date" required class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Description</label>
            <input type="text" v-model="form.description" required class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const transactions = ref([
  // Example data
  { id: 1, type: 'income', amount: 5000, category: 'Salary', date: '2025-09-01', description: 'September Salary' },
  { id: 2, type: 'expense', amount: 1200, category: 'Groceries', date: '2025-09-03', description: 'Weekly groceries' },
  { id: 3, type: 'income', amount: 200, category: 'Freelance', date: '2025-09-05', description: 'Logo design' },
  { id: 4, type: 'expense', amount: 300, category: 'Transport', date: '2025-09-06', description: 'Taxi rides' },
])

const categories = ref(['Salary', 'Freelance', 'Groceries', 'Transport', 'Utilities', 'Entertainment', 'Other'])

const filters = ref({
  startDate: '',
  endDate: '',
  category: '',
  type: '',
  search: ''
})

const showModal = ref(false)
const modalType = ref('income')
const form = ref({
  id: null,
  type: 'income',
  amount: '',
  category: '',
  date: '',
  description: ''
})

function openModal(type) {
  modalType.value = type
  form.value = { id: null, type, amount: '', category: '', date: '', description: '' }
  showModal.value = true
}
function closeModal() {
  showModal.value = false
}
function submitTransaction() {
  if (form.value.id) {
    // Edit existing
    const idx = transactions.value.findIndex(t => t.id === form.value.id)
    if (idx !== -1) transactions.value[idx] = { ...form.value }
  } else {
    // Add new
    transactions.value.push({ ...form.value, id: Date.now() })
  }
  showModal.value = false
}
function editTransaction(tx) {
  form.value = { ...tx }
  modalType.value = tx.type
  showModal.value = true
}
function deleteTransaction(id) {
  transactions.value = transactions.value.filter(t => t.id !== id)
}

const filteredTransactions = computed(() => {
  return transactions.value.filter(tx => {
    const matchesType = !filters.value.type || tx.type === filters.value.type
    const matchesCategory = !filters.value.category || tx.category === filters.value.category
    const matchesSearch = !filters.value.search || tx.description.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchesStart = !filters.value.startDate || tx.date >= filters.value.startDate
    const matchesEnd = !filters.value.endDate || tx.date <= filters.value.endDate
    return matchesType && matchesCategory && matchesSearch && matchesStart && matchesEnd
  })
})

const summary = computed(() => {
  let income = 0, expense = 0
  filteredTransactions.value.forEach(tx => {
    if (tx.type === 'income') income += tx.amount
    else expense += tx.amount
  })
  return {
    income,
    expense,
    balance: income - expense
  }
})
</script>

<style scoped>
/* Custom modal animation, optional */
</style>
