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
          <button class="bg-gray-600 text-white px-4 py-2 rounded cursor-pointer">
            Add Invoice
          </button>

          <button class="bg-gray-600 text-white px-4 py-2 rounded cursor-pointer">
            Add Receipt
          </button>
        </div>
      </div>
      
      
      
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">💸</span></div>
          <div class="text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Total Due</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">✔️</span></div>
          <div class="text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Paid</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">⏰</span></div>
          <div class="text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Overdue</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 flex flex-col">
          <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mb-4"><span class="text-gray-500">📅</span></div>
          <div class="text-3xl font-bold mb-2">₱0</div>
          <div class="text-gray-600">Upcoming</div>
        </div>
      </div>
      <vue-good-table
            :columns="columns"
            :rows="rows"
            :pagination-options="{ enabled: true, perPage: 5 }"
            :search-options="{ enabled: true }"
      />
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'

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
</script>

<style scoped>
</style>
