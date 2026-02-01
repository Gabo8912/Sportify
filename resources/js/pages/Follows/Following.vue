<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    following: any[];
}>();
</script>

<template>
    <Head title="Following" />

    <AppLayout>
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold text-white mb-6">People you follow</h1>
                
                <div v-if="following.length > 0" class="grid gap-4">
                    <div v-for="user in following" :key="user.id"
                        class="bg-neutral-900 border border-neutral-800 p-4 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-neutral-800 overflow-hidden border border-neutral-700">
                                <img v-if="user.profile?.profile_photo_path" :src="'/storage/' + user.profile.profile_photo_path" class="h-full w-full object-cover" />
                                <div v-else class="h-full w-full flex items-center justify-center text-xl font-bold text-neutral-500">
                                    {{ user.name.charAt(0) }}
                                </div>
                            </div>
                            <div>
                                <h3 class="text-white font-medium">{{ user.name }}</h3>
                                <p class="text-neutral-500 text-sm">@{{ user.name.toLowerCase().replace(/\s+/g, '') }}</p>
                            </div>
                        </div>
                        <Link :href="'/player/' + user.id" class="bg-white text-black px-4 py-2 rounded-md text-sm font-bold hover:bg-neutral-200 transition">
                            View Profile
                        </Link>
                    </div>
                </div>

                <div v-else class="text-center py-20 bg-neutral-900 rounded-lg border border-neutral-800">
                    <p class="text-neutral-500">You are not following anyone yet.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>