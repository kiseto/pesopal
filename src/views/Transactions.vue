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
          <button class="bg-gray-600 text-white px-4 py-2 rounded cursor-pointer">
            Add Expense
          </button>

          <button class="bg-gray-600 text-white px-4 py-2 rounded cursor-pointer">
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
</script>


<style scoped>
/* Custom modal animation, optional */
</style>
