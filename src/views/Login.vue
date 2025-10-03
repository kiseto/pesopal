<template>
    <div class="min-h-[calc(100vh-5.2rem)] bg-gray-50 flex items-center justify-center px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Sign In</h1>
                    <p class="text-lg text-gray-600">Login to PesoPal</p>
                </div>
                <form @submit.prevent="handleLogin">
                    <!-- Error/Success Messages -->
                    <div v-if="message" class="mb-4 p-3 rounded-lg" :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ message }}
                    </div>

                    <div class="mb-4">
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Email</label>
                        <input 
                            v-model="formData.email"
                            type="email" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            placeholder="Email"
                        >
                    </div>
                    <div class="mb-6">
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Password</label>
                        <input 
                            v-model="formData.password"
                            type="password" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            placeholder="Password"
                        >
                    </div>
                    <button 
                        type="submit" 
                        :disabled="isLoading"
                        class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-lg mb-4 disabled:opacity-50"
                    >
                        {{ isLoading ? 'Signing In...' : 'Login' }}
                    </button>
                    <div class="text-center">
                        <p class="text-gray-600">Don't have an account? <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-semibold">Sign up</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import ApiService from '../services/ApiService.js'

const router = useRouter()

// Form data
const formData = ref({
    email: '',
    password: ''
})

// UI state
const isLoading = ref(false)
const message = ref('')
const messageType = ref('error') // 'success' or 'error'

// Handle login
const handleLogin = async () => {
    // Clear previous messages
    message.value = ''
    
    // Validate required fields
    if (!formData.value.email || !formData.value.password) {
        message.value = 'Please fill in both email and password'
        messageType.value = 'error'
        return
    }

    try {
        isLoading.value = true
        
        // Call login API
        console.log('Attempting login with:', formData.value)
        const result = await ApiService.login(formData.value)
        
        if (result.success) {
            message.value = 'Login successful! Redirecting...'
            messageType.value = 'success'
            
            // Store user data if needed (you can use localStorage or a store)
            localStorage.setItem('user', JSON.stringify(result.user))
            
            // Debug: Check if session is working
            const sessionCheck = await ApiService.checkSession()
            console.log('Session after login:', sessionCheck)
            
            // Reset form
            formData.value = {
                email: '',
                password: ''
            }
            
            // Redirect to dashboard after 1 second
            setTimeout(() => {
                router.push('/dashboard')
            }, 1000)
            
        } else {
            message.value = result.message || 'Login failed. Please check your credentials.'
            messageType.value = 'error'
        }
        
    } catch (error) {
        message.value = 'Network error. Please check your connection and try again.'
        messageType.value = 'error'
        console.error('Login error:', error)
    } finally {
        isLoading.value = false
    }
}
</script>

<style scoped>
</style>
