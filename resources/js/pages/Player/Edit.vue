<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3'; // Importamos usePage para detectar el rol
import { computed } from 'vue';
import { route } from 'ziggy-js'; 

// Importamos componentes de UI para mantener el diseño consistente (Shadcn)
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps({
    profile: Object,
    user: Object
});

// 1. Detectar el ROL del usuario actual
const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const isPlayer = computed(() => currentUser.value.role === 'player');
const isScout = computed(() => currentUser.value.role === 'scout');

// 2. Formulario Inteligente (Inicializa datos según lo que exista)
const form = useForm({
    // Campos Comunes
    birth_date: props.profile?.birth_date || '', 
    
    // Campos de JUGADOR (Solo se llenan si existen)
    position: props.profile?.position || '',
    height: props.profile?.height || '',
    weight: props.profile?.weight || '',
    current_club: props.profile?.current_club || '',
    dominant_foot: props.profile?.dominant_foot || 'Right',

    // Campos de SCOUT (Ejemplos futuros)
    agency_name: props.profile?.agency_name || '',
    license_number: props.profile?.license_number || '',
});

const submit = () => {
    // Aquí podrías cambiar la ruta si el Scout tiene un endpoint diferente,
    // pero por ahora usaremos la misma y el controlador filtrará los datos.
    form.patch(route('player.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Opcional: Toast notification aquí
            console.log('Profile updated');
        },
    });
};
</script>

<template>
    <AppLayout title="Edit Profile">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <span v-if="isPlayer">⚽ Player Profile</span>
                <span v-else-if="isScout">🕵️‍♂️ Scout Profile</span>
                <span v-else>👤 User Profile</span>
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    
                    <div class="p-8">
                        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                {{ isPlayer ? 'Physical & Technical Stats' : 'Professional Details' }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                This information will be visible to {{ isPlayer ? 'Scouts and Clubs' : 'Players looking for representation' }}.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">

                            <div class="grid gap-2">
                                <Label for="birth_date">Date of Birth</Label>
                                <Input
                                    id="birth_date"
                                    v-model="form.birth_date"
                                    type="date"
                                    required
                                />
                                <InputError :message="form.errors.birth_date" />
                            </div>

                            <div v-if="isPlayer" class="space-y-6">
                                <div class="grid gap-2">
                                    <Label for="position">Primary Position</Label>
                                    <div class="relative">
                                        <select 
                                            id="position"
                                            v-model="form.position" 
                                            class="flex h-9 w-full appearance-none rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white"
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
                                    <InputError :message="form.errors.position" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="grid gap-2">
                                        <Label for="height">Height (cm)</Label>
                                        <Input 
                                            id="height"
                                            type="number" 
                                            v-model="form.height" 
                                            placeholder="e.g. 180"
                                        />
                                        <InputError :message="form.errors.height" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="weight">Weight (kg)</Label>
                                        <Input 
                                            id="weight"
                                            type="number" 
                                            v-model="form.weight" 
                                            placeholder="e.g. 75"
                                        />
                                        <InputError :message="form.errors.weight" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="grid gap-2">
                                        <Label for="current_club">Current Club</Label>
                                        <Input 
                                            id="current_club"
                                            type="text" 
                                            v-model="form.current_club" 
                                            placeholder="e.g. Real Madrid or Free Agent"
                                        />
                                        <InputError :message="form.errors.current_club" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="dominant_foot">Dominant Foot</Label>
                                        <div class="relative">
                                            <select 
                                                id="dominant_foot"
                                                v-model="form.dominant_foot" 
                                                class="flex h-9 w-full appearance-none rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white"
                                            >
                                                <option value="Right">Right</option>
                                                <option value="Left">Left</option>
                                                <option value="Both">Both</option>
                                            </select>
                                        </div>
                                        <InputError :message="form.errors.dominant_foot" />
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="isScout" class="space-y-6">
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-md border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm font-medium">
                                        ℹ️ As a Scout, you don't need to fill out physical stats.
                                    </p>
                                    <p class="text-sm mt-1">
                                        Soon you will be able to add your Agency Name and License Number here.
                                    </p>
                                </div>
                                
                                </div>

                            <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <Button 
                                    type="submit" 
                                    class="w-full sm:w-auto"
                                    :disabled="form.processing"
                                >
                                    💾 Save Changes
                                </Button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>