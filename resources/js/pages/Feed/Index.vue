<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    videos: Object,
});

const page = usePage();

/* FOLLOW */
const isFollowing = (userId) => {
    return page.props.auth.user?.following?.some(u => u.id === userId);
};

const toggleFollow = (userId) => {
    router.post(route('follow.toggle', userId), {}, {
        preserveScroll: true,
        preserveState: true
    });
};

/* LIKE */
const toggleLike = (video) => {
    if (video.is_liked) {
        video.likes_count--;
        video.is_liked = false;
    } else {
        video.likes_count++;
        video.is_liked = true;
    }

    router.post(route('videos.like', video.id), {}, {
        preserveScroll: true,
        preserveState: true
    });
};

/* SHARE */
const shareVideo = (video) => {
    const url = window.location.origin + '/player/' + video.user.id;

    if (navigator.share) {
        navigator.share({
            title: `Check out ${video.user.name} on ScoutMarket!`,
            text: video.title,
            url,
        }).catch(() => {});
    } else {
        navigator.clipboard.writeText(url);
        alert('Link copied to clipboard!');
    }
};

/* VIEWS */
const viewedVideos = new Set();

const registerView = (video) => {
    if (viewedVideos.has(video.id)) return;
    viewedVideos.add(video.id);
    axios.post(route('videos.view', video.id))
        .then(() => video.views_count++)
        .catch(() => {});
};
</script>

<template>
    <Head title="Video Feed" />

    <AppLayout title="Discover Talent">
        <div class="h-[calc(100vh-4rem)] w-full bg-black flex justify-center overflow-hidden">

            <div class="w-full h-full max-w-md bg-black relative shadow-2xl overflow-y-scroll snap-y snap-mandatory border-x border-white/10">

                <div
                    v-for="video in videos.data"
                    :key="video.id"
                    class="relative w-full h-full snap-start flex items-center justify-center bg-gray-900 border-b border-gray-800"
                >
                    <video
                        class="w-full h-full object-cover cursor-pointer"
                        :src="'/storage/' + video.video_url"
                        autoplay
                        muted
                        loop
                        playsinline
                        @click="$event.target.paused ? $event.target.play() : $event.target.pause()"
                        @play="registerView(video)"
                    />

                    <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/95 via-black/40 to-transparent p-4 pb-12 text-white pointer-events-none z-10">
                        
                        <div class="max-w-[75%] pointer-events-auto">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-11 w-11 shrink-0 rounded-full bg-gray-700 border-2 border-white/20 overflow-hidden shadow-lg">
                                    <img v-if="video.user.profile_photo_url" :src="video.user.profile_photo_url" class="h-full w-full object-cover" />
                                    <span v-else class="flex h-full w-full items-center justify-center font-bold bg-green-600">{{ video.user.name.charAt(0) }}</span>
                                </div>

                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2">
                                        <Link :href="route('player.show', video.user.id)" class="font-bold truncate hover:text-green-400 transition-colors">
                                            @{{ video.user.name }}
                                        </Link>
                                        
                                        <button
                                            v-if="page.props.auth.user && page.props.auth.user.id !== video.user.id"
                                            @click="toggleFollow(video.user.id)"
                                            class="text-[10px] uppercase tracking-wider font-black px-2 py-0.5 rounded border transition-all"
                                            :class="isFollowing(video.user.id) ? 'bg-white/10 border-white/20 text-white' : 'bg-green-600 border-green-600 text-white'"
                                        >
                                            {{ isFollowing(video.user.id) ? 'Following' : 'Follow' }}
                                        </button>
                                    </div>
                                    <span class="text-xs text-gray-300 font-medium">⚽ {{ video.user.profile?.position || 'Athlete' }}</span>
                                </div>
                            </div>

                            <p class="text-sm font-normal line-clamp-2 drop-shadow-md">
                                {{ video.title }}
                            </p>
                        </div>
                    </div>

                    <div class="absolute bottom-24 right-2 flex flex-col gap-6 items-center text-white z-20 pointer-events-auto">
                        
                        <div class="flex flex-col items-center gap-1">
                            <button
                                @click="toggleLike(video)"
                                class="p-3 rounded-full transition-all active:scale-75 shadow-lg bg-gray-800/40 backdrop-blur-sm"
                                :class="video.is_liked ? 'text-red-500' : 'text-white hover:text-red-400'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" :class="video.is_liked ? 'fill-current' : 'fill-none stroke-current stroke-2'" viewBox="0 0 24 24">
                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            <span class="text-[11px] font-bold shadow-sm">{{ video.likes_count }}</span>
                        </div>

                        <div class="flex flex-col items-center gap-1 text-white/90">
                            <div class="bg-gray-800/40 backdrop-blur-sm p-3 rounded-full shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold">{{ video.views_count }}</span>
                        </div>

                        <div class="flex flex-col items-center gap-1">
                            <button
                                @click="shareVideo(video)"
                                class="bg-gray-800/40 backdrop-blur-sm p-3 rounded-full hover:bg-green-600 transition-all shadow-lg"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                            </button>
                            <span class="text-[10px] font-black uppercase tracking-tighter">Share</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
::-webkit-scrollbar {
    display: none;
}
</style>