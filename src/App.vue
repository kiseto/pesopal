<template>
<!--Navbar-->
    <header class="sticky top-0 z-99">
        <div class="bg-white p-4 border-b-neutral-100 border-b-1">
            <div class="container mx-auto flex flex-row items-center justify-between">
        
                <!--Logo-->
                <div class="flex flex-row">
                    <img src="" alt="">
                    <router-link class="font-extrabold  cursor-pointer text-self-blue text-4xl" to="/">PesoPal</router-link>
                </div>
                <!--Navbar links-->
                <nav class="flex flex-row justify-center items-center">
                    <ul class="flex flex-row gap-x-12">
                        <li><router-link class="text-gray-700  cursor-pointer hover:text-neutral-900 ease-in-out duration-200 transition" to="/">Home</router-link></li>
                        <li><a class="text-gray-700 cursor-pointer hover:text-neutral-900 ease-in-out duration-200 transition" href="#features">Features</a></li>
                        <li><a class="text-gray-700 cursor-pointer  hover:text-neutral-900 ease-in-out duration-200 transition" href="#about">About</a></li>
                        <li><a class="text-gray-700 cursor-pointer  hover:text-neutral-900 ease-in-out duration-200 transition" href="#contact">Contact</a></li>
                    </ul>
                </nav>
        
                <!--Login and Get Started buttons-->
                <div class="flex flex-row items-center gap-4">
                    <router-link
                      to="/login"
                      class="cursor-pointer text-gray-700 hover:text-blue-600 font-semibold transition duration-200 ease-in-out"
                    >
                      Login
                    </router-link>
                    <router-link
                      to="/register"
                      class="cursor-pointer bg-self-blue text-white py-3 px-3 rounded-lg font-semibold hover:bg-blue-800 transition duration-200 ease-in-out"
                    >
                      Get Started
                    </router-link>
                </div>
            </div>
        </div>
    </header>
    <router-view />
</template>

<script setup>
</script>
