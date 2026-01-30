<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js'; // Importamos route para los enlaces

const props = defineProps({
    player: Object
});

// Detectar si hay usuario logueado o es un visitante
const page = usePage();
const currentUser = page.props.auth.user;

const getAge = (birthDate) => {
    if (!birthDate) return 'N/A';
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans text-gray-900 antialiased">
        
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link href="/" class="font-bold text-xl text-indigo-600">
                            ⚽ ScoutApp
                        </Link>
                    </div>
                    <div class="flex items-center gap-4">
                        <div v-if="currentUser">
                            <Link href="/dashboard" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600">
                                Dashboard
                            </Link>
                        </div>
                        <div v-else class="flex gap-4">
                            <Link :href="route('login')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600">
                                Log in
                            </Link>
                            <Link :href="route('register')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600">
                                Register
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    
                    <div class="h-48 bg-gradient-to-r from-blue-600 to-indigo-700 w-full relative">
                        <div class="absolute -bottom-16 left-8">
                            <div class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden shadow-lg">
                                <span class="text-4xl font-bold text-gray-500 dark:text-gray-300">
                                    {{ player.name.charAt(0) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-20 px-8 pb-8">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ player.name }}</h1>
                                <p class="text-lg text-gray-600 dark:text-gray-400 flex items-center gap-2 mt-1">
                                    ⚽ {{ player.profile?.position || 'Unknown Position' }}
                                </p>
                            </div>
                            
                            <div v-if="currentUser && currentUser.id === player.id">
                                <Link :href="route('player.profile.edit')" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">
                                    ✏️ Edit Profile
                                </Link>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-b border-gray-200 dark:border-gray-700 py-6">
                            <div class="text-center">
                                <span class="block text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Club</span>
                                <span class="block font-bold text-lg text-gray-800 dark:text-white mt-1">{{ player.profile?.current_club || 'Free Agent' }}</span>
                            </div>
                            <div class="text-center border-l border-gray-200 dark:border-gray-700">
                                <span class="block text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Height</span>
                                <span class="block font-bold text-lg text-gray-800 dark:text-white mt-1">{{ player.profile?.height ? player.profile.height + ' cm' : '--' }}</span>
                            </div>
                            <div class="text-center border-l border-gray-200 dark:border-gray-700">
                                <span class="block text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Foot</span>
                                <span class="block font-bold text-lg text-gray-800 dark:text-white mt-1">{{ player.profile?.dominant_foot || '--' }}</span>
                            </div>
                            <div class="text-center border-l border-gray-200 dark:border-gray-700">
                                <span class="block text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Age</span>
                                <span class="block font-bold text-lg text-gray-800 dark:text-white mt-1">{{ getAge(player.profile?.birth_date) }} years</span>
                            </div>
                        </div>

                        <div class="mt-10">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    📹 Video Highlights
                                </h3>
                                
                                <Link 
                                    v-if="currentUser && currentUser.id === player.id" 
                                    :href="route('videos.create')"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow transition"
                                >
                                    + Upload Highlight
                                </Link>
                            </div>

                            <div v-if="!player.videos || player.videos.length === 0" class="text-center py-12 bg-gray-50 dark:bg-gray-750 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <p class="text-gray-500 dark:text-gray-400 mb-2">No highlights uploaded yet.</p>
                                <p class="text-sm text-gray-400">Upload your best plays to show scouts what you can do.</p>
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-for="video in player.videos" :key="video.id" class="bg-black rounded-xl overflow-hidden shadow-lg group">
                                    <video 
                                        controls 
                                        class="w-full aspect-video object-cover"
                                        preload="metadata"
                                    >
                                        <source :src="`/storage/${video.video_url}`" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    
                                    <div class="p-4 bg-white dark:bg-gray-700">
                                        <h4 class="font-bold text-gray-900 dark:text-white truncate" :title="video.title">
                                            {{ video.title }}
                                        </h4>
                                        <div class="flex justify-between items-center mt-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                📅 {{ new Date(video.created_at).toLocaleDateString() }}
                                            </span>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>

                <div class="mt-6 text-center">
                    <Link href="/" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                        ← Go Home
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>