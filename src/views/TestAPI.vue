<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold mb-6 text-center">API Connection Test</h2>
      
      <div class="space-y-4">
        <button 
          @click="testConnection" 
          class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition"
        >
          Test Database Connection
        </button>
        
        <div v-if="testResult" class="p-4 rounded" :class="testResult.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
          <p><strong>Status:</strong> {{ testResult.success ? 'Success' : 'Failed' }}</p>
          <p><strong>Message:</strong> {{ testResult.message }}</p>
          <p v-if="testResult.users_count !== undefined"><strong>Users in DB:</strong> {{ testResult.users_count }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ApiService from '../services/ApiService.js'

const testResult = ref(null)

const testConnection = async () => {
  try {
    testResult.value = null
    const result = await ApiService.testConnection()
    testResult.value = result
    console.log('Connection test result:', result)
  } catch (error) {
    testResult.value = { 
      success: false, 
      message: 'Test failed: ' + error.message 
    }
  }
}
</script>