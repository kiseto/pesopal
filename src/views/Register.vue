<template>
    <div class="min-h-[calc(100vh-5.2rem)] bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-8 py-12">
        <div class="max-w-3xl w-full flex flex-row items-center justify-center">
            <!-- Registration Form (Left) -->
            <div class="bg-white rounded-2xl border-neutral-100 shadow-md border-1 p-8 w-full max-w-lg">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Create Account</h1>
                    <p class="text-lg text-gray-600">Join PesoPal today</p>
                </div>
                <form>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">First Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="First name">
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Last Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Last name">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Phone</label>
                            <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Phone">
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Birthday</label>
                            <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password">
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Confirm</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm">
                        </div>
                    </div>
                    <button type="submit" class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-lg mb-4">Create Account</button>
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
// Registration logic can be added here
</script>

<style scoped>
</style>
