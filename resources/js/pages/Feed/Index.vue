<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Props passed from Laravel Controller
const props = defineProps({
    videos: Object,
});
</script>

<template>
    <Head title="Video Feed" />

    <AppLayout title="Discover Talent">
        <div class="h-[calc(100vh-4rem)] w-full overflow-y-scroll snap-y snap-mandatory bg-black">
            
            <div class="flex justify-center w-full">
                <div class="w-full max-w-md bg-black">

                    <div 
                        v-for="video in videos.data" 
                        :key="video.id"
                        class="relative w-full h-[calc(100vh-4rem)] snap-start flex items-center justify-center bg-gray-900 border-b border-gray-800"
                    >
                        <video 
                            class="w-full h-full object-cover cursor-pointer"
                            :src="'/storage/' + video.video_url"
                            autoplay
                            muted
                            loop
                            playsinline
                            onclick="this.paused ? this.play() : this.pause()"
                        ></video>

                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/90 via-black/40 to-transparent p-4 pb-12 text-white">
                            
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 rounded-full bg-gray-700 overflow-hidden border border-white/50 flex items-center justify-center">
                                    <span class="font-bold text-sm">{{ video.user.name.charAt(0) }}</span>
                                </div>
                                
                                <div class="flex flex-col shadow-black drop-shadow-md">
                                    <Link :href="route('player.show', video.user.id)" class="font-bold text-base hover:underline">
                                        @{{ video.user.name }}
                                    </Link>
                                    <span class="text-xs text-gray-200 flex items-center gap-1">
                                        <span v-if="video.user.profile?.position" class="font-semibold text-yellow-400">
                                            {{ video.user.profile.position }}
                                        </span>
                                        <span v-else>Player</span>
                                        <span v-if="video.user.profile?.height">
                                            • {{ video.user.profile.height }}cm
                                        </span>
                                    </span>
                                </div>
                                
                                <button class="ml-auto border border-white/60 text-xs px-4 py-1.5 rounded-full hover:bg-white hover:text-black transition font-semibold">
                                    Follow
                                </button>
                            </div>

                            <p class="text-sm mb-2 font-light drop-shadow-md">
                                {{ video.title }}
                            </p>
                        </div>

                        <div class="absolute bottom-24 right-2 flex flex-col gap-6 items-center text-white z-10">
                            
                            <div class="flex flex-col items-center gap-1 group">
                                <div class="bg-gray-800/60 p-3 rounded-full hover:bg-pink-600 transition cursor-pointer backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold drop-shadow-md">1.2k</span>
                            </div>

                            <div class="flex flex-col items-center gap-1 group">
                                <div class="bg-gray-800/60 p-3 rounded-full hover:bg-blue-500 transition cursor-pointer backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 8 9 8z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold drop-shadow-md">340</span>
                            </div>

                            <div class="flex flex-col items-center gap-1 group">
                                <div class="bg-gray-800/60 p-3 rounded-full hover:bg-green-500 transition cursor-pointer backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold drop-shadow-md">Share</span>
                            </div>

                        </div>

                    </div>
                    <div v-if="videos.data.length === 0" class="h-screen flex flex-col items-center justify-center text-white bg-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <p class="text-lg font-semibold">No videos yet 😔</p>
                        <p class="text-sm text-gray-400">Be the first to show your talent!</p>
                        <Link :href="route('videos.store')" class="mt-6 px-6 py-2 bg-blue-600 rounded-full text-white font-bold hover:bg-blue-500 transition">
                            Upload Video
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>