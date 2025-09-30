<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">

      <!-- Header & Add Buttons -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex flex-col">
          <h1 class="text-4xl font-bold text-gray-900">Invoices & Receipts</h1>
          <p class="text-lg text-gray-500">Track payments and keep your receipts on hand</p>
        </div>

        <!--Buttons-->
        <div class="flex gap-2">
          <button @click="openInvoiceModal" class="bg-red-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-red-700 transition">
            Add Invoice
          </button>

          <button @click="openReceiptModal" class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700 transition">
            Add Receipt
          </button>
        </div>
      </div>
      
      
      
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
            <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="text-red-600 text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Total Due</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="text-green-600 text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Paid</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
            <ClockIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="text-orange-600 text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Overdue</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
            <CalendarDaysIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="text-blue-600 text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Upcoming</div>
        </div>
      </div>
      <vue-good-table
            :columns="columns"
            :rows="rows"
            :pagination-options="{ enabled: true, perPage: 5 }"
            :search-options="{ enabled: true }"
      />
      
      <!-- Add Invoice Modal -->
      <div v-if="showInvoiceModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeInvoiceModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Invoice</h2>
          <form @submit.prevent="submitInvoice">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Title</label>
              <input type="text" v-model="invoiceForm.title" required class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="e.g. Electric Bill, Rent Payment" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Category</label>
              <select v-model="invoiceForm.category" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select category</option>
                <option value="Utilities">Utilities</option>
                <option value="Rent">Rent</option>
                <option value="Insurance">Insurance</option>
                <option value="Subscriptions">Subscriptions</option>
                <option value="Services">Services</option>
                <option value="Medical">Medical</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Amount (₱)</label>
              <input type="number" v-model.number="invoiceForm.amount" required min="0" step="0.01" class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Due Date</label>
              <input type="date" v-model="invoiceForm.dueDate" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Status</label>
              <select v-model="invoiceForm.status" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="Unpaid">Unpaid</option>
                <option value="Paid">Paid</option>
                <option value="Overdue">Overdue</option>
                <option value="Upcoming">Upcoming</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Notes</label>
              <textarea v-model="invoiceForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="2" placeholder="Additional notes or details"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeInvoiceModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Save Invoice</button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Add Receipt Modal -->
      <div v-if="showReceiptModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeReceiptModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">Add Receipt</h2>
          <form @submit.prevent="submitReceipt">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Title</label>
              <input type="text" v-model="receiptForm.title" required class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="e.g. Grocery Receipt, Gas Receipt" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Category</label>
              <select v-model="receiptForm.category" required class="border border-gray-300 rounded px-3 py-2 w-full">
                <option value="">Select category</option>
                <option value="Food">Food & Dining</option>
                <option value="Transportation">Transportation</option>
                <option value="Shopping">Shopping</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Health">Health & Medical</option>
                <option value="Travel">Travel</option>
                <option value="Business">Business</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Amount (₱)</label>
              <input type="number" v-model.number="receiptForm.amount" required min="0" step="0.01" class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Date</label>
              <input type="date" v-model="receiptForm.dueDate" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Receipt File</label>
              <input type="file" @change="handleFileUpload" accept="image/*,application/pdf" class="border border-gray-300 rounded px-3 py-2 w-full" />
              <small class="text-gray-500">Upload image or PDF of your receipt</small>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Notes</label>
              <textarea v-model="receiptForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="2" placeholder="Additional notes or details"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeReceiptModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save Receipt</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import { 
    ExclamationTriangleIcon, 
    CheckCircleIcon, 
    ClockIcon,
    CalendarDaysIcon 
  } from '@heroicons/vue/24/outline'

  const columns = ref([
    { label: 'ID', field: 'id', type: 'number' },
    { label: 'Title', field: 'title' },
    { label: 'Category', field: 'category' },
    { label: 'Amount (₱)', field: 'amount', type: 'number' },
    { label: 'Due Date', field: 'dueDate', type: 'date', dateInputFormat: 'yyyy-MM-dd', dateOutputFormat: 'MMM dd, yyyy' },
    { label: 'Status', field: 'status' },
    { label: 'Notes', field: 'notes' },
    { label: 'Receipt', field: 'receipt' },
    { label: 'Actions', field: 'actions' }, // for buttons (view, edit, delete)
  ])

  const rows = ref([
    { id: 1, title: 'Electric Bill', category: 'Utilities', amount: 2500, dueDate: '2025-10-05', status: 'Unpaid', notes: '', receipt: null, actions: '' },
    { id: 2, title: 'Netflix', category: 'Subscriptions', amount: 550, dueDate: '2025-09-30', status: 'Paid', notes: '', receipt: null, actions: '' },
    { id: 3, title: 'Apartment Rent', category: 'Rent', amount: 15000, dueDate: '2025-10-01', status: 'Overdue', notes: '', receipt: null, actions: '' },
  ])

  // Invoice Modal
  const showInvoiceModal = ref(false)
  const invoiceForm = ref({ title: '', category: '', amount: '', dueDate: '', status: 'Unpaid', notes: '' })
  function openInvoiceModal() { showInvoiceModal.value = true }
  function closeInvoiceModal() { 
    showInvoiceModal.value = false
    invoiceForm.value = { title: '', category: '', amount: '', dueDate: '', status: 'Unpaid', notes: '' }
  }
  function submitInvoice() {
    const newId = Math.max(...rows.value.map(r => r.id)) + 1
    rows.value.push({
      id: newId,
      title: invoiceForm.value.title,
      category: invoiceForm.value.category,
      amount: invoiceForm.value.amount,
      dueDate: invoiceForm.value.dueDate,
      status: invoiceForm.value.status,
      notes: invoiceForm.value.notes,
      receipt: null,
      actions: ''
    })
    closeInvoiceModal()
  }

  // Receipt Modal
  const showReceiptModal = ref(false)
  const receiptForm = ref({ title: '', category: '', amount: '', dueDate: '', notes: '', receipt: null })
  function openReceiptModal() { showReceiptModal.value = true }
  function closeReceiptModal() { 
    showReceiptModal.value = false
    receiptForm.value = { title: '', category: '', amount: '', dueDate: '', notes: '', receipt: null }
  }
  function handleFileUpload(event) {
    const file = event.target.files[0]
    if (file) {
      receiptForm.value.receipt = file.name
    }
  }
  function submitReceipt() {
    const newId = Math.max(...rows.value.map(r => r.id)) + 1
    rows.value.push({
      id: newId,
      title: receiptForm.value.title,
      category: receiptForm.value.category,
      amount: receiptForm.value.amount,
      dueDate: receiptForm.value.dueDate,
      status: 'Paid', // Receipts are typically for paid items
      notes: receiptForm.value.notes,
      receipt: receiptForm.value.receipt,
      actions: ''
    })
    closeReceiptModal()
  }
</script>

<style scoped>
</style>
