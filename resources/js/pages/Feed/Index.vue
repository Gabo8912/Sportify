<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    videos: Object,
});

const page = usePage();


// Infinite Scroll Data
const allVideos = ref([...props.videos.data]); 
const nextCursor = ref(props.videos.next_cursor);
const isLoadingMore = ref(false);
const loadTriggerRef = ref(null); 

const visibleIndex = ref(0); 

const shouldLoad = (index) => {
    return Math.abs(index - visibleIndex.value) <= 1;
};

watch(visibleIndex, () => {
    syncAudioState();
});


const mediaRefs = ref({}); 

const setMediaRef = (el, videoId) => {
    if (el) {
        mediaRefs.value[videoId] = el;
        el.__videoData = allVideos.value.find(v => v.id === videoId);
    }
};

// Audio
const isMuted = ref(true);

const getYoutubeEmbed = (videoId) => {
    const origin = typeof window !== 'undefined' ? window.location.origin : '';
    return `https://www.youtube.com/embed/${videoId}?enablejsapi=1&autoplay=1&mute=1&controls=0&rel=0&loop=1&playlist=${videoId}&playsinline=1&origin=${origin}`;
};

const toggleMute = () => {
    isMuted.value = !isMuted.value;
    syncAudioState(); 
};

const syncAudioState = () => {
    allVideos.value.forEach((video, index) => {
        if (!shouldLoad(index)) return;

        const el = mediaRefs.value[video.id];
        if (!el) return;

        const shouldBeMuted = isMuted.value || index !== visibleIndex.value;

        if (video.platform === 'youtube') {
            const command = shouldBeMuted ? 'mute' : 'unMute';
            if (el.contentWindow) {
                el.contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: command,
                    args: []
                }), '*');
            }
        } else {
            el.muted = shouldBeMuted;
            
            if (shouldBeMuted) {
                el.pause();
            } else {
                el.play().catch(() => {});
            }
        }
    });
};

// Infinite Scroll
const loadMoreVideos = async () => {
    if (isLoadingMore.value || !nextCursor.value) return;

    isLoadingMore.value = true;
    try {
        const response = await axios.get(props.videos.path + '?cursor=' + nextCursor.value);
        allVideos.value.push(...response.data.data);
        nextCursor.value = response.data.next_cursor;
        
        nextTick(() => {
            syncAudioState();
        });

    } catch (error) {
        console.error("Error cargando más videos:", error);
    } finally {
        isLoadingMore.value = false;
    }
};

// --- Comments ---
const activeCommentsVideoId = ref(null);
const commentForm = useForm({ body: '' });

const openComments = (videoId) => { activeCommentsVideoId.value = videoId; };
const closeComments = () => { activeCommentsVideoId.value = null; commentForm.reset(); };

const submitComment = (videoId) => {
    const tempBody = commentForm.body; 

    commentForm.post(route('comments.store', videoId), {
        preserveScroll: true,
        onSuccess: () => {
            const videoIndex = allVideos.value.findIndex(v => v.id === videoId);

            if (videoIndex !== -1) {
                const currentUser = page.props.auth.user;

                const newComment = {
                    id: Date.now(),
                    body: tempBody,
                    user: {
                        id: currentUser.id,
                        name: currentUser.name,
                        profile_photo_url: currentUser.profile_photo_url
                    },
                    created_at: new Date().toISOString()
                };

                if (!allVideos.value[videoIndex].comments) {
                    allVideos.value[videoIndex].comments = [];
                }
                allVideos.value[videoIndex].comments.push(newComment);
            }

            commentForm.reset();
        },
    });
};

/* FOLLOW */
const isFollowing = (userId) => { return page.props.auth.user?.following?.some(u => u.id === userId); };
const toggleFollow = (userId) => {
    router.post(route('follow.toggle', userId), {}, { preserveScroll: true, preserveState: true });
};

