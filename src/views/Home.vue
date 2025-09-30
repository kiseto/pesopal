<template>
    <!--Sections-->
    <main>
        <!-- Hero Section -->
        <div class="bg-neutral-100 min-h-[calc(100vh-5.2rem)] flex items-center px-8">
            <div class="max-w-6xl mx-auto grid grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-6xl font-extrabold text-gray-900 mb-6">Welcome to <span class="text-blue-600">PesoPal</span></h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">Your complete financial companion for managing money, tracking expenses, and making secure payments with confidence.</p>
                    <div class="flex gap-4">
                        <button class="bg-blue-600 hover:bg-blue-800 transition ease-in-out duration-200 cursor-pointer text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg">Get Started Free</button>
                        <button class="border-2 border-blue-600 text-blue-600 cursor-pointer transition ease-in-out duration-200 hover:bg-blue-50 px-8 py-4 rounded-xl font-semibold text-lg">Learn More</button>
                    </div>
                </div>
                <div class="h-96 rounded-2xl flex items-center justify-center">
                    <img :src="pesopal_logo" alt="PesoPal Logo">
                </div>
            </div>
        </div>
        
        <!-- Features Section -->
        <div id="features" class="bg-white min-h-screen flex items-center justify-center px-8 py-16">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                    <p class="text-xl text-gray-600">Everything you need to manage your finances effectively</p>
                </div>
                <div class="grid grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-8 rounded-2xl border border-green-200">
                        <div class="w-16 h-16 bg-gray-300 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-gray-500 text-sm">Icon</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Smart Tracking</h3>
                        <p class="text-gray-700 leading-relaxed">Automatically categorize and track your expenses with intelligent insights and detailed analytics.</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-100 p-8 rounded-2xl border border-blue-200">
                        <div class="w-16 h-16 bg-gray-300 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-gray-500 text-sm">Icon</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Secure Payments</h3>
                        <p class="text-gray-700 leading-relaxed">Send and receive money with bank-level security and encryption protecting every transaction.</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-8 rounded-2xl border border-purple-200">
                        <div class="w-16 h-16 bg-gray-300 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-gray-500 text-sm">Icon</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Smart Reports</h3>
                        <p class="text-gray-700 leading-relaxed">Generate comprehensive financial reports with visual charts and actionable insights.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- About Section -->
        <div id="about" class="bg-neutral-100 min-h-screen flex items-center px-8 py-16">
            <div class="max-w-6xl mx-auto grid grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-5xl font-bold text-gray-900 mb-8">About PesoPal</h2>
                    <p class="text-xl text-gray-700 leading-relaxed mb-8">
                        PesoPal was born from a simple idea: financial management should be accessible, secure, and effortless for everyone. 
                        We believe that understanding your money shouldn't require a degree in finance or hours of complicated setup.
                    </p>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8">
                        Our mission is to empower individuals and businesses to take control of their financial future through 
                        innovative tools, intelligent insights, and uncompromising security. Join thousands of users who trust 
                        PesoPal to manage their financial journey.
                    </p>
                    <div class="flex gap-8">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">10K+</div>
                            <div class="text-gray-600">Happy Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-green-600 mb-2">$2M+</div>
                            <div class="text-gray-600">Transactions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-purple-600 mb-2">99.9%</div>
                            <div class="text-gray-600">Uptime</div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-200 h-96 rounded-2xl flex items-center justify-center">
                    <span class="text-gray-500 text-lg">About Image Placeholder</span>
                </div>
            </div>
        </div>
    </main>

    <footer id="contact" class="bg-gray-900 text-gray-200 pt-12 pb-6 mt-0">
        <div class="container mx-auto px-8">
            <div class="flex flex-col gap-8 md:flex-row md:gap-0 justify-between items-start">
                <!-- Logo and description -->
                <div class="mb-8 md:mb-0 md:w-1/4">
                    <div class="flex items-center gap-3 mb-3">
                        <img :src="pesopal_logo" alt="PesoPal Logo" class="w-8 h-8">
                        <span class="font-extrabold text-xl">PesoPal</span>
                    </div>
                    <p class="text-gray-400 text-sm mb-4">Simplifying financial management for everyone.</p>
                    <div class="flex gap-3">
                        <a href="#" class="bg-gray-800 p-2 rounded-lg hover:bg-blue-600"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm3 7v4h2v-4h2l-3-3-3 3h2z"/></svg></a>
                        <a href="#" class="bg-gray-800 p-2 rounded-lg hover:bg-blue-600"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><rect width="16" height="16" x="2" y="2" rx="4"/></svg></a>
                        <a href="#" class="bg-gray-800 p-2 rounded-lg hover:bg-blue-600"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg></a>
                    </div>
                </div>
                <!-- Links -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 w-full md:w-3/4">
                    <div>
                        <h4 class="font-semibold mb-3">Product</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#features" class="hover:text-white">Features</a></li>
                            <li><a href="#" class="hover:text-white">Pricing</a></li>
                            <li><a href="#" class="hover:text-white">Security</a></li>
                            <li><a href="#" class="hover:text-white">API</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-3">Company</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#about" class="hover:text-white">About</a></li>
                            <li><a href="#" class="hover:text-white">Careers</a></li>
                            <li><a href="#" class="hover:text-white">Blog</a></li>
                            <li><a href="#" class="hover:text-white">Press</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-3">Support</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Help Center</a></li>
                            <li><a href="#contact" class="hover:text-white">Contact</a></li>
                            <li><a href="#" class="hover:text-white">Privacy</a></li>
                            <li><a href="#" class="hover:text-white">Terms</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-8 border-gray-800">
            <div class="text-center text-gray-400 text-sm">
                &copy; 2025 Pesopal. All rights reserved.
            </div>
        </div>
    </footer>
</template>

<script setup>
import pesopal_logo from '/src/assets/images/pesopal_logo.svg'
</script>

<style scoped>
</style>
