<template>
  <div class="max-w-7xl mx-auto p-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Total Due</div>
        <div class="text-3xl font-bold text-blue-700">₱{{ summary.totalDue.toLocaleString() }}</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Paid</div>
        <div class="text-3xl font-bold text-green-600">₱{{ summary.paid.toLocaleString() }}</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Overdue</div>
        <div class="text-3xl font-bold text-red-600">₱{{ summary.overdue.toLocaleString() }}</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-8 flex flex-col">
        <div class="text-gray-500 text-lg">Upcoming</div>
        <div class="text-3xl font-bold text-yellow-600">₱{{ summary.upcoming.toLocaleString() }}</div>
      </div>
    </div>

    <!-- Filters & Add Button -->
    <div class="bg-white rounded-2xl shadow p-8 mb-12 flex flex-col md:flex-row gap-4 md:items-center justify-between">
      <div class="flex flex-col md:flex-row gap-4 w-full">
        <input type="text" v-model="filters.search" placeholder="Search title..." class="border rounded px-2 py-1 w-full md:w-48" />
        <select v-model="filters.category" class="border rounded px-2 py-1">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
        <select v-model="filters.status" class="border rounded px-2 py-1">
          <option value="">All Status</option>
          <option value="Paid">Paid</option>
          <option value="Unpaid">Unpaid</option>
          <option value="Overdue">Overdue</option>
        </select>
        <input type="date" v-model="filters.date" class="border rounded px-2 py-1" />
      </div>
      <button @click="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Add Invoice</button>
    </div>

    <!-- Reminders -->
    <div v-if="reminders.length" class="mb-6">
      <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="font-semibold text-yellow-700 mb-2">Reminders</div>
        <ul class="list-disc ml-6 text-yellow-800">
          <li v-for="r in reminders" :key="r.id">{{ r.message }}</li>
        </ul>
      </div>
    </div>

    <!-- Invoices Table -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow mb-12">
      <table class="min-w-full text-lg">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Title</th>
            <th class="px-4 py-2 text-left">Category</th>
            <th class="px-4 py-2 text-right">Amount</th>
            <th class="px-4 py-2 text-left">Due Date</th>
            <th class="px-4 py-2 text-center">Status</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="inv in filteredInvoices" :key="inv.id" class="border-b">
            <td class="px-4 py-2">{{ inv.title }}</td>
            <td class="px-4 py-2">{{ inv.category }}</td>
            <td class="px-4 py-2 text-right">₱{{ inv.amount.toLocaleString() }}</td>
            <td class="px-4 py-2">{{ inv.dueDate }}</td>
            <td class="px-4 py-2 text-center">
              <span :class="statusClass(inv.status)" class="px-2 py-1 rounded text-xs font-semibold">{{ inv.status }}</span>
            </td>
            <td class="px-4 py-2 text-center flex gap-2 justify-center">
              <button @click="viewInvoice(inv)" class="text-blue-600 hover:underline">View</button>
              <button @click="editInvoice(inv)" class="text-gray-600 hover:underline">Edit</button>
              <button @click="deleteInvoice(inv.id)" class="text-red-600 hover:underline">Delete</button>
            </td>
          </tr>
          <tr v-if="filteredInvoices.length === 0">
            <td colspan="6" class="px-4 py-6 text-center text-gray-400">No invoices found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
        <button @click="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
        <h2 class="text-xl font-bold mb-4">{{ modalInvoice.id ? 'Edit Invoice' : 'Add Invoice' }}</h2>
        <form @submit.prevent="submitInvoice">
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Title</label>
            <input type="text" v-model="modalInvoice.title" required class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Category</label>
            <select v-model="modalInvoice.category" required class="border rounded px-2 py-1 w-full">
              <option value="" disabled>Select category</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Amount</label>
            <input type="number" v-model.number="modalInvoice.amount" required min="0" class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Due Date</label>
            <input type="date" v-model="modalInvoice.dueDate" required class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Status</label>
            <select v-model="modalInvoice.status" required class="border rounded px-2 py-1 w-full">
              <option value="Paid">Paid</option>
              <option value="Unpaid">Unpaid</option>
              <option value="Overdue">Overdue</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Receipt (optional)</label>
            <input type="file" @change="handleFileUpload" class="border rounded px-2 py-1 w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-gray-700 mb-1">Notes</label>
            <textarea v-model="modalInvoice.notes" class="border rounded px-2 py-1 w-full" rows="2"></textarea>
          </div>
          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="viewingInvoice" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
        <button @click="closeView" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
        <h2 class="text-xl font-bold mb-4">Invoice Details</h2>
        <div class="mb-2"><strong>Title:</strong> {{ viewingInvoice.title }}</div>
        <div class="mb-2"><strong>Category:</strong> {{ viewingInvoice.category }}</div>
        <div class="mb-2"><strong>Amount:</strong> ₱{{ viewingInvoice.amount.toLocaleString() }}</div>
        <div class="mb-2"><strong>Due Date:</strong> {{ viewingInvoice.dueDate }}</div>
        <div class="mb-2"><strong>Status:</strong> {{ viewingInvoice.status }}</div>
        <div class="mb-2"><strong>Notes:</strong> {{ viewingInvoice.notes || '—' }}</div>
        <div class="mb-2"><strong>Receipt:</strong> <span v-if="viewingInvoice.receipt">Attached</span><span v-else>—</span></div>
        <div class="flex gap-2 mt-4">
          <button @click="markPaid(viewingInvoice)" v-if="viewingInvoice.status !== 'Paid'" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Mark as Paid</button>
          <button @click="editInvoice(viewingInvoice)" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Edit</button>
          <button @click="deleteInvoice(viewingInvoice.id)" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const categories = ref(['Utilities', 'Rent', 'Subscriptions', 'Loans', 'Insurance', 'Other'])
