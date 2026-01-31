<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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


const photoInput = ref(null);
const photoPreview = ref(null);
const photoForm = useForm({
    photo: null,
});

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    photoForm.photo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submitPhoto = () => {
    if (photoForm.photo) {
        photoForm.post(route('profile.photo.update'), {
            preserveScroll: true,
            onSuccess: () => {
                photoPreview.value = null;
                clearPhotoInput();
            },
        });
    }
};

const clearPhotoInput = () => {
    if (photoInput.value) {
        photoInput.value.value = null;
    }
};

// --- LÓGICA FOTO DE PORTADA (COVER) ---
const coverInput = ref(null);
const coverPreview = ref(null);

const selectNewCover = () => {
    coverInput.value.click();
};

const updateCoverPreview = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    // Asignamos el archivo al formulario principal
    form.cover_photo = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        coverPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

// --- FORMULARIO PRINCIPAL (DATOS + PORTADA) ---
const form = useForm({
    _method: 'PATCH',
    name: props.user.name,
    email: props.user.email,
    cover_photo: null,
    birth_date: props.profile?.birth_date || '',
    current_club: props.profile?.current_club || '', 
    position: props.profile?.position || '',
    height: props.profile?.height || '',
    weight: props.profile?.weight || '',
    dominant_foot: props.profile?.dominant_foot || 'Right',
    location: props.profile?.location || '',
    availability_status: props.profile?.availability_status || 'Available',
});

const submitProfile = () => {
    form.post(route('player.profile.update'), {
        preserveScroll: true,
    });
};

// --- PASSWORD ---
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitPassword = () => {
    passwordForm.put(route('player.password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

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
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">🖼️ Cover Photo</h3>
                        <Button type="button" variant="outline" size="sm" @click="selectNewCover">
                            Change Cover
                        </Button>
                    </div>
                    
                    <div class="relative h-48 w-full rounded-xl overflow-hidden bg-gray-900 border border-gray-700 group cursor-pointer" @click="selectNewCover">
                        
                        <img v-if="coverPreview" :src="coverPreview" class="w-full h-full object-cover" />
                        
                        <img v-else-if="profile?.cover_url" :src="profile.cover_url" class="w-full h-full object-cover" />
                        
                        <div v-else class="w-full h-full bg-gradient-to-r from-green-900 to-gray-900 flex items-center justify-center">
                            <span class="text-gray-500 font-medium">No cover photo set</span>
                        </div>

                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-white font-bold">Click to upload</span>
                        </div>
                    </div>

                    <input 
                        type="file" 
                        ref="coverInput"
                        class="hidden"
                        @change="updateCoverPreview"
                        accept="image/*"
                    />
                    
                    <InputError :message="form.errors.cover_photo" class="mt-2" />
                    <p class="text-xs text-gray-500 mt-2">Recommended size: 1200x400px. Max 10MB.</p>

                    <div v-if="coverPreview" class="mt-4 flex justify-end">
                        <Button @click="submitProfile" :disabled="form.processing">
                            Upload & Save Cover
                        </Button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">📸 Profile Photo</h3>
                    
                    <form @submit.prevent="submitPhoto" class="flex flex-col sm:flex-row items-center gap-8">
                        <div class="shrink-0 relative group">
                            <img 
                                :src="photoPreview || currentUser.profile_photo_url" 
                                alt="Profile Photo" 
                                class="h-24 w-24 object-cover rounded-full border-4 border-gray-200 dark:border-gray-700 shadow-sm"
                            />
                        </div>

                        <div class="flex flex-col gap-4 w-full max-w-md">
                            <div class="grid gap-2">
                                <Label class="sr-only" for="photo">Select Photo</Label>
                                <input 
                                    type="file" 
                                    ref="photoInput"
                                    class="hidden"
                                    @change="updatePhotoPreview"
                                    accept="image/*"
                                />

                                <div class="flex gap-4">
                                    <Button type="button" variant="outline" @click="selectNewPhoto">
                                        Select New Photo
                                    </Button>
                                    
                                    <Button v-if="photoPreview" type="submit" :disabled="photoForm.processing">
                                        Save Photo
                                    </Button>
                                </div>
                                <InputError :message="photoForm.errors.photo" />
                                <p class="text-xs text-gray-500 mt-1">
                                    JPG, JPEG, PNG up to 5MB.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        
                        <div class="grid gap-2">
                            <Label for="location">📍 Location (City, Country)</Label>
                            <Input id="location" v-model="form.location" placeholder="e.g. Madrid, Spain" />
                            <InputError :message="form.errors.location" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="availability_status">📢 Availability Status</Label>
                            <select 
                                id="availability_status"
                                v-model="form.availability_status" 
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm dark:text-white dark:bg-gray-900"
                            >
                                <option value="Available">Available</option>
                                <option value="Looking for Club">Looking for Club</option>
                                <option value="Under Contract">Under Contract</option>
                                <option value="Injured">Injured</option>
                            </select>
                            <InputError :message="form.errors.availability_status" />
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