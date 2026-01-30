<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

// Recibimos la lista de jugadores desde el backend (Laravel)
defineProps({
    players: {
        type: Array,
        default: () => [],
    }
});

// Configuración para que el Header sepa dónde estamos
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Player Market', href: '#' },
];

// Función auxiliar para mostrar iniciales si no hay foto
const getInitials = (name) => {
    return name ? name.charAt(0).toUpperCase() : '?';
};
</script>

<template>
    <AppLayout title="Dashboard" :breadcrumbs="breadcrumbs">
        
        <div class="flex flex-col gap-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        ⚽ Player Market
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Browse active players, check their stats, and watch their highlights.
                    </p>
                </div>
            </div>

            <div v-if="players.length === 0" class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                <div class="p-4 rounded-full bg-indigo-50 dark:bg-indigo-900/20 mb-3 text-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">No active players found</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm mt-1 mb-4">
                    The market is currently empty.
                </p>
                <Link :href="route('player.profile.edit')" class="text-indigo-600 hover:text-indigo-500 font-semibold hover:underline">
                    Create your Profile to appear here &rarr;
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                <div v-for="player in players" :key="player.id" class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl border border-gray-200 dark:border-gray-700 transition-all duration-300 overflow-hidden flex flex-col">
                    
                    <div class="h-24 bg-gradient-to-br from-blue-600 to-indigo-700 relative">
                        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2">
                            <div class="h-16 w-16 rounded-full border-4 border-white dark:border-gray-800 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xl font-bold text-gray-600 dark:text-gray-300 shadow-md">
                                {{ getInitials(player.name) }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 pb-6 px-6 text-center flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate" :title="player.name">
                            {{ player.name }}
                        </h3>
                        
                        <div class="flex justify-center gap-2 mt-3 flex-wrap">
                            <span class="px-2.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded-full border border-blue-200 dark:border-blue-800">
                                {{ player.profile?.position || 'N/A' }}
                            </span>
                            <span class="px-2.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full border border-gray-200 dark:border-gray-600">
                                {{ player.profile?.current_club || 'Free Agent' }}
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ player.profile?.dominant_foot || '-' }}
                                </span>
                                <span class="text-[10px] uppercase text-gray-500 font-medium tracking-wider">Foot</span>
                            </div>
                            <div class="flex flex-col border-l border-gray-100 dark:border-gray-700">
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ player.profile?.height ? player.profile.height + 'cm' : '-' }}
                                </span>
                                <span class="text-[10px] uppercase text-gray-500 font-medium tracking-wider">Height</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-2">
                            <Link 
                                :href="route().has('player.show') ? route('player.show', player.id) : '#'"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-900 dark:bg-indigo-600 hover:bg-gray-800 dark:hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                View Profile & Videos
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>