<!--Dashboard-->
<template>
  <div>
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white border-b-neutral-200 border-b-1 p-4">
      <div class="container mx-auto flex items-center justify-between">
        <!-- Logo -->
        <router-link to="/dashboard">
            <div class="flex items-center gap-2">
              <img v-bind:src="pesopal_logo" alt="PesoPal Logo" class="h-8 w-8" />
              <span class="font-extrabold text-2xl text-blue-700">PesoPal</span>
            </div>
        </router-link>
        <!-- Links -->
        <ul class="flex items-center gap-8 font-semibold text-gray-700">
          <li><router-link to="/dashboard" class="hover:text-blue-600">Dashboard</router-link></li>
          <li><router-link to="/dashboard/transactions" class="hover:text-blue-600">Transactions</router-link></li>
          <li class="relative group">
            <button class="cursor-pointer flex items-center gap-1 hover:text-blue-600 focus:outline-none">
              Finance
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul class="absolute left-0 top-full w-40 bg-white border border-gray-200 rounded-lg shadow-lg hidden group-hover:block z-10">
              <li><router-link to="/dashboard/invoices" class="block px-4 py-2 hover:bg-blue-50">Invoices & Receipts</router-link></li>
              <li><router-link to="/dashboard/budgeting" class="block px-4 py-2 hover:bg-blue-50">Budgeting</router-link></li>
              <li><router-link to="/dashboard/reports" class="block px-4 py-2 hover:bg-blue-50">Reports</router-link></li>
            </ul>
          </li>
          <!--<li><router-link to="/settings" class="hover:text-blue-600">Settings</router-link></li>-->
          <li><router-link to="/"><button class="cursor-pointer text-red-600 font-semibold hover:underline underline-offset-6 decoration-2">Logout</button></router-link></li>
        </ul>
      </div>
    </nav>
    <!-- Router View for Dashboard Subpages -->
    <router-view />
  </div>
</template>

<script setup>
import pesopal_logo from '/src/assets/images/pesopal_logo.svg'
</script>

<style scoped>
</style>