/* LIKE */
const toggleLike = (video) => {
    if (video.is_liked) { video.likes_count--; video.is_liked = false; } 
    else { video.likes_count++; video.is_liked = true; }
    router.post(route('videos.like', video.id), {}, { preserveScroll: true, preserveState: true });
};

/* SHARE */
const shareVideo = (video) => {
    const url = window.location.origin + '/player/' + video.user.id;
    if (navigator.share) {
        navigator.share({ title: `Check out ${video.user.name} on ScoutMarket!`, text: video.title, url }).catch(() => {});
    } else {
        navigator.clipboard.writeText(url);
        alert('Link copied to clipboard!');
    }
};

/* VIEWS & OBSERVERS */
const registerView = (video) => {
    if (video.has_viewed) return;
    video.has_viewed = true;
    axios.post(route('videos.view', video.id)).then(() => { video.views_count++; }).catch(() => {});
};

let viewObserver;
let infiniteObserver;

const onIframeLoad = (videoId) => {
    if (!isMuted.value) {
        const el = mediaRefs.value[videoId];
        if (el && el.contentWindow) {
             el.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'unMute', args: [] }), '*');
        }
    }
};

onMounted(() => {
    viewObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const video = entry.target.__videoData;
                if (video) registerView(video);


                const index = Number(entry.target.dataset.index);
                if (!isNaN(index)) {
                    visibleIndex.value = index;
                }
            }
        });
    }, { threshold: 0.5 });

    infiniteObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) loadMoreVideos();
    }, { rootMargin: '400px' });

    if (loadTriggerRef.value) infiniteObserver.observe(loadTriggerRef.value);
});

onBeforeUnmount(() => {
    if (viewObserver) viewObserver.disconnect();
    if (infiniteObserver) infiniteObserver.disconnect();
});


//SAVE
const toggleSave = (video) => {
    if (video.is_saved) {
        video.saves_count--;
        video.is_saved = false;
    } else {
        video.saves_count++;
        video.is_saved = true;
    }

    router.post(route('videos.save', video.id), {}, {
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            if (video.is_saved) {
                video.saves_count--;
                video.is_saved = false;
            } else {
                video.saves_count++;
                video.is_saved = true;
            }
        }
    });
};

</script>

