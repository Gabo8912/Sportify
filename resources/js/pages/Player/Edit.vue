<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
// 1. CORRECCIÓN: Importamos 'route' para usarlo en el submit
import { route } from 'ziggy-js'; 

const props = defineProps({
    profile: Object,
    user: Object
});

const form = useForm({
    // 2. BIEN HECHO: Esto soluciona el error de SQL
    birth_date: props.profile?.birth_date || '', 
    position: props.profile?.position || '',
    height: props.profile?.height || '',
    weight: props.profile?.weight || '',
    current_club: props.profile?.current_club || '',
    dominant_foot: props.profile?.dominant_foot || 'Right',
});

const submit = () => {
    form.patch(route('player.profile.update'), {
        preserveScroll: true,
        onSuccess: () => alert('✅ Profile updated successfully'),
    });
};
</script>

<template>
    <AppLayout title="Edit Profile">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                ⚽ Player Profile
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    
                    <div class="p-8">
                        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Player Stats</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">This information will be public for Scouts and Clubs.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div>
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Primary Position</label>
                                <select 
                                    v-model="form.position" 
                                    class="w-full border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 dark:text-white py-2 px-3"
                                >
                                    <option value="" disabled>Select your position</option>
                                    <option value="Goalkeeper">Goalkeeper</option>
                                    <option value="Center Back">Center Back</option>
                                    <option value="Right Back">Right Back</option>
                                    <option value="Left Back">Left Back</option>
                                    <option value="Defensive Midfielder">Defensive Midfielder</option>
                                    <option value="Attacking Midfielder">Attacking Midfielder</option>
                                    <option value="Winger">Winger</option>
                                    <option value="Striker">Striker</option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Date of Birth</label>
                                <input
                                    id="birth_date"
                                    v-model="form.birth_date"
                                    type="date"
                                    class="w-full border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 dark:text-white py-2 px-3"
                                    required
                                />
                                <div v-if="form.errors.birth_date" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.birth_date }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Height (cm)</label>
                                    <input 
                                        type="number" 
                                        v-model="form.height" 
                                        class="w-full border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 dark:text-white py-2 px-3"
                                        placeholder="e.g. 180"
                                    >
                                </div>
                                <div>
                                    <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Weight (kg)</label>
                                    <input 
                                        type="number" 
                                        v-model="form.weight" 
                                        class="w-full border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 dark:text-white py-2 px-3"
                                        placeholder="e.g. 75"
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Current Club</label>
                                    <input 
                                        type="text" 
                                        v-model="form.current_club" 
                                        class="w-full border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 dark:text-white py-2 px-3"
                                        placeholder="e.g. Real Madrid or Free Agent"
                                    >
                                </div>
                                <div>
                                    <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Dominant Foot</label>
                                    <select 
                                        v-model="form.dominant_foot" 
                                        class="w-full border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 dark:text-white py-2 px-3"
                                    >
                                        <option value="Right">Right</option>
                                        <option value="Left">Left</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button 
                                    type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-150 ease-in-out transform hover:scale-105"
                                    :disabled="form.processing"
                                >
                                    💾 Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>