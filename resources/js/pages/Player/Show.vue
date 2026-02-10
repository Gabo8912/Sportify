<script setup>
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    player: Object
});

const page = usePage();
const currentUser = page.props.auth.user;

// MODIFICACIÓN 1: Cambiamos video_file por video_link
const form = useForm({
    title: '',
    video_link: '', 
});

const submitVideo = () => {
    form.post(route('videos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

// Helper para generar URL del embed de YouTube en la galería
const getYoutubeEmbed = (videoId) => {
    return `https://www.youtube.com/embed/${videoId}?controls=0&rel=0`;
};

// Birthday logic (Sin cambios)
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

// Message logic (Sin cambios)
const messageForm = useForm({
    receiver_id: props.player.id,
    body: ''
});
const showMessageInput = ref(false);

const sendMessage = () => {
    messageForm.post(route('messages.store'), {
        onSuccess: () => {
            messageForm.reset();
            showMessageInput.value = false;
            alert('Message sent!');
        }
    });
};
</script>

<template>
    <AppLayout title="Player Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Player Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="h-48 bg-gradient-to-r from-blue-600 to-indigo-700 w-full relative ">
                        <img v-if="player.profile?.cover_url" :src="player.profile.cover_url" class="w-full h-full object-cover rounded-t-lg" alt="Cover"/>
                        <div class="absolute -bottom-16 left-8 z-10">
                            <div class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden shadow-lg">
                                <img v-if="player.profile_photo_url" :src="player.profile_photo_url" class="h-full w-full object-cover"/>
                                <span v-else class="text-4xl font-bold text-gray-500 dark:text-gray-300">{{ player.name.charAt(0) }}</span>
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
                                <Link :href="route('player.profile.edit')" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">✏️ Edit Profile</Link>
                            </div>
                        </div>

                        <div v-if="currentUser && currentUser.id !== player.id" class="mt-4">
                            <button @click="showMessageInput = !showMessageInput" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-all active:scale-95 font-bold text-sm">
                                <span>{{ showMessageInput ? '✕ Close' : '✉️ Contact Player' }}</span>
                            </button>
                            <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0">
                                <div v-if="showMessageInput" class="mt-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Send a private message</label>
                                    <textarea v-model="messageForm.body" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm" rows="3" placeholder="Hi, I'm a scout from..."></textarea>
                                    <div class="flex justify-end mt-3">
                                        <button @click="sendMessage" :disabled="messageForm.processing || !messageForm.body.trim()" class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-sm">
                                            <span v-if="messageForm.processing">Sending...</span>
                                            <span v-else>Send Message</span>
                                        </button>
                                    </div>
                                </div>
                            </transition>
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
                            </div>

                            <div v-if="currentUser && currentUser.id === player.id" class="mb-8 p-4 bg-indigo-50 dark:bg-gray-700 rounded-lg border border-indigo-100 dark:border-gray-600">
                                <h4 class="font-bold text-indigo-700 dark:text-indigo-300 mb-3">Add New Highlight (YouTube Short)</h4>
                                
                                <form @submit.prevent="submitVideo" class="flex flex-col gap-4">
                                    <div class="flex flex-col md:flex-row gap-4 items-end">
                                        <div class="w-full md:w-1/3">
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Title</label>
                                            <input v-model="form.title" type="text" placeholder="Gol vs Team X..." class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-2 text-sm" required />
                                        </div>
                                        
                                        <div class="w-full md:w-2/3">
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">YouTube Link</label>
                                            <input 
                                                v-model="form.video_link" 
                                                type="url" 
                                                placeholder="https://youtube.com/shorts/..." 
                                                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-2 text-sm" 
                                                required 
                                            />
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded w-full md:w-auto disabled:opacity-50">
                                            {{ form.processing ? 'Publishing...' : 'Add Link' }}
                                        </button>
                                    </div>
                                </form>
                                <div v-if="form.errors.video_link" class="text-red-500 text-xs mt-2">{{ form.errors.video_link }}</div>
                            </div>

                            <div v-if="!player.videos || player.videos.length === 0" class="text-center py-12 bg-gray-50 dark:bg-gray-750 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <p class="text-gray-500 dark:text-gray-400 mb-2">No highlights added yet.</p>
                            </div>

                            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="video in player.videos" :key="video.id" class="bg-black rounded-xl overflow-hidden shadow-lg group relative aspect-[9/16]">
                                    
                                    <iframe 
                                        v-if="video.platform === 'youtube'"
                                        class="w-full h-full object-cover"
                                        :src="getYoutubeEmbed(video.external_video_id)" 
                                        frameborder="0" 
                                        allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen
                                    ></iframe>

                                    <video 
                                        v-else-if="video.platform === 'local'"
                                        controls 
                                        class="w-full h-full object-cover"
                                    >
                                        <source :src="`/storage/${video.video_url}`" type="video/mp4">
                                    </video>

                                    <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/80 to-transparent pointer-events-none">
                                        <h4 class="font-bold text-white text-sm truncate">{{ video.title }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-6 text-center">
                    <Link href="/feed" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">← Go Home</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>