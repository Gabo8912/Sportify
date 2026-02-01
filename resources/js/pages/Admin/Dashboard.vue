<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    users: Object,
    videos: Object,
});

const deleteUser = (id) => {
    if (confirm('Are you sure you want to ban this user? This action cannot be undone.')) {
        router.delete(route('admin.users.delete', id), {
            preserveScroll: true,
            onSuccess: () => alert('User banned successfully.')
        });
    }
};

const deleteVideo = (id) => {
    if (confirm('Are you sure you want to remove this video?')) {
        router.delete(route('admin.videos.delete', id), {
            preserveScroll: true,
            onSuccess: () => alert('Video removed.')
        });
    }
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout title="Admin Panel">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🛡️ Admin Control Center
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-indigo-500">
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Users</div>
                        <div class="text-3xl font-black text-gray-800 dark:text-white mt-1">{{ stats.total_users }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-green-500">
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Scouts</div>
                        <div class="text-3xl font-black text-gray-800 dark:text-white mt-1">{{ stats.scouts_count }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-red-500">
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Videos Uploaded</div>
                        <div class="text-3xl font-black text-gray-800 dark:text-white mt-1">{{ stats.total_videos }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-yellow-500">
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Views</div>
                        <div class="text-3xl font-black text-gray-800 dark:text-white mt-1">{{ stats.total_views }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                            <h3 class="font-bold text-gray-700 dark:text-white">👥 Latest Users</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3">User</th>
                                        <th class="px-4 py-3">Role</th>
                                        <th class="px-4 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data" :key="user.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            {{ user.name }}
                                            <div class="text-xs text-gray-500">{{ user.email }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded text-xs font-bold"
                                                :class="{
                                                    'bg-purple-100 text-purple-800': user.role === 'admin',
                                                    'bg-blue-100 text-blue-800': user.role === 'scout',
                                                    'bg-green-100 text-green-800': user.role === 'player'
                                                }">
                                                {{ user.role.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button v-if="user.role !== 'admin'" 
                                                    @click="deleteUser(user.id)" 
                                                    class="text-red-600 hover:text-red-900 font-bold text-xs uppercase hover:underline">
                                                Ban User
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="font-bold text-gray-700 dark:text-white">📹 Recent Videos (Moderation)</h3>
                        </div>
                        <div class="p-4 space-y-4 max-h-[400px] overflow-y-auto">
                            <div v-for="video in videos.data" :key="video.id" class="flex gap-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="w-20 h-24 bg-black rounded flex items-center justify-center shrink-0">
                                    <span class="text-xs text-white">▶️ Video</span>
                                </div>
                                
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 dark:text-white text-sm line-clamp-1">{{ video.title }}</h4>
                                    <p class="text-xs text-gray-500">by {{ video.user ? video.user.name : 'Unknown' }}</p>
                                    <div class="mt-2 text-xs text-gray-400">
                                        {{ video.views_count || 0 }} views
                                    </div>
                                </div>
                                
                                <div class="flex flex-col justify-center">
                                    <button @click="deleteVideo(video.id)" class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded text-xs font-bold transition">
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <div v-if="videos.data.length === 0" class="text-center text-gray-500 text-sm py-4">
                                No videos uploaded yet.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>