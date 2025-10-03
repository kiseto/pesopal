<template>
    <div class="min-h-[calc(100vh-5.2rem)] bg-gray-50 flex items-center justify-center px-8 py-12">
        <div class="max-w-3xl w-full flex flex-row items-center justify-center">
            <!-- Registration Form (Left) -->
            <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 w-full max-w-lg">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Create Account</h1>
                    <p class="text-lg text-gray-600">Join PesoPal today</p>
                </div>
                <form @submit.prevent="handleRegister">
                    <!-- Error/Success Messages -->
                    <div v-if="message" class="mb-4 p-3 rounded-lg" :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ message }}
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">First Name</label>
                            <input 
                                v-model="formData.first_name"
                                type="text" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="First name"
                            >
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Last Name</label>
                            <input 
                                v-model="formData.last_name"
                                type="text" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Last name"
                            >
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Phone</label>
                            <input 
                                v-model="formData.phone"
                                type="tel" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Phone"
                            >
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Birthday</label>
                            <input 
                                v-model="formData.birthday"
                                type="date" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
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
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Password</label>
                            <input 
                                v-model="formData.password"
                                type="password" 
                                required
                                minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Password"
                            >
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Confirm</label>
                            <input 
                                v-model="formData.confirm_password"
                                type="password" 
                                required
                                minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Confirm"
                            >
                        </div>
                    </div>
                    <button 
                        type="submit" 
                        :disabled="isLoading"
                        class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-lg mb-4 disabled:opacity-50"
                    >
                        {{ isLoading ? 'Creating Account...' : 'Create Account' }}
                    </button>
                    <div class="text-center">
                        <p class="text-gray-600">Already have an account? <router-link to="/" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</router-link></p>
                    </div>
                </form>
            </div>
            <!-- Placeholder Image (Right)
            <div class="bg-gray-200 h-80 w-80 rounded-2xl max-w-lg flex items-center justify-center">
                <span class="text-gray-500 text-lg">Image Placeholder</span>
            </div>-->
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
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    birthday: '',
    password: '',
    confirm_password: ''
})

// UI state
const isLoading = ref(false)
const message = ref('')
const messageType = ref('error') // 'success' or 'error'

// Handle registration
const handleRegister = async () => {
    // Clear previous messages
    message.value = ''
    
    // Validate passwords match
    if (formData.value.password !== formData.value.confirm_password) {
        message.value = 'Passwords do not match'
        messageType.value = 'error'
        return
    }

    // Validate required fields
    if (!formData.value.first_name || !formData.value.last_name || !formData.value.email || !formData.value.password) {
        message.value = 'Please fill in all required fields'
        messageType.value = 'error'
        return
    }

    try {
        isLoading.value = true
        
        // Call registration API
        const result = await ApiService.register(formData.value)
        
        if (result.success) {
            message.value = 'Registration successful! Redirecting to login...'
            messageType.value = 'success'
            
            // Reset form
            formData.value = {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                birthday: '',
                password: '',
                confirm_password: ''
            }
            
            // Redirect to login after 2 seconds
            setTimeout(() => {
                router.push('/login')
            }, 2000)
            
        } else {
            message.value = result.message || 'Registration failed. Please try again.'
            messageType.value = 'error'
        }
        
    } catch (error) {
        message.value = 'Network error. Please check your connection and try again.'
        messageType.value = 'error'
        console.error('Registration error:', error)
    } finally {
        isLoading.value = false
    }
}
</script>

<style scoped>
</style>
