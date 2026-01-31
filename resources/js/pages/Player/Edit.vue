<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage, router } from '@inertiajs/vue3'; // Importamos router
import { computed } from 'vue';
import { route } from 'ziggy-js';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps({
    profile: Object,
    user: Object
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const isPlayer = computed(() => currentUser.value.role === 'player');
const isScout = computed(() => currentUser.value.role === 'scout');

// --- FORMULARIO 1: Datos Generales y Perfil ---
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    birth_date: props.profile?.birth_date || '',
    current_club: props.profile?.current_club || '', 
    position: props.profile?.position || '',
    height: props.profile?.height || '',
    weight: props.profile?.weight || '',
    dominant_foot: props.profile?.dominant_foot || 'Right',
});

// --- FORMULARIO 2: Contraseña ---
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitProfile = () => {
    form.patch(route('player.profile.update'), {
        preserveScroll: true,
    });
};

const submitPassword = () => {
    passwordForm.put(route('player.password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

// --- FUNCIÓN NUEVA: BORRAR VIDEO ---
const deleteVideo = (videoId) => {
    if (confirm('Are you sure you want to delete this video? This cannot be undone.')) {
        router.delete(route('videos.destroy', videoId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout title="Edit Profile">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center gap-2">
                <span v-if="isPlayer">⚽ Player Settings</span>
                <span v-else-if="isScout">🕵️‍♂️ Scout Settings</span>
                <span v-else>⚙️ User Settings</span>
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen space-y-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">👤 Account Information</h3>
                    <form @submit.prevent="submitProfile" class="grid gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label for="name">Full Name</Label>
                                <Input id="name" v-model="form.name" type="text" required />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="email">Email Address</Label>
                                <Input id="email" v-model="form.email" type="email" required />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mb-4">
                        {{ isPlayer ? '👟 Athletic Profile' : '💼 Professional Details' }}
                    </h3>

                    <form @submit.prevent="submitProfile" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label for="birth_date">Date of Birth</Label>
                                <Input id="birth_date" v-model="form.birth_date" type="date" required />
                                <InputError :message="form.errors.birth_date" />
                            </div>
                            
                            <div class="grid gap-2">
                                <Label for="current_club">
                                    {{ isPlayer ? 'Current Club' : 'Agency / Organization' }}
                                </Label>
                                <Input 
                                    id="current_club" 
                                    type="text" 
                                    v-model="form.current_club" 
                                    :placeholder="isPlayer ? 'e.g. Manchester City' : 'e.g. Global Talent Agency'"
                                />
                                <InputError :message="form.errors.current_club" />
                            </div>
                        </div>

                        <div v-if="isPlayer" class="space-y-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="grid gap-2">
                                <Label for="position">Primary Position</Label>
                                <select 
                                    id="position"
                                    v-model="form.position" 
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm dark:text-white dark:bg-gray-900"
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
                                <InputError :message="form.errors.position" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="grid gap-2">
                                    <Label for="height">Height (cm)</Label>
                                    <Input id="height" type="number" v-model="form.height" />
                                    <InputError :message="form.errors.height" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="weight">Weight (kg)</Label>
                                    <Input id="weight" type="number" v-model="form.weight" />
                                    <InputError :message="form.errors.weight" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="dominant_foot">Dominant Foot</Label>
                                    <select 
                                        id="dominant_foot"
                                        v-model="form.dominant_foot" 
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm dark:text-white dark:bg-gray-900"
                                    >
                                        <option value="Right">Right</option>
                                        <option value="Left">Left</option>
                                        <option value="Both">Both</option>
                                    </select>
                                    <InputError :message="form.errors.dominant_foot" />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <Button type="submit" :disabled="form.processing">
                                💾 Save Profile Info
                            </Button>
                        </div>
                    </form>
                </div>

                <div v-if="user.videos && user.videos.length > 0" class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🎬 Manage Videos</h3>
                    <div class="space-y-4">
                        <div v-for="video in user.videos" :key="video.id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-4">
                                <video :src="`/storage/${video.video_url}`" class="h-16 w-24 object-cover rounded bg-black"></video>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ video.title }}</p>
                                    <p class="text-xs text-gray-500">{{ new Date(video.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                            
                            <Button 
                                @click="deleteVideo(video.id)"
                                variant="destructive"
                                size="sm"
                            >
                                Delete 🗑️
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8 border border-red-100 dark:border-red-900/30">
                    <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4">🔒 Security</h3>
                    
                    <form @submit.prevent="submitPassword" class="space-y-6">
                        <div class="grid grid-cols-1 gap-4 max-w-xl">
                            <div class="grid gap-2">
                                <Label for="current_password">Current Password</Label>
                                <Input id="current_password" type="password" v-model="passwordForm.current_password" />
                                <InputError :message="passwordForm.errors.current_password" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="new_password">New Password</Label>
                                <Input id="new_password" type="password" v-model="passwordForm.password" />
                                <InputError :message="passwordForm.errors.password" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Confirm New Password</Label>
                                <Input id="password_confirmation" type="password" v-model="passwordForm.password_confirmation" />
                                <InputError :message="passwordForm.errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="flex justify-start">
                            <Button type="submit" variant="destructive" :disabled="passwordForm.processing">
                                🔑 Update Password
                            </Button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>