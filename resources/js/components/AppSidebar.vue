<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { LayoutGrid, User as UserIcon, GalleryVerticalEnd, Video, Mail, ShieldCheck} from 'lucide-vue-next';
import NavUser from '@/components/NavUser.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupLabel,

} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import AppLogo from './AppLogo.vue';
import { route } from 'ziggy-js';

// Definimos la interfaz (igual que la tenías)
interface User {
    id: number;
    role: string;
    name: string;
    email: string;
    profile_photo_url?: string;
    profile?: {
        position?: string;
        current_club?: string;
    } | null;
}

const page = usePage();
const user = computed(() => page.props.auth.user as User | null);
const isPlayer = computed(() => user.value?.role === 'player');
const isAdmin = computed(() => user.value?.role === 'admin');
const following = computed(() => (page.props.auth.user as any)?.following || []);
console.log('Datos de seguidores en Inertia:', following.value);

const mainNavItems = computed(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    ...(isAdmin.value ? [{
        title: 'Admin Panel',
        href: route('admin.dashboard'),
        icon: ShieldCheck,
        isActive: route().current('admin.dashboard')
    }] : []),
    ...(user.value ? [{
        title: 'Messages',
        href: route('messages.index'),
        icon: Mail,
    }] : []),

    ...(user.value ? [
        {
            title: 'Following',
            href: '/following', 
            icon: UserIcon, 
        },
        {
            title: 'Followers',
            href: '/followers', 
            icon: UserIcon,
        }
    ] : []),

    ...(user.value && isPlayer.value ? [{
        title: 'My Highlights',
        href: route('player.show', user.value.id),
        icon: Video,
    }] : []),

    ...(user.value ? [{
        title: 'Edit Profile ',
        href: '/player-profile',
        icon: UserIcon,
    }] : []),

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
                        <Link :href="user ? dashboard() : '/'">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" :following="following" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser v-if="user" :user="user" />
            
            <div v-else class="p-2">
                <Link :href="route('login')" class="flex w-full items-center justify-center rounded-md bg-green-600 py-2 text-sm font-bold text-white">
                    Log In
                </Link>
            </div>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

