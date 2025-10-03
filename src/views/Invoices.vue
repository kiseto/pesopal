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
          <div class="text-red-600 text-3xl font-bold mb-2">₱{{ summary.total_due.toLocaleString() }}</div>
          <div class="text-gray-600">Total Due</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="text-green-600 text-3xl font-bold mb-2">₱{{ summary.paid.toLocaleString() }}</div>
          <div class="text-gray-600">Paid</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
            <ClockIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="text-orange-600 text-3xl font-bold mb-2">₱{{ summary.overdue.toLocaleString() }}</div>
          <div class="text-gray-600">Overdue</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
            <CalendarDaysIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="text-blue-600 text-3xl font-bold mb-2">₱{{ summary.upcoming.toLocaleString() }}</div>
          <div class="text-gray-600">Upcoming</div>
        </div>
      </div>
      <div v-if="isLoading" class="bg-white rounded-2xl border-neutral-200 border-1 p-8 text-center">
        <div class="text-gray-500">Loading invoices and receipts...</div>
      </div>
      <!-- Invoices Table -->
      <div class="bg-white rounded-2xl border-neutral-200 border-1 mb-8">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Invoices</h2>
          <p class="text-gray-600">Bills and payments you need to track</p>
        </div>
        <div class="p-6">
          <vue-good-table
                v-if="!isLoading"
                :columns="invoiceColumns"
                :rows="invoiceRows"
                :pagination-options="{ enabled: true, perPage: 5 }"
                :search-options="{ enabled: true }"
          >
            <template #table-row="props">
              <span v-if="props.column.field == 'actions'">
                <button @click="viewInvoice(props.row)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mr-1 hover:bg-blue-600">View</button>
                <button @click="editInvoice(props.row)" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs mr-1 hover:bg-yellow-600">Edit</button>
                <button @click="deleteInvoice(props.row)" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Delete</button>
              </span>
              <span v-else>
                {{ props.formattedRow[props.column.field] }}
              </span>
            </template>
          </vue-good-table>
          <div v-else class="text-center py-8 text-gray-500">Loading invoices...</div>
        </div>
      </div>

      <!-- Receipts Table -->
      <div class="bg-white rounded-2xl border-neutral-200 border-1">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Receipts</h2>
          <p class="text-gray-600">Your expense receipts and documentation</p>
        </div>
        <div class="p-6">
          <vue-good-table
                v-if="!isLoading"
                :columns="receiptColumns"
                :rows="receiptRows"
                :pagination-options="{ enabled: true, perPage: 5 }"
                :search-options="{ enabled: true }"
          >
            <template #table-row="props">
              <span v-if="props.column.field == 'actions'">
                <button @click="viewReceipt(props.row)" class="cursor-pointer bg-blue-500 text-white px-2 py-1 rounded text-xs mr-1 hover:bg-blue-600">View</button>
                <button @click="editReceipt(props.row)" class="cursor-pointer bg-yellow-500 text-white px-2 py-1 rounded text-xs mr-1 hover:bg-yellow-600">Edit</button>
                <button @click="deleteReceipt(props.row)" class="cursor-pointer bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Delete</button>
              </span>
              <span v-else-if="props.column.field == 'receipt'">
                <a 
                  v-if="props.row.receipt && props.row.receipt !== 'No file'" 
                  :href="`file:///c:/xampp/htdocs/pesopal/uploads/receipts/${props.row.receipt}`" 
                  target="_blank" 
                  class="text-blue-600 hover:text-blue-800 underline cursor-pointer"
                >
                  {{ props.row.receipt }}
                </a>
                <span v-else class="text-gray-500">No file</span>
              </span>
              <span v-else>
                {{ props.formattedRow[props.column.field] }}
              </span>
            </template>
          </vue-good-table>
          <div v-else class="text-center py-8 text-gray-500">Loading receipts...</div>
        </div>
      </div>
      
      <!-- Add Invoice Modal -->
      <div v-if="showInvoiceModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeInvoiceModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">{{ isEditingInvoice ? 'Edit Invoice' : 'Add Invoice' }}</h2>
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
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">{{ isEditingInvoice ? 'Update Invoice' : 'Save Invoice' }}</button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Add Receipt Modal -->
      <div v-if="showReceiptModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-[1px]">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-xl p-8 relative">
          <button @click="closeReceiptModal" class="cursor-pointer absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <h2 class="text-xl font-bold mb-4">{{ isEditingReceipt ? 'Edit Receipt' : 'Add Receipt' }}</h2>
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
              <input 
                ref="fileInput" 
                type="file" 
                @change="handleFileUpload" 
                accept="image/*,application/pdf" 
                class="cursor-pointer border border-gray-300 rounded px-3 py-2 w-full" 
              />
              <small class="text-gray-500">Upload image or PDF of your receipt</small>
              <div v-if="receiptForm.receipt && !isEditingReceipt" class="text-sm text-gray-600 mt-1">
                Selected: {{ receiptForm.receipt }}
              </div>
              <div v-if="isEditingReceipt && receiptForm.receipt" class="text-sm text-gray-600 mt-1">
                Current file: {{ receiptForm.receipt }} (select a new file to replace)
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Notes</label>
              <textarea v-model="receiptForm.notes" class="border border-gray-300 rounded px-3 py-2 w-full" rows="2" placeholder="Additional notes or details"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" @click="closeReceiptModal" class="cursor-pointer px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
              <button type="submit" class="cursor-pointer px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">{{ isEditingReceipt ? 'Update Receipt' : 'Save Receipt' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted } from 'vue'
  import { 
    ExclamationTriangleIcon, 
    CheckCircleIcon, 
    ClockIcon,
    CalendarDaysIcon 
  } from '@heroicons/vue/24/outline'
  import ApiService from '../services/ApiService.js'

  // Separate columns for invoices
  const invoiceColumns = ref([
    { label: '#', field: 'displayId', type: 'number' },
    { label: 'Title', field: 'title' },
    { label: 'Category', field: 'category' },
    { label: 'Amount (₱)', field: 'amount', type: 'number' },
    { label: 'Due Date', field: 'dueDate', type: 'date', dateInputFormat: 'yyyy-MM-dd', dateOutputFormat: 'MMM dd, yyyy' },
    { label: 'Status', field: 'status' },
    { label: 'Notes', field: 'notes' },
    { label: 'Actions', field: 'actions' }
  ])

  // Separate columns for receipts
  const receiptColumns = ref([
    { label: '#', field: 'displayId', type: 'number' },
    { label: 'Title', field: 'title' },
    { label: 'Category', field: 'category' },
    { label: 'Amount (₱)', field: 'amount', type: 'number' },
    { label: 'Date', field: 'date', type: 'date', dateInputFormat: 'yyyy-MM-dd', dateOutputFormat: 'MMM dd, yyyy' },
    { label: 'Receipt File', field: 'receipt' },
    { label: 'Notes', field: 'notes' },
    { label: 'Actions', field: 'actions' }
  ])

  const invoiceRows = ref([])
  const receiptRows = ref([])
  const isLoading = ref(true)
  const fileInput = ref(null)
  const summary = ref({
    total_due: 0,
    paid: 0,
    overdue: 0,
    upcoming: 0
  })

  // Load data from APIs
  const loadData = async () => {
    try {
      isLoading.value = true
      
      // First get raw data to debug
      const rawDataResult = await ApiService.getRawInvoicesData()
      console.log('=== RAW DATABASE DATA ===')
      console.log('Raw database result:', rawDataResult)
      
      if (rawDataResult.success) {
        const rawData = rawDataResult.data
        console.log('Your user_id:', rawData.user_id)
        console.log('Raw invoices in DB:', rawData.raw_invoices)
        console.log('Raw receipts in DB:', rawData.raw_receipts) 
        console.log('Manual summary calculation:', rawData.manual_summary)
        
        // Use the manual summary which is calculated directly from raw data
        summary.value = rawData.manual_summary
        
        // Format invoices for invoices table only
        const invoiceTableRows = []
        if (rawData.raw_invoices) {
          rawData.raw_invoices.forEach((invoice, index) => {
            invoiceTableRows.push({
              id: parseInt(invoice.id), // Keep original ID for backend operations
              displayId: index + 1, // Sequential number for display
              title: invoice.title,
              category: invoice.category,
              amount: parseFloat(invoice.amount),
              dueDate: invoice.due_date,
              status: invoice.status,
              notes: invoice.notes || '',
              actions: ''
            })
          })
        }
        
        // Format receipts for receipts table only
        const receiptTableRows = []
        if (rawData.raw_receipts) {
          rawData.raw_receipts.forEach((receipt, index) => {
            receiptTableRows.push({
              id: parseInt(receipt.id), // Keep original ID for backend operations
              displayId: index + 1, // Sequential number for display
              title: receipt.title,
              category: receipt.category,
              amount: parseFloat(receipt.amount),
              date: receipt.receipt_date,
              receipt: receipt.file_path || 'No file', // Use actual file_path from database
              notes: receipt.notes || '',
              actions: ''
            })
          })
        }
        
        invoiceRows.value = invoiceTableRows
        receiptRows.value = receiptTableRows
        
        console.log('Invoices table data:', invoiceTableRows)
        console.log('Receipts table data:', receiptTableRows)
        console.log('Summary (invoices only):', rawData.manual_summary)
      } else {
        console.error('Failed to get raw data:', rawDataResult.message)
      }
      
    } catch (error) {
      console.error('Error loading data:', error)
    } finally {
      isLoading.value = false
    }
  }

  // Invoice Modal
  const showInvoiceModal = ref(false)
  const isEditingInvoice = ref(false)
  const invoiceForm = ref({ title: '', category: '', amount: '', dueDate: '', status: 'Unpaid', notes: '' })
  function openInvoiceModal() { 
    isEditingInvoice.value = false // Reset edit mode
    showInvoiceModal.value = true 
    // Set default due date to tomorrow
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    invoiceForm.value.dueDate = tomorrow.toISOString().split('T')[0]
  }
  function closeInvoiceModal() { 
    showInvoiceModal.value = false
    isEditingInvoice.value = false
    invoiceForm.value = { title: '', category: '', amount: '', dueDate: '', status: 'Unpaid', notes: '' }
  }
  async function submitInvoice() {
    try {
      if (isEditingInvoice.value) {
        // Update existing invoice
        console.log('=== UPDATING INVOICE ===')
        console.log('Invoice ID:', invoiceForm.value.id)
        console.log('Form data:', invoiceForm.value)
        
        const updateData = {
          title: invoiceForm.value.title,
          category: invoiceForm.value.category,
          amount: invoiceForm.value.amount,
          due_date: invoiceForm.value.dueDate,
          status: invoiceForm.value.status,
          notes: invoiceForm.value.notes
        }
        
        console.log('Update data being sent:', updateData)
        
        const result = await ApiService.updateInvoice(invoiceForm.value.id, updateData)
        
        console.log('Update result:', result)
        
        if (result.success) {
          console.log('Update successful, reloading data...')
          closeInvoiceModal()
          // Just reload data from database, don't update local array manually
          await loadData()
        } else {
          console.error('Failed to update invoice:', result.message)
          alert('Failed to update invoice: ' + result.message)
        }
      } else {
        // Add new invoice
        const result = await ApiService.addInvoice({
          title: invoiceForm.value.title,
          category: invoiceForm.value.category,
          amount: invoiceForm.value.amount,
          due_date: invoiceForm.value.dueDate,
          status: invoiceForm.value.status,
          notes: invoiceForm.value.notes
        })
        
        if (result.success) {
          console.log('=== ADDING NEW INVOICE ===')
          console.log('Add result:', result)
          
          closeInvoiceModal()
          // Just reload data, don't manually add to avoid duplicates
          loadData()
        } else {
          console.error('Failed to add invoice:', result.message)
          alert('Failed to add invoice: ' + result.message)
        }
      }
    } catch (error) {
      console.error('Error saving invoice:', error)
      alert('Error saving invoice')
    }
  }

  // Receipt Modal
  const showReceiptModal = ref(false)
  const isEditingReceipt = ref(false)
  const receiptForm = ref({ title: '', category: '', amount: '', dueDate: '', notes: '', receipt: null, receiptFile: null })
  function openReceiptModal() { 
    isEditingReceipt.value = false // Reset edit mode
    showReceiptModal.value = true 
    // Set default date to today
    receiptForm.value.dueDate = new Date().toISOString().split('T')[0]
    // Reset file input
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }
  function closeReceiptModal() { 
    showReceiptModal.value = false
    isEditingReceipt.value = false
    receiptForm.value = { title: '', category: '', amount: '', dueDate: '', notes: '', receipt: null, receiptFile: null }
    // Reset file input
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }
  function handleFileUpload(event) {
    const file = event.target.files[0]
    if (file) {
      // Store the actual file object for upload
      receiptForm.value.receiptFile = file
      receiptForm.value.receipt = file.name
      console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type)
    }
  }
  async function submitReceipt() {
    try {
      if (isEditingReceipt.value) {
        // Update existing receipt
        console.log('=== UPDATING RECEIPT ===')
        console.log('Receipt ID:', receiptForm.value.id)
        console.log('Form data:', receiptForm.value)
        
        const updateData = {
          title: receiptForm.value.title,
          category: receiptForm.value.category,
          amount: receiptForm.value.amount,
          receipt_date: receiptForm.value.dueDate,
          notes: receiptForm.value.notes
        }
        
        // Add file_path if a new file was selected
        if (receiptForm.value.receipt && receiptForm.value.receiptFile) {
          updateData.file_path = receiptForm.value.receipt
        }
        
        console.log('Update data being sent:', updateData)
        
        const result = await ApiService.updateReceipt(receiptForm.value.id, updateData)
        
        if (result.success) {
          // Update the local array
          const index = receiptRows.value.findIndex(row => row.id === receiptForm.value.id)
          if (index > -1) {
            receiptRows.value[index] = {
              id: receiptForm.value.id,
              title: receiptForm.value.title,
              category: receiptForm.value.category,
              amount: receiptForm.value.amount,
              date: receiptForm.value.dueDate,
              receipt: receiptForm.value.receipt || 'No file',
              notes: receiptForm.value.notes,
              actions: ''
            }
          }
          closeReceiptModal()
          // Reload data to ensure accuracy
          loadData()
        } else {
          console.error('Failed to update receipt:', result.message)
          alert('Failed to update receipt: ' + result.message)
        }
      } else {
        // Add new receipt
        const result = await ApiService.addReceipt({
          title: receiptForm.value.title,
          category: receiptForm.value.category,
          amount: receiptForm.value.amount,
          receipt_date: receiptForm.value.dueDate,
          notes: receiptForm.value.notes,
          receipt: receiptForm.value.receipt // Just send the filename for now
        })
        
        if (result.success) {
          console.log('=== ADDING NEW RECEIPT ===')
          console.log('Add result:', result)
          
          closeReceiptModal()
          // Just reload data, don't manually add to avoid duplicates
          loadData()
        } else {
          console.error('Failed to add receipt:', result.message)
          alert('Failed to add receipt: ' + result.message)
        }
      }
    } catch (error) {
      console.error('Error saving receipt:', error)
      alert('Error saving receipt')
    }
  }

  // Invoice Actions
  function viewInvoice(invoice) {
    alert(`Viewing Invoice: ${invoice.title}\nAmount: ₱${invoice.amount}\nStatus: ${invoice.status}\nDue Date: ${invoice.dueDate}\nNotes: ${invoice.notes || 'No notes'}`)
  }
  
  function editInvoice(invoice) {
    // Pre-fill the form with existing data and set edit mode
    invoiceForm.value = {
      id: invoice.id, // Add ID to track which invoice we're editing
      title: invoice.title,
      category: invoice.category,
      amount: invoice.amount,
      dueDate: invoice.dueDate,
      status: invoice.status,
      notes: invoice.notes || ''
    }
    isEditingInvoice.value = true // Set edit mode
    showInvoiceModal.value = true
  }
  
  async function deleteInvoice(invoice) {
    if (confirm(`Are you sure you want to delete the invoice "${invoice.title}"?`)) {
      try {
        // Call API to delete from database
        const result = await ApiService.deleteInvoice(invoice.id)
        
        if (result.success) {
          // Remove from local array for immediate UI update
          const index = invoiceRows.value.findIndex(row => row.id === invoice.id)
          if (index > -1) {
            invoiceRows.value.splice(index, 1)
          }
          
          // Reload data to update summary cards
          loadData()
          
          console.log('Invoice deleted successfully:', invoice.id)
        } else {
          alert('Failed to delete invoice: ' + result.message)
        }
      } catch (error) {
        console.error('Error deleting invoice:', error)
        alert('Error deleting invoice')
      }
    }
  }

  // Receipt Actions
  function viewReceipt(receipt) {
    alert(`Viewing Receipt: ${receipt.title}\nAmount: ₱${receipt.amount}\nCategory: ${receipt.category}\nDate: ${receipt.date}\nReceipt File: ${receipt.receipt}\nNotes: ${receipt.notes || 'No notes'}`)
  }
  
  function editReceipt(receipt) {
    // Pre-fill the form with existing data and set edit mode
    receiptForm.value = {
      id: receipt.id, // Add ID to track which receipt we're editing
      title: receipt.title,
      category: receipt.category,
      amount: receipt.amount,
      dueDate: receipt.date, // Using dueDate field name for the date input
      notes: receipt.notes || '',
      receipt: receipt.receipt || null,
      receiptFile: null // Reset file input for new upload
    }
    isEditingReceipt.value = true // Set edit mode
    showReceiptModal.value = true
  }
  
  async function deleteReceipt(receipt) {
    if (confirm(`Are you sure you want to delete the receipt "${receipt.title}"?`)) {
      try {
        // Call API to delete from database
        const result = await ApiService.deleteReceipt(receipt.id)
        
        if (result.success) {
          // Remove from local array for immediate UI update
          const index = receiptRows.value.findIndex(row => row.id === receipt.id)
          if (index > -1) {
            receiptRows.value.splice(index, 1)
          }
          
          // Reload data to ensure accuracy
          loadData()
          
          console.log('Receipt deleted successfully:', receipt.id)
        } else {
          alert('Failed to delete receipt: ' + result.message)
        }
      } catch (error) {
        console.error('Error deleting receipt:', error)
        alert('Error deleting receipt')
      }
    }
  }

  // Load data on component mount
  onMounted(() => {
    loadData()
  })
</script>

<style scoped>
</style>
