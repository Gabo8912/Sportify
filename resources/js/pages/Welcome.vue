<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome to ScoutApp" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white selection:bg-indigo-500 selection:text-white">
        
        <div v-if="canLogin" class="absolute top-0 right-0 p-6 text-right z-10 w-full flex justify-between items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tighter">
                ⚽ ScoutApp
            </div>

            <div>
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">
                    Dashboard
                </Link>

                <template v-else>
                    <Link :href="route('login')" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500 mr-4">
                        Log in
                    </Link>

                    <Link v-if="canRegister" :href="route('register')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-semibold transition">
                        Register
                    </Link>
                </template>
            </div>
        </div>

        <div class="relative flex items-center justify-center min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-gray-900 to-black z-0"></div>
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] z-0"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-white mb-6">
                    Showcase Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">Talent.</span>
                </h1>
                <p class="mt-4 text-xl text-gray-300 max-w-2xl mx-auto mb-10">
                    The professional platform for football players to upload highlights, track stats, and get discovered by scouts worldwide.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link 
                        v-if="!$page.props.auth.user"
                        :href="route('register')" 
                        class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-full text-lg shadow-lg hover:shadow-indigo-500/50 transition transform hover:-translate-y-1"
                    >
                        Start Your Career 🚀
                    </Link>
                     <Link 
                        v-else
                        :href="route('dashboard')" 
                        class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-full text-lg shadow-lg hover:shadow-indigo-500/50 transition transform hover:-translate-y-1"
                    >
                        Go to Dashboard 🚀
                    </Link>
                    
                    </div>
            </div>
        </div>

        <div class="py-24 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">How It Works</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Simple steps to get scouted.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="text-center p-6 rounded-xl bg-gray-50 dark:bg-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                            📝
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Create Profile</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Set up your professional player card with your position, age, height, and club history.
                        </p>
                    </div>

                    <div class="text-center p-6 rounded-xl bg-gray-50 dark:bg-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                            📹
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Upload Highlights</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Share your best goals, assists, and skills directly. Show, don't just tell.
                        </p>
                    </div>

                    <div class="text-center p-6 rounded-xl bg-gray-50 dark:bg-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                            👀
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Get Scouted</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Your profile appears on the global dashboard where scouts are looking for new talent.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="bg-gray-100 dark:bg-gray-900 py-8 border-t border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                <p>&copy; 2024 ScoutApp. All rights reserved.</p>
                <p class="mt-2">Built with Laravel & Vue.</p>
            </div>
        </footer>
    </div>
</template>