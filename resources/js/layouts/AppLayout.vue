<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar';

interface BreadcrumbItem {
    title: string;
    href: string;
}

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <SidebarProvider>
        <AppSidebar />

        <SidebarInset>
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />

            <div class="flex-1 p-6">
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>