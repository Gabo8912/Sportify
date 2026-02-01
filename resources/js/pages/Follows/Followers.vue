<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    followers: any[];
}>();
</script>

<template>
    <Head title="Followers" />

    <AppLayout>
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold text-white mb-6">Your Followers</h1>
                
                <div v-if="followers.length > 0" class="grid gap-4">
                    <div v-for="user in followers" :key="user.id"
                        class="bg-neutral-900 border border-neutral-800 p-4 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-neutral-800 flex items-center justify-center font-bold text-neutral-500 border border-neutral-700">
                                {{ user.name.charAt(0) }}
                            </div>
                            <div>
                                <h3 class="text-white font-medium">{{ user.name }}</h3>
                            </div>
                        </div>
                        <Link :href="'/player/' + user.id" class="bg-white text-black px-4 py-2 rounded-md text-sm font-bold">
                            View Profile
                        </Link>
                    </div>
                </div>

                <div v-else class="text-center py-20 bg-neutral-900 rounded-lg border border-neutral-800">
                    <p class="text-neutral-500">No one is following you yet.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>