const invoices = ref([
  { id: 1, title: 'Electric Bill', category: 'Utilities', amount: 2500, dueDate: '2025-10-05', status: 'Unpaid', notes: '', receipt: null },
  { id: 2, title: 'Netflix', category: 'Subscriptions', amount: 550, dueDate: '2025-09-30', status: 'Paid', notes: '', receipt: null },
  { id: 3, title: 'Apartment Rent', category: 'Rent', amount: 15000, dueDate: '2025-10-01', status: 'Overdue', notes: '', receipt: null },
])

const filters = ref({
  search: '',
  category: '',
  status: '',
  date: ''
})

const showModal = ref(false)
const modalInvoice = ref({
  id: null,
  title: '',
  category: '',
  amount: '',
  dueDate: '',
  status: 'Unpaid',
  notes: '',
  receipt: null
})

const viewingInvoice = ref(null)

function openModal() {
  modalInvoice.value = { id: null, title: '', category: '', amount: '', dueDate: '', status: 'Unpaid', notes: '', receipt: null }
  showModal.value = true
}
function closeModal() {
  showModal.value = false
}
function handleFileUpload(e) {
  modalInvoice.value.receipt = e.target.files[0] || null
}
function submitInvoice() {
  if (modalInvoice.value.id) {
    // Edit
    const idx = invoices.value.findIndex(i => i.id === modalInvoice.value.id)
    if (idx !== -1) invoices.value[idx] = { ...modalInvoice.value }
  } else {
    // Add
    const newId = Date.now()
    invoices.value.push({ ...modalInvoice.value, id: newId })
  }
  showModal.value = false
}
function viewInvoice(inv) {
  viewingInvoice.value = { ...inv }
}
function closeView() {
  viewingInvoice.value = null
}
function editInvoice(inv) {
  modalInvoice.value = { ...inv }
  showModal.value = true
}
function deleteInvoice(id) {
  invoices.value = invoices.value.filter(i => i.id !== id)
  closeView()
}
function markPaid(inv) {
  inv.status = 'Paid'
  closeView()
}

const filteredInvoices = computed(() => {
  return invoices.value.filter(inv => {
    const matchesSearch = !filters.value.search || inv.title.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchesCategory = !filters.value.category || inv.category === filters.value.category
    const matchesStatus = !filters.value.status || inv.status === filters.value.status
    const matchesDate = !filters.value.date || inv.dueDate === filters.value.date
    return matchesSearch && matchesCategory && matchesStatus && matchesDate
  })
})

const summary = computed(() => {
  let totalDue = 0, paid = 0, overdue = 0, upcoming = 0
  const today = new Date().toISOString().slice(0, 10)
  invoices.value.forEach(inv => {
    if (inv.status === 'Paid') paid += inv.amount
    else if (inv.status === 'Overdue') overdue += inv.amount
    else if (inv.status === 'Unpaid') {
      totalDue += inv.amount
      if (inv.dueDate > today) upcoming += inv.amount
    }
  })
  return { totalDue, paid, overdue, upcoming }
})

const reminders = computed(() => {
  const today = new Date().toISOString().slice(0, 10)
  return invoices.value.filter(inv => {
    if (inv.status === 'Unpaid' && inv.dueDate <= today) {
      return true
    }
    if (inv.status === 'Unpaid' && inv.dueDate > today) {
      const diff = (new Date(inv.dueDate) - new Date(today)) / (1000 * 60 * 60 * 24)
      return diff <= 3 // due in next 3 days
    }
    return false
  }).map(inv => ({ id: inv.id, message: `${inv.title} is due on ${inv.dueDate}` }))
})

function statusClass(status) {
  if (status === 'Paid') return 'bg-green-100 text-green-700'
  if (status === 'Overdue') return 'bg-red-100 text-red-700'
  return 'bg-yellow-100 text-yellow-700'
}
</script>

<style scoped>
</style>