<template>
    <Head title="Video Feed" />

    <AppLayout title="Discover Talent">
        <div class="h-[calc(100vh-4rem)] w-full bg-black flex justify-center overflow-hidden">

            <div class="w-full h-full max-w-md bg-black relative shadow-2xl overflow-y-scroll snap-y snap-mandatory border-x border-white/10">

                <div
                    v-for="(video, index) in allVideos"
                    :key="video.id"
                    :data-index="index" 
                    class="relative w-full h-full snap-start flex items-center justify-center bg-gray-900 border-b border-gray-800"
                >
                    <div @click="toggleMute" class="absolute inset-0 z-10 w-full h-full cursor-pointer flex items-center justify-center">
                        <div v-if="isMuted" class="bg-black/40 p-4 rounded-full backdrop-blur-sm animate-pulse pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                            </svg>
                        </div>
                    </div>

                    <div v-if="video.platform === 'youtube'" class="w-full h-full bg-black">
                        <iframe 
                            v-if="shouldLoad(index)"
                            :ref="(el) => { setMediaRef(el, video.id); if(el && viewObserver) viewObserver.observe(el); }"
                            @load="onIframeLoad(video.id)"
                            class="w-full h-full object-cover pointer-events-none"
                            :src="getYoutubeEmbed(video.external_video_id)" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                            :data-index="index"
                        ></iframe>
                        <img v-else :src="`https://img.youtube.com/vi/${video.external_video_id}/hqdefault.jpg`" class="w-full h-full object-cover opacity-60" />
                    </div>

                    <video
                        v-else
                        :ref="(el) => { setMediaRef(el, video.id); if(el && viewObserver) viewObserver.observe(el); }"
                        class="w-full h-full object-cover"
                        :src="shouldLoad(index) ? video.video_url : ''" 
                        autoplay
                        loop
                        playsinline
                        :muted="isMuted || index !== visibleIndex"  :poster="video.thumbnail_url"
                        :data-index="index"
                        @play="registerView(video)"
                    />
                    
                    <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/95 via-black/40 to-transparent p-4 pb-12 text-white pointer-events-none z-20">
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

                    <div class="absolute bottom-24 right-2 flex flex-col gap-6 items-center text-white z-30 pointer-events-auto">
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

                        <div class="flex flex-col items-center gap-1">
                            <button
                                @click="openComments(video.id)"
                                class="bg-gray-800/40 backdrop-blur-sm p-3 rounded-full hover:bg-gray-700 transition-all shadow-lg"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </button>
                            <span class="text-[11px] font-bold">{{ video.comments?.length || 0 }}</span>
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

                        <div class="flex flex-col items-center gap-1">
                        <button
                            @click="toggleSave(video)"
                            class="p-3 rounded-full transition-all active:scale-75 shadow-lg bg-gray-800/40 backdrop-blur-sm"
                            :class="video.is_saved ? 'text-yellow-400' : 'text-white hover:text-yellow-200'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" :class="video.is_saved ? 'fill-current' : 'fill-none stroke-current stroke-2'" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </button>
                        <span class="text-[11px] font-bold shadow-sm">{{ video.saves_count || 0 }}</span>
                    </div>
                    
                    </div>
                    <Transition name="slide-up">
                        <div v-if="activeCommentsVideoId === video.id" 
                            class="absolute inset-x-0 bottom-0 h-[65%] bg-white dark:bg-gray-900 z-[40] rounded-t-2xl flex flex-col shadow-2xl pointer-events-auto">
                            
                            <div class="p-4 border-b dark:border-gray-800 flex justify-between items-center text-black dark:text-white">
                                <span class="font-bold">{{ video.comments?.length || 0 }} comments</span>
                                <button @click="closeComments" class="text-2xl p-2">&times;</button>
                            </div>

                            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                                <div v-for="comment in video.comments" :key="comment.id" class="flex gap-3 items-start text-left">
                                    <img :src="comment.user.profile_photo_url" class="h-8 w-8 rounded-full object-cover shrink-0" />
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-[11px] font-bold text-gray-500">@{{ comment.user.name }}</span>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 break-words">{{ comment.body }}</p>
                                    </div>
                                </div>
                                <div v-if="!video.comments?.length" class="text-center text-gray-400 py-10 text-sm font-medium">
                                    No comments yet. Start the conversation!
                                </div>
                            </div>

                            <div class="p-4 border-t dark:border-gray-800 bg-gray-50 dark:bg-gray-800 rounded-b-2xl">
                                <div class="flex gap-2">
                                    <input 
                                        v-model="commentForm.body" 
                                        placeholder="Add comment..." 
                                        class="flex-1 bg-white dark:bg-gray-700 border-none rounded-full px-4 text-sm dark:text-white focus:ring-2 focus:ring-green-500"
                                        @keyup.enter="submitComment(video.id)"
                                    />
                                    <button 
                                        @click="submitComment(video.id)" 
                                        :disabled="!commentForm.body || commentForm.processing"
                                        class="text-green-600 font-bold px-2 disabled:opacity-50"
                                    >
                                        Post
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                    

                    <div v-if="activeCommentsVideoId === video.id" 
                        @click="closeComments" 
                        class="absolute inset-0 z-[35] bg-black/20 pointer-events-auto">
                    </div>

                </div>

                <div ref="loadTriggerRef" class="w-full h-20 snap-start flex items-center justify-center">
                    <span v-if="isLoadingMore" class="text-white/50 text-xs animate-pulse">Loading talent...</span>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
::-webkit-scrollbar {
    display: none;
}

.slide-up-enter-active, .slide-up-leave-active {
    transition: transform 0.3s cubic-bezier(0.33, 1, 0.68, 1);
}
.slide-up-enter-from, .slide-up-leave-to {
    transform: translateY(100%);
}
</style>