<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    players: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const position = ref(props.filters.position || '');
const foot = ref(props.filters.foot || '');
const club = ref(props.filters.club || '');
const location = ref(props.filters.location || '');
const availability = ref(props.filters.availability || '');
const ageMin = ref(props.filters.age_min || '');
const ageMax = ref(props.filters.age_max || '');

const updateSearch = debounce(() => {
    router.get(route('dashboard'), {
        search: search.value,
        position: position.value,
        foot: foot.value,
        club: club.value,
        location: location.value,
        availability: availability.value,
        age_min: ageMin.value,
        age_max: ageMax.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch([search, position, foot, club, location, availability, ageMin, ageMax], updateSearch);

const positions = [
    'Goalkeeper',
    'Center Back',
    'Right Back',
    'Left Back',
    'Defensive Midfielder',
    'Attacking Midfielder',
    'Winger',
    'Striker'
];

const calculateAge = (dateString) => {
    if (!dateString) return '--';
    
    const today = new Date();
    const birthDate = new Date(dateString);
    
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return isNaN(age) ? '--' : age;
};
</script>

<template>
    <AppLayout title="Scouting Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Talent Market
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                
                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6 mb-8 border border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-400 uppercase font-bold">Search Player</label>
                            <input v-model="search" type="text" placeholder="Player name..." class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-green-500">
                        </div>

                        <div>
                            <label class="text-xs text-gray-400 uppercase font-bold">Current Club</label>
                            <input v-model="club" type="text" placeholder="Club name..." class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-green-500">
                        </div>

                        <div>
                            <label class="text-xs text-gray-400 uppercase font-bold">Location</label>
                            <input v-model="location" type="text" placeholder="City or Country..." class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-green-500">
                        </div>

                        <div>
                            <label class="text-xs text-gray-400 uppercase font-bold">Position</label>
                            <select v-model="position" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg">
                                <option value="">All Positions</option>
                                <option v-for="p in positions" :key="p" :value="p">{{ p }}</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-1/2">
                                <label class="text-xs text-gray-400 uppercase font-bold">Min Age</label>
                                <input v-model="ageMin" type="number" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg">
                            </div>
                            <div class="w-1/2">
                                <label class="text-xs text-gray-400 uppercase font-bold">Max Age</label>
                                <input v-model="ageMax" type="number" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-400 uppercase font-bold">Status</label>
                            <select v-model="availability" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg">
                                <option value="">Any Status</option>
                                <option value="Available">Available</option>
                                <option value="Looking for Club">Looking for Club</option>
                                <option value="Under Contract">Under Contract</option>
                                <option value="Injured">Injured</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-400 uppercase font-bold">Foot</label>
                            <select v-model="foot" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-lg">
                                <option value="">Any Foot</option>
                                <option value="Right">Right</option>
                                <option value="Left">Left</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                    </div>
                </div>
                

                <div v-if="players.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <Link 
                        v-for="user in players.data" 
                        :key="user.id" 
                        :href="route('player.show', user.id)"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 overflow-hidden group border border-gray-700 flex flex-col"
                    >
                        <div class="relative h-48 bg-gradient-to-br from-gray-700 to-gray-900">
                            <img v-if="user.profile_photo_url" :src="user.profile_photo_url" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition duration-500">
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <span class="text-6xl text-gray-600 font-bold">{{ user.name.charAt(0) }}</span>
                            </div>
                            
                            <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/90 to-transparent p-4">
                                <h3 class="text-xl font-bold text-white truncate">{{ user.name }}</h3>
                                <p class="text-green-400 text-sm font-semibold uppercase tracking-wider">
                                    {{ user.profile?.position || 'Unlisted' }}
                                </p>
                            </div>
                        </div>

                        <div class="p-4 grid grid-cols-3 gap-4 text-center border-b border-gray-700 bg-gray-800/50">
                            <div>
                                <div class="text-xs text-gray-400 uppercase font-bold">Age</div>
                                <div class="text-lg font-bold text-white">{{ calculateAge(user.profile?.birth_date) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 uppercase font-bold">Height</div>
                                <div class="text-lg font-bold text-white">{{ user.profile?.height ? user.profile.height + 'm' : '--' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 uppercase font-bold">Foot</div>
                                <div class="text-lg font-bold text-white">{{ user.profile?.dominant_foot || '--' }}</div>
                            </div>
                        </div>

                        <div class="p-4 flex justify-between items-center bg-gray-800">
                            <div class="flex items-center text-sm text-gray-400 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                {{ user.profile?.current_club || 'Free Agent' }}
                            </div>
                            
                            <div class="flex items-center gap-1 text-xs font-bold bg-gray-700 px-2 py-1 rounded-full text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ user.videos_count || 0 }}
                            </div>
                        </div>
                    </Link>

                </div>

                <div v-else class="text-center py-20 bg-gray-800 rounded-lg border border-dashed border-gray-600">
                    <p class="text-gray-400 text-lg">No players found matching your filters.</p>
                    <button @click="search=''; position=''; foot=''" class="mt-2 text-green-400 font-bold underline">
                        Clear all filters
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>