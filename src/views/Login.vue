<template>
    <div class="min-h-[calc(100vh-5.2rem)] bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl border-neutral-100 shadow-md border-1 p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Sign In</h1>
                    <p class="text-lg text-gray-600">Login to PesoPal</p>
                </div>
                <form>
                    <div class="mb-4">
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email">
                    </div>
                    <div class="mb-6">
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password">
                    </div>
                    <!--<button type="submit" class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-lg mb-4">Login</button>-->
                    <router-link  to="/dashboard"><button class="cursor-pointer w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold text-lg mb-4">Login</button></router-link>
                    <div class="text-center">
                        <p class="text-gray-600">Don't have an account? <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-semibold">Sign up</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
// Login logic can be added here
</script>

<style scoped>
</style>
