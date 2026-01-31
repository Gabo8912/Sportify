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

const form = useForm({
    title: '',
    video_file: null,
});

//Video
const submitVideo = () => {
    form.post(route('videos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
//Birthday
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

//Message
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

                        <div v-if="currentUser && currentUser.id !== player.id" class="mt-4">
        
        <button 
            @click="showMessageInput = !showMessageInput"
            class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
        >
            ✉️ Contact Player
        </button>

        <div v-if="showMessageInput" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm transition-all">
    
    <label for="message_body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Write your message to {{ player.name }}
    </label>

    <textarea 
        id="message_body"
        v-model="messageForm.body" 
        rows="4"
        class="w-full rounded-md border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 dark:placeholder-gray-500"
        placeholder="Hi, I represents a club and would like to offer you a trial..."
    ></textarea>
    
    <div v-if="messageForm.errors.body" class="text-red-500 text-xs mt-1">
        {{ messageForm.errors.body }}
    </div>
    
    <div class="flex justify-end items-center gap-3 mt-3">
        <button 
            @click="showMessageInput = false"
            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 underline"
        >
            Cancel
        </button>

        <button 
            @click="sendMessage" 
            :disabled="messageForm.processing"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
        >
            <span v-if="messageForm.processing">Sending...</span>
            <span v-else>Send Message 🚀</span>
        </button>
    </div>
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
                            </div>

                            <div v-if="currentUser && currentUser.id === player.id" class="mb-8 p-4 bg-indigo-50 dark:bg-gray-700 rounded-lg border border-indigo-100 dark:border-gray-600">
                                <h4 class="font-bold text-indigo-700 dark:text-indigo-300 mb-3">Upload New Highlight</h4>
                                <form @submit.prevent="submitVideo" class="flex flex-col md:flex-row gap-4 items-end">
                                    <div class="w-full md:w-1/3">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Title</label>
                                        <input v-model="form.title" type="text" placeholder="Gol vs Team X..." class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 p-2 text-sm" required />
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Video File</label>
                                        <input type="file" @input="form.video_file = $event.target.files[0]" accept="video/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                                    </div>
                                    <div class="w-full md:w-auto">
                                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full md:w-auto disabled:opacity-50">
                                            {{ form.processing ? 'Uploading...' : 'Upload Video' }}
                                        </button>
                                    </div>
                                </form>
                                <div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                                </div>
                                <div v-if="form.errors.video_file" class="text-red-500 text-xs mt-2">{{ form.errors.video_file }}</div>
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
                    <Link href="/feed" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                        ← Go Home
                    </Link>
                </div>

            </div>
        </div>
    </AppLayout>
</template>