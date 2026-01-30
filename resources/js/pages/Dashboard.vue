<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    players: Array
});

// Función auxiliar para obtener iniciales si no hay foto
const getInitials = (name) => {
    return name ? name.charAt(0).toUpperCase() : '?';
};
</script>

<template>
    <AppLayout title="Scouting Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ⚽ Player Market
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="players.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-lg">
                        No active players found yet.
                    </p>
                    <Link :href="route('player.profile.edit')" class="mt-4 inline-block text-indigo-500 hover:underline">
                        Create your profile to appear here
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <div v-for="player in players" :key="player.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                        
                        <div class="h-24 bg-gradient-to-r from-blue-500 to-indigo-600 relative">
                            <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2">
                                <div class="h-16 w-16 rounded-full border-4 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xl font-bold text-gray-500 dark:text-gray-300 shadow-md">
                                    {{ getInitials(player.name) }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 pb-6 px-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                                {{ player.name }}
                            </h3>
                            
                            <div class="flex justify-center gap-2 mt-3 flex-wrap">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded-full">
                                    {{ player.profile?.position || 'N/A' }}
                                </span>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs font-semibold rounded-full">
                                    {{ player.profile?.current_club || 'Free Agent' }}
                                </span>
                            </div>

                            <div class="mt-4 flex justify-center gap-6 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ player.profile?.dominant_foot || '-' }}</span>
                                    <span class="text-xs">Foot</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ player.profile?.height ? player.profile.height + 'cm' : '-' }}</span>
                                    <span class="text-xs">Height</span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <Link 
                                    :href="route('player.show', player.id)" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors"
                                >
                                    View Full Profile & Videos →
                                </Link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>