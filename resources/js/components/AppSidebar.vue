<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, Folder, LayoutGrid, User as UserIcon, GalleryVerticalEnd, Video, Mail } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,

} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import AppLogo from './AppLogo.vue';
import { route } from 'ziggy-js';

interface User {
    id: number;
    role: string;
    name: string;
    email: string;
    profile?: {
        position?: string;
        current_club?: string;
    } | null;
}

const page = usePage();
const user = computed(() => page.props.auth.user as User);
const isPlayer = computed(() => user.value?.role === 'player');

const mainNavItems = computed(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Messages',
        href: route('messages.index'),
        icon: Mail,
    },
    ...(isPlayer.value ? [{
        title: 'My Highlights',
        href: route('player.show', user.value.id),
        icon: Video,
    }] : []),
    {
        title: isPlayer.value ? 'Edit Player Profile' : 'Edit Scout Profile ',
        href: '/player-profile',
        icon: UserIcon,
    },
    {
        title: 'Video Feed',
        href: '/feed',
        icon: GalleryVerticalEnd,
    },
]);


</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>